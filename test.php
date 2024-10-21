<?php
/**
 * Script to format a serialized PHP string by adding indentation and line breaks.
 */

/**
 * Formats a serialized string with indentation and line breaks.
 *
 * @param string $serialized The serialized string to format.
 * @return string The formatted serialized string.
 */
function format_serialized_string($serialized) {
    // Define a placeholder for escaped double quotes to prevent confusion during formatting
    $placeholder = '__DOUBLE_QUOTE__';
    $serialized = str_replace('""', $placeholder, $serialized);

    $formatted = '';
    $indentLevel = 0;
    $length = strlen($serialized);

    for ($i = 0; $i < $length; $i++) {
        $char = $serialized[$i];

        switch ($char) {
            case '{':
                $formatted .= "{" . PHP_EOL;
                $indentLevel++;
                $formatted .= str_repeat("\t", $indentLevel);
                break;

            case '}':
                $formatted .= ";" . PHP_EOL;
                $indentLevel--;
                $formatted .= str_repeat("\t", $indentLevel) . "}" . PHP_EOL;
                // Add indentation after closing brace if not the end
                if ($i + 1 < $length && $serialized[$i + 1] !== '}') {
                    $formatted .= str_repeat("\t", $indentLevel);
                }
                break;

            case ';':
                $formatted .= ";" . PHP_EOL;
                // Add indentation after semicolon if next character is not a closing brace
                if ($i + 1 < $length && $serialized[$i + 1] !== '}') {
                    $formatted .= str_repeat("\t", $indentLevel);
                }
                break;

            case ':':
                // Append colon without adding anything
                $formatted .= ":";
                break;

            default:
                $formatted .= $char;
                break;
        }
    }

    // Restore the escaped double quotes
    $formatted = str_replace($placeholder, '""', $formatted);

    // Enclose the formatted string in double quotes and escape them as needed
    $formatted = '"' . $formatted . '"';

    return $formatted;
}

// Your serialized string
$serialized_input = '"a:5:{s:26:""itinerary_pricing_category"";b:0;s:15:""itinerary_types"";a:4:{i:0;O:7:""WP_Term"":10:{s:7:""term_id"";i:0;s:4:""name"";s:7:""gloable"";s:4:""slug"";s:7:""gloable"";s:10:""term_group"";i:0;s:16:""term_taxonomy_id"";i:0;s:8:""taxonomy"";s:15:""itinerary_types"";s:11:""description"";s:0:"""";s:6:""parent"";i:0;s:5:""count"";i:1;s:6:""filter"";s:3:""raw"";}i:1;O:7:""WP_Term"":10:{s:7:""term_id"";i:0;s:4:""name"";s:4:""load"";s:4:""slug"";s:4:""load"";s:10:""term_group"";i:0;s:16:""term_taxonomy_id"";i:0;s:8:""taxonomy"";s:15:""itinerary_types"";s:11:""description"";s:0:"""";s:6:""parent"";i:0;s:5:""count"";i:1;s:6:""filter"";s:3:""raw"";}i:2;O:7:""WP_Term"":10:{s:7:""term_id"";i:0;s:4:""name"";s:5:""local"";s:4:""slug"";s:5:""local"";s:10:""term_group"";i:0;s:16:""term_taxonomy_id"";i:0;s:8:""taxonomy"";s:15:""itinerary_types"";s:11:""description"";s:0:"""";s:6:""parent"";i:0;s:5:""count"";i:1;s:6:""filter"";s:3:""raw"";}i:3;O:7:""WP_Term"":10:{s:7:""term_id"";i:0;s:4:""name"";s:3:""usa"";s:4:""slug"";s:3:""usa"";s:10:""term_group"";i:0;s:16:""term_taxonomy_id"";i:0;s:8:""taxonomy"";s:15:""itinerary_types"";s:11:""description"";s:0:"""";s:6:""parent"";i:0;s:5:""count"";i:1;s:6:""filter"";s:3:""raw"";}}s:16:""travel_locations"";a:2:{i:0;O:7:""WP_Term"":10:{s:7:""term_id"";i:0;s:4:""name"";s:7:""beijing"";s:4:""slug"";s:7:""beijing"";s:10:""term_group"";i:0;s:16:""term_taxonomy_id"";i:0;s:8:""taxonomy"";s:16:""travel_locations"";s:11:""description"";s:0:"""";s:6:""parent"";i:0;s:5:""count"";i:1;s:6:""filter"";s:3:""raw"";}i:1;O:7:""WP_Term"":10:{s:7:""term_id"";i:0;s:4:""name"";s:3:""cph"";s:4:""slug"";s:3:""cph"";s:10:""term_group"";i:0;s:16:""term_taxonomy_id"";i:0;s:8:""taxonomy"";s:16:""travel_locations"";s:11:""description"";s:0:"""";s:6:""parent"";i:0;s:5:""count"";i:1;s:6:""filter"";s:3:""raw"";}}s:8:""activity"";a:3:{i:0;O:7:""WP_Term"":10:{s:7:""term_id"";i:0;s:4:""name"";s:6:""coffee"";s:4:""slug"";s:6:""coffee"";s:10:""term_group"";i:0;s:16:""term_taxonomy_id"";i:0;s:8:""taxonomy"";s:8:""activity"";s:11:""description"";s:0:"""";s:6:""parent"";i:0;s:5:""count"";i:1;s:6:""filter"";s:3:""raw"";}i:1;O:7:""WP_Term"":10:{s:7:""term_id"";i:0;s:4:""name"";s:3:""ggg"";s:4:""slug"";s:3:""ggg"";s:10:""term_group"";i:0;s:16:""term_taxonomy_id"";i:0;s:8:""taxonomy"";s:8:""activity"";s:11:""description"";s:0:"""";s:6:""parent"";i:0;s:5:""count"";i:1;s:6:""filter"";s:3:""raw"";}i:2;O:7:""WP_Term"":10:{s:7:""term_id"";i:0;s:4:""name"";s:8:""shopping"";s:4:""slug"";s:8:""shopping"";s:10:""term_group"";i:0;s:16:""term_taxonomy_id"";i:0;s:8:""taxonomy"";s:8:""activity"";s:11:""description"";s:0:"""";s:6:""parent"";i:0;s:5:""count"";i:1;s:6:""filter"";s:3:""raw"";}}s:15:""travel_keywords"";a:3:{i:0;O:7:""WP_Term"":10:{s:7:""term_id"";i:0;s:4:""name"";s:4:""fild"";s:4:""slug"";s:4:""fild"";s:10:""term_group"";i:0;s:16:""term_taxonomy_id"";i:0;s:8:""taxonomy"";s:15:""travel_keywords"";s:11:""description"";s:0:"""";s:6:""parent"";i:0;s:5:""count"";i:1;s:6:""filter"";s:3:""raw"";}i:1;O:7:""WP_Term"":10:{s:7:""term_id"";i:0;s:4:""name"";s:4:""name"";s:4:""slug"";s:4:""name"";s:10:""term_group"";i:0;s:16:""term_taxonomy_id"";i:0;s:8:""taxonomy"";s:15:""travel_keywords"";s:11:""description"";s:0:"""";s:6:""parent"";i:0;s:5:""count"";i:1;s:6:""filter"";s:3:""raw"";}i:2;O:7:""WP_Term"":10:{s:7:""term_id"";i:0;s:4:""name"";s:8:""name.txt"";s:4:""slug"";s:8:""name-txt"";s:10:""term_group"";i:0;s:16:""term_taxonomy_id"";i:0;s:8:""taxonomy"";s:15:""travel_keywords"";s:11:""description"";s:0:"""";s:6:""parent"";i:0;s:5:""count"";i:1;s:6:""filter"";s:3:""raw"";}}}"';


// Remove the outer double quotes
$serialized_input = trim($serialized_input, '"');

// Format the serialized string
$formatted_output = format_serialized_string($serialized_input);

// Escape double quotes by doubling them
$formatted_output = '"' . $formatted_output . '"';

// Output the formatted string within <pre> tags for readability in a browser
echo "<pre>" . htmlspecialchars($formatted_output) . "</pre>";

// If you want to see the formatted string in the command line, uncomment the following line:
// echo $formatted_output;
?>
