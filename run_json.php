<?php

include '_gen-wp-term.php';
include '_pre_json.php';
define('DEFAULT_FILE_PATTERN', '*.json');
define('DEFAULT_TEMPLATE', 'template.csv');

// 其他代码保持不变...

function convert_json_to_data($filePath) {
    // 与上面提供的代码相同
}

// 修改writeToOutput函数中的函数调用
function writeToOutput ($templateHandle, $outputHandle, $txtFiles, $header) {
    [$itineraryDataIndex, $postTitleIndex, $postExcerptIndex, $postTaxonomiesIndex] = getIndex($header , $templateHandle);

    // Process each .json file
    foreach ($txtFiles as $txtFile) {
        echo "Processing file: $txtFile\n";

        // Extract data from the .json file
        [$serializedItineraries, $serializedTaxonomies, $post_title, $post_excerpt] = convert_json_to_data($txtFile);

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

// 主流程保持不变，但要确保调用的函数是新的JSON处理函数

?>
