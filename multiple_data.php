<?php

/**
 * Processes all JSON files in the specified directory, combining '活动' entries into an array.
 *
 * @param string $directory The directory containing the JSON files.
 */
function processJsonFiles($directory)
{
    // Get all JSON files in the directory
    $jsonFiles = glob($directory . '/*.json');

    foreach ($jsonFiles as $jsonFile) {
        echo "Processing file: $jsonFile\n";

        // Read the JSON content as an array of lines
        $lines = file($jsonFile);

        if ($lines === false) {
            echo "Error reading file: $jsonFile\n";
            continue;
        }

        $insideDaysData = false;
        $braceDepth = 0;
        $currentDay = '';
        $daysDataContent = '';
        $modified = false;
        $newLines = [];
        $activities = [];
        $dayBuffer = '';
        $insideDay = false;

        foreach ($lines as $line) {
            // Trim whitespace from the line
            $trimmedLine = trim($line);

            // Check if we are entering or exiting the 'daysData' array
            if (strpos($trimmedLine, '"daysData"') !== false) {
                $insideDaysData = true;
                $newLines[] = $line;
                continue;
            }

            if ($insideDaysData) {
                // Check for array start and end
                if (strpos($trimmedLine, '[') !== false) {
                    $braceDepth++;
                    $newLines[] = $line;
                    continue;
                }

                if (strpos($trimmedLine, ']') !== false) {
                    $braceDepth--;
                    $insideDaysData = false;
                    $newLines[] = $line;
                    continue;
                }

                // Check for object start
                if (strpos($trimmedLine, '{') !== false) {
                    $braceDepth++;
                    $insideDay = true;
                    $dayBuffer = $line;
                    $activities = [];
                    continue;
                }

                // Check for object end
                if (strpos($trimmedLine, '}') !== false) {
                    $braceDepth--;
                    $insideDay = false;

                    // After collecting all lines for the day, process the day
                    $dayBuffer .= $line;
                    $dayContent = processDay($dayBuffer, $activities);

                    $newLines[] = $dayContent;

                    $modified = true;
                    continue;
                }

                if ($insideDay) {
                    // Collect '活动' entries and other lines
                    if (strpos($trimmedLine, '"活动"') !== false) {
                        // Extract the '活动' value
                        $activityValue = getActivityValue($trimmedLine);
                        $activities[] = $activityValue;
                    } else {
                        // Add line to day buffer
                        $dayBuffer .= $line;
                    }
                } else {
                    // Add line to daysData content
                    $newLines[] = $line;
                }
            } else {
                // Add line to newLines
                $newLines[] = $line;
            }
        }

        // Write the modified content back to the file
        if ($modified) {
            $newContent = implode('', $newLines);
            if (file_put_contents($jsonFile, $newContent) === false) {
                echo "Error writing to file: $jsonFile\n";
            } else {
                echo "File updated successfully: $jsonFile\n";
            }
        } else {
            echo "No changes made to file: $jsonFile\n";
        }
    }
}

/**
 * Processes a day object, combining '活动' entries into an array.
 *
 * @param string $dayBuffer The string content of the day object.
 * @param array $activities The collected activities.
 * @return string The modified day object content.
 */
function processDay($dayBuffer, $activities)
{
    // Remove trailing commas and whitespace
    $dayBuffer = trim($dayBuffer, ", \n\r\t");

    // Build the '活动' array content
    $activityEntries = '';
    if (!empty($activities)) {
        $activityArray = json_encode($activities, JSON_UNESCAPED_UNICODE);
        $activityEntries = "\n    \"活动\": $activityArray";
    }

    // Remove any '活动' entries from dayBuffer
    $dayBuffer = preg_replace('/\s*"活动"\s*:\s*(".*?"|\[.*?\]|\{.*?\})(,?)/us', '', $dayBuffer);

    // Remove any trailing commas after the previous removal
    $dayBuffer = preg_replace('/,\s*}/us', "\n}", $dayBuffer);

    // Insert the '活动' array before the closing brace
    $dayBuffer = rtrim($dayBuffer, "\n\r\t }") . ",$activityEntries\n  }";

    return $dayBuffer;
}

/**
 * Extracts the activity value from a line containing '活动'.
 *
 * @param string $line The line containing '活动'.
 * @return string The activity value.
 */
function getActivityValue($line)
{
    // Match the '活动' line to extract the value
    if (preg_match('/"活动"\s*:\s*(.*?)(,?\s*$)/us', $line, $matches)) {
        $value = trim($matches[1]);
        // Remove surrounding quotes if present
        $value = trim($value, '"');
        return $value;
    }
    return '';
}

// Specify the directory containing the JSON files
$directory = './data/format';

// Run the processing function
processJsonFiles($directory);

?>
