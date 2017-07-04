<?php
/*
*	WebSite schema - http://schema.org/WebSite
*
*	The template for showing an the website's schema info
*/

// Exit if accessed directly
if( ! defined( 'ABSPATH') ){ exit; }

// Create the schema
$website_schema_array = array(
		'@context' => 'http://schema.org',
		'@type' => 'WebSite',
		'name' => get_bloginfo('name'),
		'url' => get_site_url()
);

// Check if the site has an image and add it if so
$custom_logo_id = get_theme_mod( 'custom_logo' );
if( ! empty( $custom_logo_id ) ){
    $image = wp_get_attachment_image_src( $custom_logo_id , 'full' );
    $website_schema_array['image'] = $image[0];
}

/*----- DO A FILTER ----- */

// Add filter so that users can customize the fields if they want
if( has_filter('wpjm_schema_custom_website_fields' )) {
	$website_schema_array = apply_filters( 'wpjm_schema_custom_website_fields', $website_schema_array );
}