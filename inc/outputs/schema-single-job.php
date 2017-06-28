<?php
/*
*	JobPosting schema - http://schema.org/JobPosting
*
*	The template for showing an individual job listing's schema
*/

// Start the output buffer
ob_start();
?>
    {
        "@context": "http://schema.org",
        "@type": "JobPosting",
        <?php if( ! empty( $image ) ){ echo '"image": "' . $image . '",'; }
        if( ! empty( $description ) ){ echo '"description": "' . $description . '",'; }
        if( ! empty( $job_type ) ){ echo '"employmentType": ' . $job_type . ','; }
        if( ! empty( $job_category ) ){ echo '"industry": ' . $job_category . ','; } ?>
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
<?php

$job_schema = ob_get_clean();