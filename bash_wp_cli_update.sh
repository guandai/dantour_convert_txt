# wp  cli  example 
; a:2:{
;	i:0;a:5:{s:5:"label";s:5:"Day01";s:5:"title";s:5:"Place";s:4:"date";s:7:"Invalid";s:4:"time";s:0:"";s:4:"desc";s:7:"Content";}
;	i:1;a:5:{s:5:"label";s:5:"Day02";s:5:"title";s:5:"Title";s:4:"date";s:7:"Invalid";s:4:"time";s:0:"";s:4:"desc";s:7:"Content";}
; }

; array (
;   array (
;     'label' => 'Day01',
;     'title' => '国内/奥斯陆',
;     'date' => 'Invalid date',
;     'time' => '',
;     'desc' => 'aa',
;   ),
; )

; wp post meta update 2970 wp_travel_trip_itinerary_data '[{"label":"Day01","title":"国内/奥斯陆","date":"Invalid date","time":"","desc":"aa"}]'  --format=json
; wp post meta get 2970 wp_travel_trip_itinerary_data  --format==json
; [{"label":"Day01","title":"\u56fd\u5185\/\u5965\u65af\u9646","date":"Invalid date","time":"","desc":"aa"}]


post_ids=$(wp post list --post_type=itineraries --fields=ID)
for i in $post_ids; do
		echo $(wp post meta get $i _thumbnail_id --format=json)
    existing_data=$(wp post meta get $i wp_travel_trip_itinerary_data --format=json)
		; modified_data=$(echo "$existing_data" | jq 'map(.date = "")')
done

