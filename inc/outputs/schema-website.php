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