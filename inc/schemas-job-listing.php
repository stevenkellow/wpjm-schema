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

	// Get all the relevant info about the job listings
	require_once( plugin_dir_path( __FILE__ ) . 'job-listing-info.php' );

	// Include the schema for the job
	require_once( plugin_dir_path( __FILE__ ) . 'outputs/schema-single-job.php' );
	// Output the schema
	echo '<script type="application/ld+json">' . $job_schema . '</script>';
    
	// Include the website schema
	require_once( plugin_dir_path( __FILE__ ) . 'outputs/schema-website.php' );
	// Output the schema
	echo '<script type="application/ld+json">' . $website_schema . '</script>';

}
