<?php

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

    // Split into sections based on "-----------------------------------"
    $days = preg_split('/-{3,}/', $itineraryText);
    $itineraries = [];

    foreach ($days as $day) {
        if (empty(trim($day))) continue;

        // Parse each section of the day
        preg_match_all('/##(.*?)\n(.*?)(?=(\n##|\z))/s', $day, $matches, PREG_SET_ORDER);
        $dayArray = [];
        $desc = '';

        foreach ($matches as $match) {
            $label = trim($match[1]);
            $content = trim($match[2]);

            switch ($label) {
                case '天数':
                    $dayArray['label'] = $content;
                    break;
                case '城市':
                    $dayArray['title'] = $content;
                    break;
                case '日期':
                    if ($content == '无') {
                        $dayArray['date'] = '';
                    } else {
                        $dayArray['date'] = $content;
                    }
                    break;
                case '早餐':
                    $desc .= "<p><strong>早餐</strong>: $content ";
                    break;
                case '午餐':
                    $desc .= "<strong>午餐</strong>: $content ";
                    break;
                case '晚餐':
                    $desc .= "<strong>晚餐</strong>: $content</p>";
                    break;
                default:
                    $desc .= "<strong>$label</strong>: $content</p><br/>";
            }
        }

        if (!empty($desc)) {
            $dayArray['desc'] = $desc;
        }

        $itineraries[] = $dayArray;
    }

    // Serialize the array of itineraries
    $serializedItineraries = serialize($itineraries);

    // Return extracted data
    return [
        'post_title' => $post_title,
        'post_excerpt' => $post_excerpt,
        'serializedItineraries' => $serializedItineraries
    ];
}

/**
 * Displays usage instructions.
 *
 * @param string $scriptName Name of the script.
 */
function display_usage($scriptName) {
    echo "Usage: php $scriptName <file_pattern>\n";
    echo "Example: php $scriptName '*.txt'\n";
    exit(1);
}

// Check if the script is run from the command line
if (php_sapi_name() !== 'cli') {
    die("This script must be run from the command line.\n");
}

// Get the file pattern from the first argument if provided, else default to '*.txt'
$filePattern = $argv[1] ?? '*.txt';

// Optional: Validate the file pattern (basic validation)
if (!is_string($filePattern) || empty($filePattern)) {
    echo "Invalid file pattern provided.\n";
    display_usage($argv[0]);
}

// Directory containing the .txt files
$folderPath = '../format';
// Ensure the folder path does not end with a slash
$folderPath = rtrim($folderPath, '/');

// $templateFilePath should be a complete path if not in the same directory
$templateFilePath = 'template.csv';  // Path to your template.csv file
$newFilePath = 'output_'.$filePattern.'.csv';      // Specify the path for the updated CSV

// Construct the full glob pattern
$globPattern = $folderPath . '/' . $filePattern;

// Scan the folder for all matching files
$txtFiles = glob($globPattern);

if (empty($txtFiles)) {
    die("No files matched the pattern '$filePattern' in the directory: $folderPath\n");
}

// Open the CSV template for reading
if (($templateHandle = fopen($templateFilePath, "r")) !== FALSE) {
    $header = fgetcsv($templateHandle);  // Read the header row

    // Determine the index of the required columns
    $itineraryDataIndex = array_search('wp_travel_trip_itinerary_data', $header);
    $postTitleIndex = array_search('post_title', $header);
    $postExcerptIndex = array_search('post_excerpt', $header);

    if ($itineraryDataIndex === FALSE || $postTitleIndex === FALSE || $postExcerptIndex === FALSE) {
        die("One or more required columns not found in the template CSV.\n");
    }

    // Create a new CSV for writing the output
    if (($outputHandle = fopen($newFilePath, 'w')) !== FALSE) {
        // Write the header to the new CSV file
        fputcsv($outputHandle, $header);

        // Process each .txt file
        foreach ($txtFiles as $txtFile) {
            echo "Processing file: $txtFile\n";

            // Extract data from the .txt file
            $extractedData = convert_text_to_data($txtFile);
            $post_title = $extractedData['post_title'];
            $post_excerpt = $extractedData['post_excerpt'];
            $serializedData = $extractedData['serializedItineraries'];

            // Reset the template data for each row
            fseek($templateHandle, 0);  // Reset template pointer to the start
            fgetcsv($templateHandle);    // Skip header again

            // Read each line of the template CSV file
            while (($templateRow = fgetcsv($templateHandle)) !== FALSE) {
                // Update the necessary columns with extracted data
                $templateRow[$itineraryDataIndex] = $serializedData;
                $templateRow[$postTitleIndex] = $post_title;
                $templateRow[$postExcerptIndex] = $post_excerpt;

                // Write the updated row to the output CSV
                fputcsv($outputHandle, $templateRow);
            }
        }

        // Close the output file
        fclose($outputHandle);
        echo "CSV file has been updated successfully at: $newFilePath\n";
    } else {
        echo "Error opening the file for writing: $newFilePath\n";
    }

    // Close the template file
    fclose($templateHandle);
} else {
    echo "Error opening the template CSV file for reading: $templateFilePath\n";
}
