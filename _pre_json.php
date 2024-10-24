<?php

/**
 * Extracts itineraries from the days data.
 *
 * @param array $daysData Array of days in the itinerary from the JSON.
 * @return array Extracted itineraries.
 */
function getItineraries($daysData) {
    $itineraries = [];

    foreach ($daysData as $day) {
        $dayArray = [];
        $desc = '';

        // Map the fields from JSON to the itinerary array
        $dayArray['label'] = $day['天数'] ?? '';
        $dayArray['title'] = $day['城市'] ?? '';
        $dayArray['date'] = ($day['日期'] ?? '') !== '无' ? $day['日期'] : '';
        $dayArray['stay'] = ($day['住宿'] ?? '') !== '无' ? $day['住宿'] : '';

        // Meals
        $breakfast = $day['早餐'] ?? '/';
        $lunch = $day['午餐'] ?? '/';
        $dinner = $day['晚餐'] ?? '/';

        $desc .= "<p><strong>早餐</strong>: $breakfast ";
        $desc .= "<strong>午餐</strong>: $lunch ";
        $desc .= "<strong>晚餐</strong>: $dinner</p>";

        // Activities
        if (!empty($day['活动']) && is_array($day['活动'])) {
            foreach ($day['活动'] as $activity) {
                // Remove the leading '-- ' if present
                $activity = preg_replace('/^--\s*/', '', $activity);
                $desc .= "<p>$activity</p>";
            }
        }

        $dayArray['desc'] = $desc;

        $itineraries[] = $dayArray;
    }

    return $itineraries;
}

/**
 * Extracts post_title and post_excerpt from the trip data.
 *
 * @param array $tripData Trip data array from the JSON.
 * @return array Extracted post_title and post_excerpt.
 */
function getPostLevelData($tripData) {
    $post_title = trim($tripData['题目'] ?? '无主题');
    $post_excerpt = trim($tripData['概要'] ?? $post_title);

    return [$post_title, $post_excerpt];
}
