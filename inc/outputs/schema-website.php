<?php
/*
*	WebSite schema - http://schema.org/WebSite
*
*	The template for showing an the website's schema info
*/

// Exit if accessed directly
if( ! defined( 'ABSPATH') ){ exit; }

// Create the schema
$website_array = array(
		'@context' => 'http://schema.org',
		'@type' => 'WebSite',
		'name' => get_bloginfo('name'),
		'url' => get_site_url()
);

// Set the schema for output
$website_schema = json_encode( $website_array );