<?php
/*
*	Display schema for multiple job listings on an overview page (such as where the jobs shortcode is)
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

    // Get all the relevant info about the job listings
	require_once( plugin_dir_path( __FILE__ ) . 'job-listing-info.php' );

    ?>
            {
            "@type": "ListItem",
            "position": <?php echo $counter; ?>,
            "item": <?php
        
            // Include the schema for the job
            require_once( plugin_dir_path( __FILE__ ) . 'outputs/schema-single-job.php' );
            // Output the schema
            echo $job_schema;
                
            ?>}<?php if( $counter !== $total_post_count ){ echo ','; }

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
    
    // Include the website schema
	require_once( plugin_dir_path( __FILE__ ) . 'outputs/schema-website.php' );
	// Output the schema
	echo '<script type="application/ld+json">' . $website_schema . '</script>';
    
}