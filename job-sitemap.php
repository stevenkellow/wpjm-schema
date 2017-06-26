<?php
/*
*	Create XML sitemap for job listings
*
*/	
function wpjm_schema_generate_sitemap() {
  
$sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
$sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
  
// Get a query of all jobs that are available
$all_jobs = new WP_Query( array( 'post_type' => 'job_listing', 'post_status' => 'publish', 'posts_per_page' => -1 ) );

foreach( $all_jobs->posts as $post ){

  $sitemap .= '<url>';
  $sitemap .= '<loc>' . get_the_permalink( $post->ID ) . '</loc>';
  $sitemap .= '<lastmod>' . date( 'c', strtotime( $post->post_modified_gmt ) ) . '</lastmod>';
  $sitemap .= '</url>';
  
}

$sitemap .= '</urlset>';

$fp = fopen(ABSPATH . 'job-sitemap.xml', 'w');
fwrite($fp, $sitemap);
fclose($fp);

}