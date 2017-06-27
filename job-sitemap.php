<?php
/*
*	Create XML sitemap for job listings
*
*/

// Exit if accessed directly
if( ! defined( 'ABSPATH') ){ exit; }

/*
*	Function to create a sitemap
*
*/
function wpjm_schema_generate_sitemap() {
  
$sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
$sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
  
// Get a query of all jobs that are available
$all_jobs = new WP_Query( array( 'post_type' => 'job_listing', 'post_status' => 'publish', 'posts_per_page' => -1 ) );

// Add the URL and last modified time (in GMT) to the sitemap
foreach( $all_jobs->posts as $post ){

    $sitemap .= '<url>';
    $sitemap .= '<loc>' . get_the_permalink( $post->ID ) . '</loc>';
    $sitemap .= '<lastmod>' . date( 'c', strtotime( $post->post_modified_gmt ) ) . '</lastmod>';
    
    // Check if post is featured and if so add a higher priority
    $featured = get_post_meta( $post->ID, '_featured', true );
    if( $featured == 1 ){
        
        $sitemap .= '<priority>0.8</priority>';
        
    }
    
    $sitemap .= '</url>';
  
}

$sitemap .= '</urlset>';

// Write the sitemap to yoursite.com/job-sitemap.xml
$fp = fopen(ABSPATH . 'job-sitemap.xml', 'w');
fwrite($fp, $sitemap);
fclose($fp);

// Let Google know the sitemap has been updated
wpjm_schema_google_ping();

}

/*
*	Function to let Google know that the sitemap has been updated
*
*/
function wpjm_schema_google_ping(){
	
	// Set up the sitemap ping URL
	$ping_url = 'http://www.google.com/ping?sitemap=' . trailingslashit( get_site_url() ) . 'job-sitemap.xml';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ping_url); // The URL we're using to get/send data
    
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15); // Timeout when connecting to the server
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Timeout when retrieving from the server
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // We don't want to force SSL incase a site doesn't use it
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Hide the output from the page

    // Get the response
    $response = curl_exec($ch);
    
    // Check if there's an error
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // If there's any curl errors or server-side errors lets show that
    if (curl_errno($ch) || ( $httpcode < 200 || $httpcode >= 300 )  ) {
        $data = false;
		curl_close($ch);
    } else {
		// It worked
        $data = true;
		
        curl_close($ch);
        
    }

    // Send a response back
    return $data;

}