<?php

/**
 * Extracts itineraries from the itinerary text.
 *
 * @param array $days Array of days in the itinerary.
 * @return array Extracted itineraries.
 */
function getItineraries($days) {
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
									$dayArray['date'] = ($content == '无') ? '' : $content;
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

	return $itineraries;
}
