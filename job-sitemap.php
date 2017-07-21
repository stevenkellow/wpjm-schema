<?php
/*
*	Create XML sitemap for job listings
*
*/

// Exit if accessed directly
if( ! defined( 'ABSPATH') ){ exit; }

/**
 *  wpjm_schema_generate_sitemap
 *
 *  Function to create the sitemap
 *
 *  @since 0.2
 *  @last_modified 0.4
 */
function wpjm_schema_generate_sitemap() {
    
    // Generate sitemap by default
    $create_sitemap = true;

    // Add filter so that users turn off the sitemap generation if they want
    if( has_filter('wpjm_schema_generate_job_sitemap') ) {
        $create_sitemap = apply_filters('wpjm_schema_generate_job_sitemap', $create_sitemap);
    }

    // If we want to create the sitemap
    if( $create_sitemap === true ){
  
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Get a query of all jobs that are available
        $all_jobs = new WP_Query( array( 'post_type' => 'job_listing', 'post_status' => 'publish', 'posts_per_page' => -1 ) );

        // Add the URL and last modified time (in GMT) to the sitemap
        foreach( $all_jobs->posts as $post ){

          $sitemap .= '<url>';
          $sitemap .= '<loc>' . get_the_permalink( $post->ID ) . '</loc>'; // Add the URL
          $sitemap .= '<lastmod>' . date( 'c', strtotime( $post->post_modified_gmt ) ) . '</lastmod>'; // Add the last modified time in GMT
          $sitemap .= '</url>';

        }

        $sitemap .= '</urlset>';

        // Write the sitemap to yoursite.com/job-sitemap.xml
        $fp = fopen(ABSPATH . 'job-sitemap.xml', 'w');
        fwrite($fp, $sitemap);
        fclose($fp);

        // Ping Google by default
        $ping_search_engines = true;

        // Add filter so that users turn off the Google ping if they want
        if( has_filter('wpjm_schema_ping_search_engines') ) {
            $ping_search_engines = apply_filters('wpjm_schema_ping_search_engines', $ping_search_engines);
        }

        // If we want to ping Google
        if( $ping_search_engines === true ){

            // Let Google know the sitemap has been updated
            wpjm_schema_search_engine_ping();

        }
        
    }

}

/**
 *  wpjm_schema_search_engine_ping
 *
 *  Function to let Google/Bing know that the sitemap has been updated
 *
 *  @since 0.3
 *  @last_modified 0.4
 */
function wpjm_schema_search_engine_ping(){
    
    // Get the sitemap URL
    $sitemap_url = trailingslashit( get_site_url() ) . 'job-sitemap.xml';
	
	// Set up the sitemap ping URLs
    $ping_urls = array( 
        'google' => 'http://www.google.com/ping?sitemap=' . $sitemap_url,
        'bing' => 'http://www.bing.com/ping?sitemap=' . $sitemap_url 
	);

    // Set up the arguments - we don't want SSLverify as the sitemap endpoints don't use it
    $args = array( 'sslverify' => false );
    
    // Set the response success variable
    $data = true;
    
    // Ping Google & Bing
    foreach( $ping_urls as $engine => $value ){
        
        // Make the ping
        $response = wp_remote_get( $value, $args );
    
        // Check the response HTTP code
        $httpcode = $response['response']['code'];

        // If there's any errors lets show that
        if ( $httpcode < 200 || $httpcode >= 300 ) {

            $data = false;

        }
        
    }
    
    // Send a response back
    return $data;

}

/**
 *  wpjm_schema_remove_sitemap
 *
 *  Function to remove the sitemap
 *
 *  @since 0.4.4
 */
function wpjm_schema_remove_sitemap(){
	
	wp_delete_file( ABSPATH . 'job-sitemap.xml' );
	
}