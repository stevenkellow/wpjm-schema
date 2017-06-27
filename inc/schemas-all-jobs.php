<?php
/*
*	Schema for multiple job listings (https://schema.org/JobPosting)
*
*/

// Exit if accessed directly
if( ! defined( 'ABSPATH') ){ exit; }

// Start output buffer
ob_start();

// Get a query of all jobs that are available
$all_jobs = new WP_Query( array( 'post_type' => 'job_listing', 'post_status' => 'publish', 'posts_per_page' => -1 ) );

// Total post count
$total_post_count = count( $all_jobs->posts );

// If there are jobs active on this page
if( $total_post_count > 0 ){

    ?>

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "ItemList",
        "itemListElement": [
    <?php

    // Set up a counter for each job so we can put the list position correctly
    $counter = 1;

    foreach( $all_jobs->posts as $post ){

        // Get general job information
        $title = $post->post_title; // Job title
        $permalink = get_site_url() . '/jobs/?name=' . $post->post_name; // Job permalink
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
        $image = get_the_post_thumbnail_url( $post->ID, 'full' ); // Company logo

        // Get geolocation info
        $location = get_post_meta($post->ID, '_job_location', true); // General location

        $geolocated = get_post_meta($post->ID, 'geolocated', true); // Check whether the job location was geolocated
        $latitude = get_post_meta($post->ID, 'geolocation_lat', true); // Geolocation latitude
        $longitude = get_post_meta($post->ID, 'geolocation_long', true); // Geolocation longitude
        $country =	get_post_meta($post->ID, 'geolocation_country_short', true); // Geolocation country

        $city = get_post_meta( $post->ID, 'geolocation_city', true); // City
        $region = get_post_meta( $post->ID, 'geolocation_state_long', true ); // Region
        $postal_code = get_post_meta( $post->ID, 'geolocation_postcode', true); // Postal or ZIP code

    ?>
            {
            "@type": "ListItem",
            "position": <?php echo $counter; ?>,
            "item": {
                "@context": "http://schema.org",
                "@type": "JobPosting",
                <?php if( ! empty( $image ) ){ echo '"image": "' . $image . '",'; }
                if( ! empty( $description ) ){ echo '"description": "' . $description . '",'; }
                if( ! empty( $job_type ) ){ echo '"employmentType": ' . $job_type . ','; } // Note we add the double quotes earlier here in case of array
                if( ! empty( $job_category ) ){ echo '"industry": "' . $job_category . '",'; } ?>
                "jobLocation": {
                    "@type": "Place",
                    <?php if( $geolocated == 1 ){ ?>
                    "address": {
                        "@type": "PostalAddress",
                    <?php if( ! empty( $city ) ){ ?>"addressLocality": "<?php echo $city; ?>",<?php } else { if( ! empty( $location ) ){ ?>"addressLocality": "<?php echo $city; ?>",<?php } } ?>
                        <?php if( ! empty( $region ) ){ ?>"addressRegion": "<?php echo $region; ?>",<?php } ?>
                        "addressCountry": "<?php echo $country; ?>"
                    },
                    <?php if( ! empty( $latitude ) && ! empty( $longitude ) ){ ?>"geo": {
                        "@type": "GeoCoordinates",
                        "latitude": "<?php echo $latitude; ?>",
                        "longitude": "<?php echo $longitude; ?>"				
                    }<?php } } ?>
                },
                "hiringOrganization": {
                "@type" : "Organization",
                    <?php if( ! empty( $company_url ) ){ echo '"url": "' . $company_url . '",'; }
                    if( ! empty( $company_twitter ) ){ ?>
                    "sameAs": [<?php echo '"' . $company_twitter . '"';?>],<?php }
                    if( ! empty( $company_desc ) ){ echo '"description": "' . $company_desc . '",'; }
                    if( ! empty( $image ) ){ echo '"logo": "' . $image . '",'; } ?>
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
        }<?php if( $counter !== $total_post_count ){ echo ','; }

        // Increment list counter
        $counter++;

        /* Restore original Post Data */
        wp_reset_postdata();

    }
    ?>
        ],
        "itemListOrder": "https://schema.org/ItemListOrderAscending",
        "name": "<?php echo get_bloginfo('name'); ?> jobs"
    }
    </script>
    <?php

    // Get the output
    $all_jobs_schema = ob_get_clean();

    // Output the schema
    echo $all_jobs_schema;
    
}

?>