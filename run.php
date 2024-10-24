<?php

include '_gen-wp-term.php';
include '_pre.php';
define('DEFAULT_FILE_PATTERN', '*.txt');
define('DEFAULT_TEMPLATE', 'template.csv');


// Check if the script is run from the command line
if (php_sapi_name() !== 'cli') {
    die("This script must be run from the command line.\n");
}

/**
 *  
 * @param array $header Header row of the CSV file.
 * @param string $templateHandle Handle to the template CSV file.
 * @return array Index of the required columns in the CSV file.
 */
function getIndex ($header , $templateHandle) {
    // Determine the index of the required columns
    $itineraryDataIndex = array_search('wp_travel_trip_itinerary_data', $header);
    $postTitleIndex = array_search('post_title', $header);
    $postExcerptIndex = array_search('post_excerpt', $header);
    $postTaxonomiesIndex = array_search('taxonomies', $header);
    if ($itineraryDataIndex === FALSE || $postTitleIndex === FALSE || $postExcerptIndex === FALSE) {
        die("One or more required columns not found in the template CSV.\n");
    }

    return [$itineraryDataIndex, $postTitleIndex, $postExcerptIndex, $postTaxonomiesIndex];
}

/**
 * Extracts post_title and post_excerpt from the itinerary text.
 *
 * @param string $itineraryText Itinerary text to extract data from.
 * @return array Extracted post_title and post_excerpt.
 */
function getPostLevelData(&$itineraryText) {
    // Extract post_title (概述中的标题)
    $title_pattern = '/##概述\s*\n(.*)\n/';
    preg_match($title_pattern, $itineraryText, $overviewMatches);

    if (isset($overviewMatches[1])) {
        $post_title = trim($overviewMatches[1]);
        $itineraryText = preg_replace($title_pattern, '', $itineraryText);
    } else {
        $post_title = 'Unknown Title';  // Default if no match
    }

    // Extract post_excerpt (摘要)
    $title_pattern = '/##摘要\s*\n(.*)\n/';
    preg_match($title_pattern , $itineraryText, $summaryMatches);
    if (isset($summaryMatches[1])) {
        $post_excerpt = trim($summaryMatches[1]);
        $itineraryText = preg_replace($title_pattern , '', $itineraryText);
    } else {
        $post_excerpt = $post_title;  // Default if no match
    }

    
    return [$post_title, $post_excerpt, $itineraryText];
}



/**
 * Converts itinerary text files to serialized data.
 *
 * @param string $filePath Path to the itinerary text file.
 * @return array Extracted data including post_title, post_excerpt, and serialized itineraries.
 */
function convert_text_to_data($filePath) {
    // Check if the file exists
    if (!file_exists($filePath)) {
        die("File not found: $filePath\n");
    }

    // Read the contents of the file
    $itineraryText = file_get_contents($filePath);
    [$post_title, $post_excerpt, $itineraryText] = getPostLevelData($itineraryText);

    // Split into sections based on "-----------------------------------"
    $days = preg_split('/-{3,}/', $itineraryText);

    $taxonomies = [
        'itinerary_types' => [],
        'travel_locations' => [],
        'activity' => ['coffee', 'shopping'],
        'travel_keywords' => [$file_name = pathinfo($filePath, PATHINFO_BASENAME)],
    ];

    // Serialize the array of itineraries
    $serializedItineraries = serialize(getItineraries($days));
    $serializedTaxonomies = getTaxonomies($taxonomies);
    return [$serializedItineraries, $serializedTaxonomies, $post_title, $post_excerpt];
}

/**
 * Displays usage instructions.
 *
 * @param string $scriptName Name of the script.
 */
function display_usage($scriptName) {
    echo "Usage: php $scriptName <file_pattern>\n";
    echo "Example: php $scriptName " . DEFAULT_FILE_PATTERN . "\n";
    exit(1);
}


/**
 * Get the new file path, template handle, and list of .txt files.
 *
 * @param array $arg Command line arguments.
 * @return array New file path, template handle, and list of .txt files.
 */
function getNewFilePath ($arg) {
    // Directory containing the .txt files
    $folderPath = './data/format';
    // Ensure the folder path does not end with a slash
    $folderPath = rtrim($folderPath, '/');


    // Get the file pattern from the first argument if provided, else default to DEFAULT_FILE_PATTERN
    $filePattern = $arg[1] ?? DEFAULT_FILE_PATTERN;

    // Optional: Validate the file pattern (basic validation)
    if (!is_string($filePattern) || empty($filePattern)) {
        echo "Invalid file pattern provided.\n";
        display_usage($arg[0]);
    }

    
    $fileSuffix = $filePattern == DEFAULT_FILE_PATTERN ? '' : '_'. $filePattern;
    $newFilePath = 'output'. $fileSuffix .'.csv';      // Specify the path for the updated CSV

    // Construct the full glob pattern
    $globPattern = $folderPath . '/' . $filePattern;
   
    // Open the CSV template for reading
    if (($templateHandle = fopen(DEFAULT_TEMPLATE, "r")) == FALSE) {
        die("Error opening the template CSV file for reading: {constant('DEFAULT_TEMPLATE')} \n");
    }

    // Scan the folder for all matching files
    $txtFiles = glob($globPattern);
    if (empty($txtFiles)) {
        die("No files matched the pattern '$filePattern' in the directory: $folderPath\n");
    }

    return [$newFilePath, $templateHandle, $txtFiles];
}


/**
 * Get the output handle for writing the updated CSV.
 *
 * @param string $newFilePath Path to the new CSV file.
 * @param array $header Header row of the CSV file.
 * @return resource Handle to the output CSV file.
 */
function getOutputHandle ($newFilePath, $header) {
    // Create a new CSV for writing the output
    if (($outputHandle = fopen($newFilePath, 'w')) == FALSE) {
        die("Error opening the file for writing: $newFilePath\n");
    }

    // Write the header to the new CSV file
    fputcsv($outputHandle, $header);

    return $outputHandle;
}


/**
 * Write the updated data to the output CSV.
 *
 * @param resource $templateHandle Handle to the template CSV file.
 * @param resource $outputHandle Handle to the output CSV file.
 * @param array $txtFiles List of .txt files to process.
 * @param array $header Header row of the CSV file.
 */
function writeToOutput ($templateHandle, $outputHandle, $txtFiles, $header) {
    [$itineraryDataIndex, $postTitleIndex, $postExcerptIndex, $postTaxonomiesIndex] = getIndex($header , $templateHandle);

    // Process each .txt file
    foreach ($txtFiles as $txtFile) {
        echo "Processing file: $txtFile\n";

        // Extract data from the .txt file
        [$serializedItineraries, $serializedTaxonomies, $post_title, $post_excerpt] = convert_text_to_data($txtFile);

        // Reset the template data for each row
        fseek($templateHandle, 0);  // Reset template pointer to the start
        fgetcsv($templateHandle);    // Skip header again

        // Read each line of the template CSV file
        while (($templateRow = fgetcsv($templateHandle)) !== FALSE) {
            // Update the necessary columns with extracted data
            $templateRow[$itineraryDataIndex] = $serializedItineraries;
            $templateRow[$postTitleIndex] = $post_title;
            $templateRow[$postExcerptIndex] = $post_excerpt;
            $templateRow[$postTaxonomiesIndex] = $serializedTaxonomies;

            // Write the updated row to the output CSV
            fputcsv($outputHandle, $templateRow);
        }
    }
}




[$newFilePath, $templateHandle, $txtFiles]  = getNewFilePath($argv);
$header = fgetcsv($templateHandle);  // Read the header row
$outputHandle = getOutputHandle($newFilePath, $header);

// Write the updated data to the output CSV
writeToOutput ($templateHandle, $outputHandle, $txtFiles, $header);

// Close the output file
fclose($outputHandle);
// Close the template file
fclose($templateHandle);

echo "CSV file has been updated successfully at: $newFilePath\n";
