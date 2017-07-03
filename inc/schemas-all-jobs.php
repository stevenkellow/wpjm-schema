<?php
/*
*	Display schema for multiple job listings on an overview page (such as where the jobs shortcode is)
*
*/

// Exit if accessed directly
if( ! defined( 'ABSPATH') ){ exit; }

// Start output buffer
ob_start();

// Get a query of all jobs that are available
$all_jobs = new WP_Query( array( 'post_type' => 'job_listing', 'post_status' => 'publish', 'posts_per_page' => -1 ) );

// Total post count
$total_post_count = count( $all_jobs->posts );

// If there are jobs active on this page
if( $total_post_count > 0 ){
    
    // Create the schema array
    $job_multi_list_schema_array = array( 
        '@context' => 'http://schema.org',
        '@type' => 'ItemList',
        'itemListOrder' => 'https://schema.org/ItemListOrderAscending'
    );
	
	// Give the list a name
	$list_name = get_bloginfo('name') . ' ' . __( 'jobs', 'wpjm-schema');
	
	// Add name to the arra
	$job_multi_list_schema_array['name'] = $list_name;

    // Set up a counter for each job so we can put the list position correctly
    $counter = 1;
    
    // Create an array of items
    $job_listing_items = array();

    foreach( $all_jobs->posts as $post ){
        
        // Get the schema loaded in_admin_footer
        include( plugin_dir_path( __FILE__ ) . '/outputs/schema-single-job.php' );

        // Create the schema array
        $job_multi_list_array = array( '@type' => 'ListItem', 'position' => $counter, 'item' => $job_schema_array );
        
        // Add to the listing items
        $job_listing_items[] = $job_multi_list_array;

        // Increment list counter
        $counter++;        

    }
	
	// Restore original Post Data
    wp_reset_postdata();
    
    // Add all of the items to the schema
    $job_multi_list_schema_array['itemListElement'] = $job_listing_items;

    
    // Output the list schema
    echo '<script type="application/ld+json">' . json_encode( $job_multi_list_schema_array ) . '</script>';
    
    // Include the website schema
	require_once( plugin_dir_path( __FILE__ ) . 'outputs/schema-website.php' );
	// Output the schema
	echo '<script type="application/ld+json">' . json_encode( $website_schema_array ) . '</script>';
    
}