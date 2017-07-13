<?php
/*
*	Display schema for an individual job listing
*
*/

// Exit if accessed directly
if( ! defined( 'ABSPATH') ){ exit; }

// Call in the post info
global $post;

// Only show the schema if the job is active as per Google guidance - https://developers.google.com/search/docs/data-types/job-postings#create-job-postings
if( $post->post_status == 'publish' ){

	// Get all the relevant info about the job listings and get the schema
	include_once( plugin_dir_path( __FILE__ ) . 'outputs/schema-single-job.php' );
	
	// Output the schema
	echo '<script type="application/ld+json">' . json_encode( $job_schema_array ) . '</script>';
    
	// Show web schema by default
	$show_web_schema = true;
	
	// Add filter so that users can customize the fields if they want
	if( has_filter('wpjm_schema_show_website_schema') ) {
		$show_web_schema = apply_filters('wpjm_schema_show_website_schema', $show_web_schema);
	}
	
	// If we want to show the web schema
	if( $show_web_schema == true ){
	
		// Include the website schema
		include_once( plugin_dir_path( __FILE__ ) . 'outputs/schema-website.php' );
		// Output the schema
		echo '<script type="application/ld+json">' . json_encode( $website_schema_array ) . '</script>';
	
	}

}
