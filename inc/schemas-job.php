<?php
/*
*	Schema for individual job listings (https://schema.org/JobPosting)
*
*/

// Call in the post info
global $post;

// Only show the schema if the job is active as per Google guidance - https://developers.google.com/search/docs/data-types/job-postings#create-job-postings

if( $post->post_status == 'publish' ){

    // Start the output buffer
    ob_start();

    // Get general job information
    $title = $post->post_title; // Job title
    $permalink = get_the_permalink(); // Job permalink
    $description = $post->post_content; // Get the job description
    $date = date( 'Y-m-d', strtotime( $post->post_date ) ); // Get the date the job was posted

    $app_deadline = get_post_meta($post->ID, '_application_deadline', true); // If the application deadline plugin exists, use set deadline
    if( ! $app_deadline ){
        $app_deadline = get_post_meta($post->ID, '_job_expires', true); // If no deadline set, just get the job expiry date
    }

    // Check if categories are enabled for job listings
    if ( get_option( 'job_manager_enable_categories' ) ) {
        $job_category = wp_get_post_terms($post->ID, 'job_listing_category', array("fields" => "names"))[0]; // Job category
    }
    
    // Check if job types are enabled for job listings
    if( get_option( 'job_manager_enable_types') ){

        // Get the job types JSON formatted
        $job_type = wpjm_schema_get_the_job_types( $post->ID );

    }

    // Get hiring organization details
    $company_name = get_the_company_name(); // Company name
    $company_url = get_the_company_website(); // Company URL
    $company_desc = get_post_meta($post->ID, '_company_tagline', true); // Tagline / description
    $company_twitter = 'https://twitter.com/' . get_the_company_twitter(); // Company Twitter account
    $image = get_post_meta($post->ID, '_company_logo', true); // Company login

    // Get the general job location
    $location = get_post_meta($post->ID, '_job_location', true); // General location

    // Get geolocation info if the WPJM Geolocation plugin is installed
    $geolocated = get_post_meta($post->ID, 'geolocated', true); // Check whether the job location was geolocated
    $latitude = get_post_meta($post->ID, 'geolocation_lat', true); // Geolocation latitude
    $longitude = get_post_meta($post->ID, 'geolocation_long', true); // Geolocation longitude
    $country =	get_post_meta($post->ID, 'geolocation_country_short', true); // Geolocation country

    ?>

    <script type="application/ld+json">
    {
        "@context": "http://schema.org",
        "@type": "JobPosting",
        <?php if( ! empty( $image ) ){ echo '"image": "' . $image . '",'; }
        if( ! empty( $description ) ){ echo '"description": "' . $description . '",'; }
        if( ! empty( $job_type ) ){ echo '"employmentType": ' . $job_type . ','; }
        if( ! empty( $job_category ) ){ echo '"industry": "' . $job_category . '",'; } ?>
        "jobLocation": {
            "@type": "Place",
        <?php if( $geolocated == 1 ){ ?>
            "geo": {
                "@type": "GeoCoordinates",
                "latitude": "<?php echo $latitude; ?>",
                "longitude": "<?php echo $longitude; ?>",
                "addressCountry": "<?php echo $country; ?>"
            },<?php } ?>
            "name": "<?php echo $location; ?>"
        },
        "hiringOrganization": {
            "@type" : "Organization",
                <?php if( ! empty( $company_url ) ){ echo '"url": "' . $company_url . '",'; }
                if( ! empty( $company_twitter ) ){ ?>
                "sameAs": [<?php echo '"' . $company_twitter . '"';?>],<?php }
                if( ! empty( $company_desc ) ){ echo '"description": "' . $company_desc . '",'; } ?>
                "name" : "<?php echo $company_name; ?>"
        },
        "identifier": {
            "@type": "PropertyValue",
            "name": "<?php echo $company_name; ?>",
            "value": "<?php echo $post->ID; ?>"
        },
        "title": "<?php echo $title; ?>",
        "datePosted": "<?php echo $date; ?>",
        "validThrough": "<?php echo $app_deadline; ?>",
        "url":"<?php echo $permalink; ?>"
    }
    </script>

    <?php

    // Get the output
    $job_schema = ob_get_clean();


    // Output the schema
    echo $job_schema;
    
    
}