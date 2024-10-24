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



/**
 * Extracts post_title and post_excerpt from the itinerary text.
 *
 * @param string $itineraryText Itinerary text to extract data from.
 * @return array Extracted post_title and post_excerpt.
 */
function getPostLevelData(&$itineraryText) {
	// Extract post_title
	$title_pattern = '/##题目\s*\n(.*)\n/';
	preg_match($title_pattern, $itineraryText, $overviewMatches);

	if (isset($overviewMatches[1])) {
			$post_title = trim($overviewMatches[1]);
			$itineraryText = preg_replace($title_pattern, '', $itineraryText);
	} else {
			$post_title = '无主题';  // Default if no match
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
