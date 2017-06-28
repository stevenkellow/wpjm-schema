<?php
/*
*	File to capture job listing info
*
*/

// Get general job information
$title = $post->post_title; // Job title
$permalink = get_the_permalink(); // Job permalink
$description = $post->post_content; // Get the job description
$date = date( 'Y-m-d', strtotime( $post->post_date ) ); // Get the date the job was posted

$app_deadline = get_post_meta($post->ID, '_application_deadline', true); // If the application deadline plugin exists, use set deadline
if( ! $app_deadline ){
	$app_deadline = get_post_meta($post->ID, '_job_expires', true); // If no deadline set, just get the job expiry date
}

// Check if categories are enabled for job listings
if ( get_option( 'job_manager_enable_categories' ) ) {
	$job_category = wpjm_schema_get_the_job_categories( $post->ID ); // Job category formatted as 'industry'
}

// Check if job types are enabled for job listings
if( get_option( 'job_manager_enable_types') ){

	// Get the job types JSON formatted
	$job_type = wpjm_schema_get_the_job_types( $post->ID ); // Job type formatted as 'employmentType'

}

// Get hiring organization details
$company_name = get_the_company_name(); // Company name
$company_url = get_the_company_website(); // Company URL
$company_desc = get_post_meta($post->ID, '_company_tagline', true); // Tagline / description

$twitter_username = get_the_company_twitter(); // Company Twitter account
// Check Twitter is set before using it
if( $twitter_username ){
	$company_twitter = 'https://twitter.com/' . $twitter_username;
}
$image = get_the_post_thumbnail_url( $post->ID, 'full' ); // Company logo

// Get geolocation info
$location = get_post_meta($post->ID, '_job_location', true); // General location

$geolocated = get_post_meta($post->ID, 'geolocated', true); // Check whether the job location was geolocated

// If we've geolocated the job, get more information
if( $geolocated == 1 ){
	$latitude = get_post_meta($post->ID, 'geolocation_lat', true); // Geolocation latitude
	$longitude = get_post_meta($post->ID, 'geolocation_long', true); // Geolocation longitude
	$country =	get_post_meta($post->ID, 'geolocation_country_short', true); // Geolocation country

	$city = get_post_meta( $post->ID, 'geolocation_city', true); // City
	$region = get_post_meta( $post->ID, 'geolocation_state_long', true ); // Region
	$postal_code = get_post_meta( $post->ID, 'geolocation_postcode', true); // Postal or ZIP code
}