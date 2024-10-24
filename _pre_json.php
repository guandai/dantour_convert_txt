<?php

/**
 * Extracts itineraries from the itinerary data.
 *
 * @param array $days Array of days in the itinerary from JSON data.
 * @return array Extracted itineraries.
 */
function getItineraries($days) {
    $itineraries = [];

    foreach ($days as $day) {
        if (empty($day)) continue;

        $dayArray = [];
        $desc = '';

        // Map fields from JSON data to itinerary array
        $dayArray['label'] = $day['天数'] ?? '';
        $dayArray['title'] = $day['城市'] ?? '';
        $dayArray['date'] = ($day['日期'] ?? '') == '无' ? '' : $day['日期'];
        $dayArray['stay'] = ($day['住宿'] ?? '') == '无' ? '' : $day['住宿'];
        $dayArray['breakfast'] = ($day['早餐'] ?? '') == '无' ? '' : $day['早餐'];
        $dayArray['lunch'] = ($day['午餐'] ?? '') == '无' ? '' : $day['午餐'];
        $dayArray['dinner'] = ($day['晚餐'] ?? '') == '无' ? '' : $day['晚餐'];
        $dayArray['traffic'] = ($day['交通'] ?? '') == '无' ? '' : $day['交通'];

        // Build description
        $desc .= "<p><strong>早餐</strong>: {$dayArray['breakfast']} ";
        $desc .= "<strong>午餐</strong>: {$dayArray['lunch']} ";
        $desc .= "<strong>晚餐</strong>: {$dayArray['dinner']}</p>";

        foreach(['活动','提示','参考航班'] as $name) {
            if (!empty($day[$name]) && is_array($day[$name])) {
                foreach ($day[$name] as $data) {
                    // Remove '-- ' at the beginning
                    $data = preg_replace('/^--\s*/', '', $data);
                    $desc .= "<strong>$name</strong>: <p>$data</p>";
                }
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
 * Extracts post_title and post_excerpt from the trip data.
 *
 * @param array $tripData Trip data to extract data from.
 * @return array Extracted post_title and post_excerpt.
 */
function getPostLevelData($tripData) {
    $post_title = trim($tripData['题目'] ?? '无主题');
    $post_excerpt = trim($tripData['概要'] ?? $post_title);
    return [$post_title, $post_excerpt];
}
