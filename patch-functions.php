<?php
/*
*   Functions needed to get job types and to output them - one is submitted to core but the others are custom
*
*/

// Exit if accessed directly
if( ! defined( 'ABSPATH') ){ exit; }

// Bring in the wpjm_get_the_job_types function if it isn't included in WPJM already
if( version_compare( JOB_MANAGER_VERSION, '1.27.0', '<') ) ){
/**
 * Gets the job type for the listing. - as defined here https://github.com/Automattic/WP-Job-Manager/blob/d9d1d79bf9b1ebf3ab43c06bc8c3e7aa9644519e/wp-job-manager-template.php
 *
 * @since WPJM 1.27
 *
 * @param int|WP_Post $post (default: null).
 * @return false|array
 */
function wpjm_get_the_job_types( $post = null ) {
	$post = get_post( $post );

	if ( 'job_listing' !== $post->post_type ) {
		return false;
	}

	$types = get_the_terms( $post->ID, 'job_listing_type' );

	// Return single if not enabled.
	if ( ! empty( $types ) && ! job_manager_multi_job_type() ) {
		$types = array( current( $types ) );
	}

	/**
	 * Filter the returned job types for a post.
	 *
	 * @since WPJM 1.27
	 *
	 * @param array   $types
	 * @param WP_Post $post
	 */
	return apply_filters( 'wpjm_the_job_types', $types, $post );
}

}

/**
 *  wpjm_schema_output_friendly_job_type
 *
 *  Converts the default job types to Google specified definitions - https://developers.google.com/search/docs/data-types/job-postings#definitions
 *
 *  @since 0.2
 *  @last_modified 0.4
 *
 *  @param string $specified_job_type - the name of the job type entered.
 *  @return string $job_type - Converted job type
 */
function wpjm_schema_output_friendly_job_type( $specified_job_type ){

	// Convert default job types to Google recommended values
	switch( $specified_job_type ){
		case 'Freelance':
			$job_type = 'CONTRACTOR';
			break;
		case 'Full Time':
			$job_type = 'FULL_TIME';
			break;
		case 'Internship':
			$job_type = 'INTERN';
			break;
		case 'Part Time':
			$job_type = 'PART_TIME';
			break;
		case 'Temporary':
			$job_type = 'TEMPORARY';
			break;
		default:
			$job_type = 'OTHER';
	}
	
    // Return the formatted job type
	return $job_type;
}

/**
 *  wpjm_schema_get_the_job_types
 *
 *  Runs the logic to decide whether to output job types as an array or string
 *
 *  @since 0.2
 *  @last_modified 0.4
 *
 *  @param int $post_id - the current post in the loop
 *  @return string $job_type - Converted job type as array or string
 */
function wpjm_schema_get_the_job_types( $post_id ){
	// Check how many job types there are
	$attached_job_types = wpjm_get_the_job_types( $post_id );
	$job_type_count = count( $attached_job_types );
	
	if( $job_type_count > 1 ){
		
		// Open the job types array
		$job_type = array();
		
		// Add each individual job type to an array
		foreach( $attached_job_types as $individual_job_type ){
            
            // Add formatted job type to our array string			
			$job_type[] = wpjm_schema_output_friendly_job_type( $individual_job_type->name );
			
		}
		
	} else {
		
		// For the one job type, output the friendly version
		$job_type = wpjm_schema_output_friendly_job_type( $attached_job_types[0]->name );
		
	}
	
    // Return the job type
	return $job_type;
}

/**
 *  wpjm_schema_get_the_job_categories
 *
 *  Runs the logic to decide whether to output job categories as an array or string
 *
 *  @since 0.3
 *
 *  @param int $post_id - the current post in the loop
 *  @return string $job_category - Converted job category as array or string
 */
function wpjm_schema_get_the_job_categories( $post_id ){
	// Check how many job categories there are
	$attached_job_categories = wp_get_post_terms($post_id, 'job_listing_category', array('fields' => 'names'));
	$job_category_count = count( $attached_job_categories );
	
	if( $job_category_count > 1 ){
		
		// Open the job categories array
		$job_category = array();
		
		// Add each individual job category to an array
		foreach( $attached_job_categories as $individual_job_category ){
            
            // Add formatted job category to our array string			
			$job_category[] = $individual_job_category;
			
		}
		
		
	} else {
		
		// For the one job category, output it
		$job_category = $attached_job_categories[0];
		
	}
	
    // Return the job category
	return $job_category;
}