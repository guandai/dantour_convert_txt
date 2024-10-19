<?php
require('data.php');
// Transform the itineraries
$transformedItineraries = [];

foreach ($itineraries as $day) {
    // Start the description with meals, transportation, and lodging
    $desc = <<<EOT
<p><strong>早餐</strong>: {$day['早餐']} <strong>午餐</strong>: {$day['午餐']} <strong>晚餐</strong>: {$day['晚餐']} </p>
<br/>
<p><strong>交通</strong>: {$day['交通']} <strong>住宿</strong>: {$day['住宿']}</p>
EOT;

if (isset($array['参考航班'])) {
    // Safe to use $array['参考航班']
    $desc .= "<strong>参考航班</strong><br/><p>{$day['参考航班']}</p><br/>"; 
}


    // Append activities dynamically
    $i = 1;
    while (isset($day["活动$i"])) {
        $activityTime = is_array($day["活动$i"]) ? $day["活动$i"][0] : '';
        $activityDesc = is_array($day["活动$i"]) ? $day["活动$i"][1] : $day["活动$i"];
        $desc .= "<strong>活动$i $activityTime</strong><br/><p>$activityDesc</p><br/>";
        $i++;
    }

    // Add the transformed day to the transformed itineraries array
    $transformedItineraries[] = [
        "label" => $day['标题'],
        "title" => $day['城市'],
        "date" => $day['日期'],
        "desc" => $desc
    ];
}

print_r(json_encode($transformedItineraries));	// For demonstration, printing out the transformed data
