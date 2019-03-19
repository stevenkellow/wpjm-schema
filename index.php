<?php
/*
Plugin Name: WPJM Schema
Plugin URI: https://wordpress.org/plugins/wpjm-schema/
Description: Adds Schema.org markup to your WP Job Manager pages
Version: 0.6
Author: Steven Kellow
Author URI: https://www.stevenkellow.com
Text Domain: wpjm-schema
Domain Path: /languages
*/

// Exit if accessed directly
if( ! defined( 'ABSPATH') ){ exit; }

// Make sure plugin check will work
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// Check the WPJM plugin exists
if( is_plugin_active( 'wp-job-manager/wp-job-manager.php') ){
	
	// Call in custom functions for output
	include_once( plugin_dir_path( __FILE__ ) . 'patch-functions.php' );
    
    /*
    *   If we're on WPJM version 1.28 or above then it includes schema on its own
    *   We want this plugin to be able to do more than just stock schemas
    *   So we're going to remove the default WPJM schemas
    *   If the defaults work for you, this plugin can be uninstalled, else use this
    */
    if( version_compare( JOB_MANAGER_VERSION, '1.28.0', '>=') ){
        //Get the current post
        global $post;
        add_filter( 'wpjm_output_job_listing_structured_data', 'wpjm_schema_remove_default_data', 15 );
        function wpjm_schema_remove_default_data( $data = false ){
            return false;
        }
    }
    
    /**
	 *  wpjm_schema_print
	 *
	 *  Function to print the schema in the document footer
	 *
	 *  @since 0.2
	 *  @last_modified 0.5
	 */
    add_action('wp_footer', 'wpjm_schema_print');
    function wpjm_schema_print(){
        
        // Check if WPJM has it's own schema built-in
        if( version_compare( JOB_MANAGER_VERSION, '1.28.0', '<') ){
            $wpjm_native_schema = true;
        } else {
            $wpjm_native_schema = false;
        }

        // Check if the current page is a jobs overview page - deprecated (false by default) because Google doesn't want us to do this: https://developers.google.com/search/docs/data-types/job-postings#guidelines
        $show_multi_job_schema = false;

        // Add filter so that users turn on the multi job schema if they want
        if( has_filter('wpjm_schema_show_multi_job_schema' ) ) {
            $show_multi_job_schema = apply_filters( 'wpjm_schema_show_multi_job_schema', $show_multi_job_schema );
        }

        // If we want to create the sitemap
        if( $show_multi_job_schema === true ){
        
            //Get the current post
            global $post;

            // If this page has the jobs shortcode on it
            if( is_singular( $post ) && has_shortcode( $post->post_content, 'jobs') ) {

                // Set a variable to say we're on a multi page
                $multi_page = true;

                // Include the all jobs schema
                include_once( plugin_dir_path( __FILE__ ) . 'inc/schemas-all-jobs.php' );

            }
        }

        // Check if the page is a job listing
        if( 'job_listing' == get_post_type() ){
            
            // Set a variable to say we're on a single job listing page
            $multi_page = false;

            // Include the single job page schema
            include_once( plugin_dir_path( __FILE__ ) . 'inc/schemas-job-listing.php' );

        }

    }
	
	
	/*
	*  Call in the sitemap generator - we check if it should be activated inside the function
	*
	*/
	include_once( plugin_dir_path( __FILE__ ) . 'job-sitemap.php' );
	
	// Run the sitemap generator when a new job is published, updated or the plugin is installed
	add_action( 'publish_job_listing', 'wpjm_schema_generate_sitemap' );
	add_action( 'save_post_job_listing', 'wpjm_schema_generate_sitemap' );
	register_activation_hook(  __FILE__, 'wpjm_schema_generate_sitemap' );

	// Delete the sitemap if the plugin is deactivated
	register_deactivation_hook(  __FILE__, 'wpjm_schema_remove_sitemap' );

	// Add a CRON job to run with the expired jobs hook to make sure sitemap is updated if jobs expire
	add_action( 'job_manager_check_for_expired_jobs', 'wpjm_schema_generate_sitemap' );
	
    
    
} else {
    
    // If the WP Job Manager plugin is not installed not only will we not post schema, but we'll create an admin notice
    
    // Check if we're on an admin page
    if( is_admin() ){
    
        // Create the install warning
        add_action( 'admin_notices', 'wpjm_schema_main_plugin_install_warning' );
        function wpjm_schema_main_plugin_install_warning() {
        ?>
        <div class="notice notice-error is-dismissible">
            <p><?php _e( 'WP Job Manager plugin not installed, please install to activate JobPosting schema', 'wpjm-schema' ); ?></p>
        </div>
        <?php
        }
    
    }
    
}