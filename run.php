<?php

include '_gen-wp-term.php';
include '_pre_json.php'; // Contains convert_json_to_data function
include '_pre_txt.php';  // Contains convert_txt_to_data function

// Parse command-line arguments
$options = getopt('', ['format:']);

// Set the format, default to 'json' if not provided
$format = $options['format'] ?? 'json';

// Validate the format
$allowedFormats = ['json', 'txt'];
if (!in_array($format, $allowedFormats)) {
    die("Invalid format specified. Allowed formats are 'json' and 'txt'.\n");
}

// Define constants based on the format
define('DEFAULT_EXTENSION', $format);
define('DEFAULT_FILE_PATTERN', '*.' . DEFAULT_EXTENSION);
define('DEFAULT_TEMPLATE', 'template.csv');

// Check if the script is run from the command line
if (php_sapi_name() !== 'cli') {
    die("This script must be run from the command line.\n");
}

/**
 * Displays usage instructions.
 *
 * @param string $scriptName Name of the script.
 */
function display_usage($scriptName) {
    echo "Usage: php $scriptName [--format=json|txt] [file_pattern]\n";
    echo "Example: php $scriptName --format=json '*.json'\n";
    echo "Default format is 'json' if not specified.\n";
    exit(1);
}

/**
 * Get the index of the required columns in the CSV file.
 *
 * @param array $header Header row of the CSV file.
 * @param resource $templateHandle Handle to the template CSV file.
 * @return array Indexes of the required columns.
 */
function getIndex($header, $templateHandle) {
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
 * Get the new file path, template handle, and list of files.
 *
 * @param array $arg Command line arguments.
 * @return array New file path, template handle, and list of files.
 */
function getNewFilePath($arg) {
    // Directory containing the files
    $folderPath = './data/' . DEFAULT_EXTENSION;
    // Ensure the folder path does not end with a slash
    $folderPath = rtrim($folderPath, '/');

    // Remove script name from arguments
    array_shift($arg);

    // Remove '--format' option and its value from $arg
    foreach ($arg as $key => $value) {
        if (strpos($value, '--format=') === 0) {
            unset($arg[$key]);
        }
    }
    $arg = array_values($arg); // Re-index the array

    // Get the file pattern from the first argument if provided, else default to DEFAULT_FILE_PATTERN
    $filePattern = $arg[0] ?? DEFAULT_FILE_PATTERN;

    // Optional: Validate the file pattern (basic validation)
    if (!is_string($filePattern) || empty($filePattern)) {
        echo "Invalid file pattern provided.\n";
        display_usage($arg[0]);
    }

    $fileSuffix = $filePattern == DEFAULT_FILE_PATTERN ? '' : '_' . $filePattern;
    $newFilePath = 'output' . $fileSuffix . '.csv'; // Specify the path for the updated CSV

    // Construct the full glob pattern
    $globPattern = $folderPath . '/' . $filePattern;

    // Open the CSV template for reading
    if (($templateHandle = fopen(DEFAULT_TEMPLATE, "r")) == FALSE) {
        die("Error opening the template CSV file for reading: " . DEFAULT_TEMPLATE . "\n");
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
function getOutputHandle($newFilePath, $header) {
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
 * @param array $txtFiles List of files to process.
 * @param array $header Header row of the CSV file.
 */
function writeToOutput($templateHandle, $outputHandle, $txtFiles, $header) {
    [$itineraryDataIndex, $postTitleIndex, $postExcerptIndex, $postTaxonomiesIndex] = getIndex($header, $templateHandle);

    // Process each file
    foreach ($txtFiles as $txtFile) {
        echo "\n\n";
        echo "Processing file: $txtFile\n";

        // Use the converter function based on the format
        $convertFnName = 'convert_' . DEFAULT_EXTENSION . '_to_data';

        // Check if the function exists
        if (!function_exists($convertFnName)) {
            die("Conversion function '$convertFnName' does not exist.\n");
        }

        // Extract data from the file
        [$serializedItineraries, $serializedTaxonomies, $post_title, $post_excerpt] = $convertFnName($txtFile);

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

// Main script execution
[$newFilePath, $templateHandle, $txtFiles] = getNewFilePath($argv);
$header = fgetcsv($templateHandle);  // Read the header row
$outputHandle = getOutputHandle($newFilePath, $header);

// Write the updated data to the output CSV
writeToOutput($templateHandle, $outputHandle, $txtFiles, $header);

// Close the output file
fclose($outputHandle);
// Close the template file
fclose($templateHandle);

echo "CSV file has been updated successfully at: $newFilePath\n";
