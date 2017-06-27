<?php
/*
*   Functions needed to get job types and to output them - one is submitted to core but the others are custom
*
*/

// Exit if accessed directly
if( ! defined( 'ABSPATH') ){ exit; }

// Bring in the get_the_job_types function if it isn't included in WPJM already
if( ! function_exists( 'get_the_job_types' ) ){
/**
 * Gets the job type for the listing. - as defined here https://github.com/Automattic/WP-Job-Manager/commit/3dd90f8363fe060808300f201389893677cbcd2c
 *
 * @since 1.26.2
 *
 * @param int|WP_Post $post (default: null).
 * @return false|array
 */
function get_the_job_types( $post = null ) {
	$post = get_post( $post );

	if ( 'job_listing' !== $post->post_type ) {
		return;
	}

	$types = get_the_terms( $post->ID, 'job_listing_type' );

	return apply_filters( 'the_job_types', $types, $post );
}

}

/**
 * wpjm_schema_output_friendly_job_type
 *
 * Converts the default job types to Google specified definitions - https://developers.google.com/search/docs/data-types/job-postings#definitions
 *
 * @param string $specified_job_type - the name of the job type entered.
 * @return string $job_type - Converted job type
 */
function wpjm_schema_output_friendly_job_type( $specified_job_type ){

	// Convert default job types to Google recommended values
	switch( $specified_job_type ){
		case 'Freelance':
			$job_type = '"CONTRACTOR"';
			break;
		case 'Full Time':
			$job_type = '"FULL_TIME"';
			break;
		case 'Internship':
			$job_type = '"INTERN"';
			break;
		case 'Part Time':
			$job_type = '"PART_TIME"';
			break;
		case 'Temporary':
			$job_type = '"TEMPORARY"';
			break;
		default:
			$job_type = '"OTHER"';
	}
	
    // Return the formatted job type
	return $job_type;
}

/**
 * wpjm_schema_get_the_job_types
 *
 * Runs the logic to decide whether to output job types as an array or string
 *
 * @param int $post_id - the current post in the loop
 * @return string $job_type - Converted job type as array or string
 */
function wpjm_schema_get_the_job_types( $post_id ){
	// Check how many job types there are
	$attached_job_types = get_the_job_types( $post_id );
	$job_type_count = count( $attached_job_types );
	
	if( $job_type_count > 1 ){
        
        // Set a counter for our upcoming loop
        $job_type_counter = 0;
		
		// Open the job types array
		$job_type = '[';
		
		// Add each individual job type to an array
		foreach( $attached_job_types as $individual_job_type ){
			
			// Get the formatted job type
			$new_job_type = wpjm_schema_output_friendly_job_type( $individual_job_type->name );
            
            // Add formatted job type to our array string			
			$job_type .= $new_job_type;
			
			// Increment the counter
			$job_type_counter++;
		
			// If there's more job types to add put in a comma
			if( $job_type_counter !== $job_type_count ){
				
				$job_type .= ',';
				
			}
			
		}
		
		// Close the job type array
		$job_type .= ']';
		
		
	} else {
		
		// For the one job type, output the friendly version
		$job_type = wpjm_schema_output_friendly_job_type( $attached_job_types[0]->name );
		
	}
	
    // Return the job type
	return $job_type;
}