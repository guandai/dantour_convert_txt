<?php

/**
 * Processes all JSON files in the specified directory, renaming all '活动' keys.
 *
 * @param string $directory The directory containing the JSON files.
 */
function processJsonFiles($directory) {
    // Get all JSON files in the directory
    $jsonFiles = glob($directory . '/*.json');

    foreach ($jsonFiles as $jsonFile) {
        echo "Processing file: $jsonFile\n";

        // Read the contents of the JSON file
        $jsonContent = file_get_contents($jsonFile);
        // Since JSON cannot have duplicate keys, we'll use regex to handle possible duplicates
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Handle invalid JSON due to duplicate keys
            echo "JSON parse error in file: $jsonFile. Attempting to fix duplicate keys.\n";

            // Use regex to find duplicate '活动' keys
            preg_match_all('/"活动"\s*:\s*(".*?")(,|\s*\})/u', $jsonContent, $matches, PREG_OFFSET_CAPTURE);

            if (count($matches[0]) > 0) {
                $activities = [];
                $positions = [];
                // Collect all '活动' values and their positions
                foreach ($matches[0] as $index => $match) {
                    $activities[] = json_decode($matches[1][$index][0]);
                    $positions[] = $match[1];
                }

                // Remove all '活动' entries from the JSON content
                // We need to do this in reverse order to not mess up positions
                for ($i = count($positions) - 1; $i >= 0; $i--) {
                    $startPos = $positions[$i];
                    $length = strlen($matches[0][$i][0]);
                    $jsonContent = substr_replace($jsonContent, '', $startPos, $length);
                }

                // Decode the modified JSON content
                $data = json_decode($jsonContent, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    echo "Still unable to parse JSON in file: $jsonFile\n";
                    continue;
                }

                // Now, add the activities back with new keys
                $activityCount = 1;
                foreach ($activities as $activity) {
                    $data['daysData'][0]['活动' . $activityCount] = $activity;
                    $activityCount++;
                }

                $modified = true;
            } else {
                echo "Unable to fix JSON in file: $jsonFile\n";
                continue;
            }
        } else {
            $modified = false;

            // Process each day in daysData
            if (isset($data['daysData']) && is_array($data['daysData'])) {
                foreach ($data['daysData'] as &$day) {
                    $activities = [];
                    // Collect all keys that are '活动'
                    foreach ($day as $key => $value) {
                        if ($key === '活动') {
                            $activities[] = $value;
                            unset($day[$key]);
                            $modified = true;
                        }
                    }

                    // If '活动' is an array, split it into individual activities
                    if (isset($day['活动']) && is_array($day['活动'])) {
                        foreach ($day['活动'] as $activity) {
                            $activities[] = $activity;
                        }
                        unset($day['活动']);
                        $modified = true;
                    }

                    // Rename all activities to '活动1', '活动2', etc.
                    if (!empty($activities)) {
                        $activityCount = 1;
                        foreach ($activities as $activity) {
                            $day['活动' . $activityCount] = $activity;
                            $activityCount++;
                        }
                    }
                }
                unset($day); // Unset reference to avoid unexpected behavior
            } else {
                echo "No 'daysData' found or it's not an array in file: $jsonFile\n";
                continue;
            }
        }

        // Save the modified data back to the JSON file
        if ($modified) {
            // Reorder the keys to maintain the original order
            $newJsonContent = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            if (file_put_contents($jsonFile, $newJsonContent) === false) {
                echo "Error writing to file: $jsonFile\n";
            } else {
                echo "File updated successfully: $jsonFile\n";
            }
        } else {
            echo "No changes made to file: $jsonFile\n";
        }
    }
}

// Specify the directory containing the JSON files
$directory = './data/format';

// Run the processing function
processJsonFiles($directory);

?>
