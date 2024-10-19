<?php


function convert_text_to_data() {
	// Path to the data file
	$filePath = 'data.txt';

	// Check if the file exists
	if (!file_exists($filePath)) {
			die("File not found");
	}

	// Read the contents of the file
	$itineraryText = file_get_contents($filePath);

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
							case '标题':
									$dayArray['label'] = $content;
									break;
							case '城市':
									$dayArray['title'] = $content;
									break;
							case '日期':
									$dayArray['date'] = $content;
									break;
							case '早餐':
									$desc .= "<p><strong>早餐</strong> $content,";
									break;
							case '午餐':
									$desc .= "<strong>午餐</strong> $content,";
									break;
							case '晚餐':
									$desc .= "<strong>晚餐</strong> $content</p>";
									break;
							case '交通':
									$desc .= "<p><strong>交通</strong> $content";
									break;
							case '住宿':
									$desc .= "<strong>住宿</strong>: $content</p><br/>";
									break;
							default:
									if (str_starts_with($label, '活动')) {
											$activityParts = explode(' ', $label);
											$activityLabel = array_shift($activityParts);
											$time = implode(' ', $activityParts);
											$time = $time ? $time . ': ' : '';
											$desc .= "<strong>$activityLabel $time</strong><br/><p>$content</p><br/>";
									} else {
											$desc .= "<strong>$label</strong><br/><p>$content</p><br/>";
									}
					}
			}

			if (!empty($desc)) {
					$dayArray['desc'] = $desc;
			}

			$itineraries[] = $dayArray;
	}

	// Serialize the array of itineraries
	$serializedItineraries = serialize($itineraries);

	// Store or use the serialized string as needed
	// For example, you could write it to a file or just store it in a variable
	// file_put_contents('serialized_itineraries.txt', $serializedItineraries);

	// Uncomment to test output of the serialized data
	return $serializedItineraries;
}


