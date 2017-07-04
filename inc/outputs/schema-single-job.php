<?php
/*
*	JobPosting schema - http://schema.org/JobPosting
*
*	The template for showing an individual job listing's schema
*/

// Exit if accessed directly
if( ! defined( 'ABSPATH') ){ exit; }


// Main schema array
$job_schema_array = array( '@context' => 'http://schema.org', '@type' => 'JobPosting' );

/*----- GET THE GENERAL INFORMATION ---- */

// Add the job title
$job_schema_array['title'] = $post->post_title;

// Add the permalink
// Check what permalink to show
if( isset( $multi_page ) && $multi_page == true ){
    $job_schema_array['url']= get_site_url() . '/jobs/?name=' . $post->post_name; // Create a dynamic url to the main jobs page that will redirect to the job listing page
} else {
    $job_schema_array['url'] = get_the_permalink(); // Get absolute permalink
}

// Add the date opened
$job_schema_array['datePosted'] = date( 'Y-m-d', strtotime( $post->post_date ) );

// Add the closing/expiry date
$app_deadline = get_post_meta($post->ID, '_application_deadline', true); // If the application deadline plugin exists, use set deadline
if( ! $app_deadline ){
	$app_deadline = get_post_meta($post->ID, '_job_expires', true); // If no deadline set, just get the job expiry date
}
$job_schema_array['validThrough'] = $app_deadline;

// Check for the job title
if( ! empty( $post->post_title ) ){
	$job_schema_array['title'] = $post->post_title;
}

// Check for the company logo
$logo = get_the_post_thumbnail_url( $post->ID, 'full' );
if( ! empty( $logo ) ){
	$job_schema_array['image'] = $logo;
} else {
    // Get the site logo as a fallback image
    $custom_logo_id = get_theme_mod( 'custom_logo' );
    if( ! empty( $custom_logo_id ) ){
    
        $image = wp_get_attachment_image_src( $custom_logo_id , 'full' );  
        $job_schema_array['image'] = $image[0];
        
    }
    
}

// Check for the job description
if( ! empty( $post->post_content ) ){
	$job_schema_array['description'] = $post->post_content;
}


// Check if job types are enabled for job listings
if( get_option( 'job_manager_enable_types') ){

	// Get the job types JSON formatted
	$job_type = wpjm_schema_get_the_job_types( $post->ID ); // Job type formatted as 'employmentType'

}
// Check for the job type (employmentType, e.g.: FULL_TIME, PART_TIME )
if( ! empty( $job_type ) ){
	$job_schema_array['employmentType'] = $job_type;
}


// Check if categories are enabled for job listings
if ( get_option( 'job_manager_enable_categories' ) ) {
	$job_category = wpjm_schema_get_the_job_categories( $post->ID ); // Job category formatted as 'industry'
}
// Check for the job category (which we've mapped to industry)
if( ! empty( $job_category ) ){
	$job_schema_array['industry'] = $job_category;
}

/*---- SET UP THE JOB LOCATION ARRAY --- */

	// Set up the job location array
	$job_location_array = array( '@type' => 'Place');
	
	// Set up the address array
	$job_address_array = array( '@type' => 'PostalAddress' );
	
	// Add the location "locality" - use the city if we have it, or the general location otherwise
	$city = get_post_meta( $post->ID, 'geolocation_city', true);
	if( ! empty( $city ) ){
		
		$job_address_array['addressLocality'] = $city;
	
	} else{
		
		// Get the location as a fallback
		$location = get_post_meta($post->ID, '_job_location', true);
		
		if( ! empty( $location ) ){
			$job_address_array['addressLocality'] = $location;
		}
	}
	
	// Add region if we have it
	$region = get_post_meta( $post->ID, 'geolocation_state_long', true );
	if( ! empty( $region ) ){
		$job_address_array['addressRegion'] = $region;
	}
	
	// Add country if we have it
	$country = get_post_meta($post->ID, 'geolocation_country_short', true);
	if( ! empty( $country ) ){
		$job_address_array['addressCountry'] = $country;
	}
	
	// Add the address schema to the location array
	$job_location_array['address'] = $job_address_array;
	
	/*------------*/
	
	// If we've geolocated the job
	if( $geolocated == 1 ){
		
		// Set up the geolocation array
		$geo_array = array( '@type' => 'GeoCoordinates' );
		
		//Add latitude
		$geo_array['latitude'] = get_post_meta($post->ID, 'geolocation_lat', true);
		
		// Add longitude
		$geo_array['longitude'] = get_post_meta($post->ID, 'geolocation_long', true);
		
		// Add the geo array to the location array
		$job_location_array['geo'] = $geo_array;
		
		
	}
	
	// Add the job location to the final schema
	$job_schema_array['jobLocation'] = $job_location_array;
	

/* ------ ADD THE COMPANY INFORMATION ------ */

    // Set up the array
    $job_company_array = array( '@type' => 'Organization' );

    // Add the company name
	$company_name = get_the_company_name();
    $job_company_array['name'] = $company_name;

    // Add the URL if we have it
    if( ! empty( $company_url ) ){
        $job_company_array['url'] = get_the_company_website();
    }

    // Add the company logo if we have it
    if( ! empty( $logo ) ){
        $job_company_array['logo'] = $logo;
    }

    // Add the company tagline if we have it
	$company_desc = get_post_meta($post->ID, '_company_tagline', true);
    if( ! empty( $company_desc ) ){
        $job_company_array['description'] = $company_desc;
    }

    // Add the company Twitter if we have it
	$company_twitter = get_the_company_twitter();
    if( ! empty( $company_twitter ) ){

        // Set it up as a same as array
        $company_twitter_array = array( $company_twitter ); // Potentially add other social media links here

        // Add it to company info
        $job_company_array['sameAs'] = $company_twitter_array;


    }

    // Add the company infromation to the final schema
    $job_schema_array['hiringOrganization'] = $job_company_array;


/*----- ADD THE REQUIRED IDENTIFIER SECTION ----- */

    $job_identifier_array = array( '@type' => 'PropertyValue' );

    // Add the company name as the identifier
    $job_identifier_array['name'] = $company_name;

    // Add post ID as the unique value
    $job_identifier_array['value'] = $post->ID;

    // Add the identifier to the final array
    $job_schema_array['identifier'] = $job_identifier_array;

/*----- DO A FILTER ----- */

// Do a filter to check if the user wants to change any values
if( has_filter( 'wpjm_schema_custom_job_fields' )) {
	$job_schema_array = apply_filters( 'wpjm_schema_custom_job_fields', $job_schema_array );
}