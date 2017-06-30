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
$job_schema_array['title'] = $title;

// Add the permalink
$job_schema_array['url'] = $permalink;

// Add the date opened
$job_schema_array['datePosted'] = $date;

// Add the closing/expiry date
$job_schema_array['validThrough'] = $app_deadline;

// Check for the job title
if( ! empty( $title ) ){
	$job_schema_array['title'] = $title;
}

// Check for the company logo
if( ! empty( $image ) ){
	$job_schema_array['image'] = $image;
}

// Check for the job description
if( ! empty( $description ) ){
	$job_schema_array['description'] = $description;
}

// Check for the job type (employmentType, e.g.: FULL_TIME, PART_TIME )
if( ! empty( $job_type ) ){
	$job_schema_array['employmentType'] = $job_type;
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
	if( ! empty( $city ) ){
		$job_address_array['addressLocality'] = $city;
	} elseif( ! empty( $location ) ){
		$job_address_array['addressLocality'] = $location;
	}
	
	// Add region if we have it
	if( ! empty( $region ) ){
		$job_address_array['addressRegion'] = $region;
	}
	
	// Add country if we have it
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
		$geo_array['latitude'] = $latitude;
		
		// Add longitude
		$geo_array['longitude'] = $longitude;
		
		// Add the geo array to the location array
		$job_location_array['geo'] = $geo_array;
		
		
	}
	
	
	
	// Add the job location to the final schema
	$job_schema_array['jobLocation'] = $job_location_array;
	

/* ------ ADD THE COMPANY INFORMATION ------ */

// Set up the array
$job_company_array = array( '@type' => 'Organization' );

// Add the company name
$job_company_array['name'] = $company_name;

// Add the URL if we have it
if( ! empty( $company_url ) ){
	$job_company_array['url'] = $company_url;
}

// Add the company logo if we have it
if( ! empty( $image ) ){
	$job_company_array['logo'] = $image;
}

// Add the company tagline if we have it
if( ! empty( $company_desc ) ){
	$job_company_array['description'] = $company_desc;
}

// Add the company Twitter if we have it
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


/*----- DO THE FINAL OUTPUTTING ---- */

$job_schema = json_encode($job_schema_array);