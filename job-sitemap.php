<?php
/*
*   Generate a jobs sitemap for all published jobs on site
*
*/

header('Content-type: application/xml');

// Call in WordPress functions
require_once("../../../wp-load.php");

// Ensure that the WP Job Manager and Schema plugins are active
if( is_plugin_active( 'wp-job-manager/wp-job-manager.php') && is_plugin_active( 'wpjm-schema/index.php') ){

?>
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"> 
	<?php
	
	// Get a query of all jobs that are available
	$all_jobs = new WP_Query( array( 'post_type' => 'job_listing', 'post_status' => 'publish', 'posts_per_page' => -1 ) );
	
	foreach( $all_jobs->posts as $post ){
		
		echo '<url>';
		echo '<loc>' . get_the_permalink( $post->ID ) . '</loc>';
		echo '<lastmod>' . date( 'c', $post->post_modified_gmt ) . '</lastmod>';
		echo '</url>';
		
	}
	?>
</urlset>
<?php
}