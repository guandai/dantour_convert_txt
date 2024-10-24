<?php
/**
 * Mock WP_Term class for serialization purposes outside WordPress.
 */
class WP_Term
{
	public $term_id;
	public $name;
	public $slug;
	public $term_group;
	public $term_taxonomy_id;
	public $taxonomy;
	public $description;
	public $parent;
	public $count;
	public $filter;
}

/**
 * Creates a mock WP_Term object with the given properties.
 *
 * @param string $name The name of the term.
 * @param string $taxonomy The taxonomy to which the term belongs.
 * @param int    $term_id Optional. The term ID. Default is 0.
 * @param int    $term_taxonomy_id Optional. The term taxonomy ID. Default is 0.
 *
 * @return WP_Term The created WP_Term object.
 */
function create_wp_term($name, $taxonomy, $term_id = 0, $term_taxonomy_id = 0)
{
	$term = new WP_Term();

	// Assign properties
	$term->term_id = $term_id;
	$term->name = $name;
	$term->slug = sanitize_title($name);
	$term->term_group = 0;
	$term->term_taxonomy_id = $term_taxonomy_id;
	$term->taxonomy = $taxonomy;
	$term->description = '';
	$term->parent = 0;
	$term->count = 1;
	$term->filter = 'raw';

	return $term;
}

/**
 * Sanitizes a string to create a URL-friendly slug.
 *
 * @param string $string The string to sanitize.
 * @return string The sanitized slug.
 */
function sanitize_title($string)
{
	// Convert to lowercase
	$slug = strtolower($string);
	// Replace non-alphanumeric characters with hyphens
	$slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
	// Trim hyphens from both ends
	$slug = trim($slug, '-');
	return $slug;
}

// Define the taxonomy arrays
$taxonomies = [
	'itinerary_types' => [],
	'travel_locations' => [],
	'activity' => [],
	'travel_keywords' => ['fild', 'name', 'name.txt'],
];

// Initialize the associative array with specified taxonomies

function getTaxonomies($taxonomies) {
	$serialized_data = [
			'itinerary_pricing_category' => false,
	];

	// Iterate over each taxonomy and populate the serialized data
	foreach ($taxonomies as $taxonomy => $terms) {
			if (count($terms) > 0) {  // Check if there are terms in the taxonomy
					$serialized_data[$taxonomy] = []; // Initialize as empty array

					foreach ($terms as $term_name) {
							$term = create_wp_term($term_name, $taxonomy);
							$serialized_data[$taxonomy][] = $term;
					}
			}
	}

	// Serialize the associative array
	$serialized_string = serialize($serialized_data);

	// Escape double quotes by doubling them
	$escaped_serialized = str_replace('"', '""', $serialized_string);

	// Output the serialized string enclosed in double quotes
	$result = '"' . $escaped_serialized . '"';
	// echo $result;
	return $result;
}
