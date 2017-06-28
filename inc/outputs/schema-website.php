<?php
/*
*	WebSite schema - http://schema.org/WebSite
*
*	The template for showing an the website's schema info
*/
// Start the output buffer
ob_start();
?>
	{
	  "@context": "http://schema.org",
	  "@type": "WebSite",
	  "name": "<?php echo get_bloginfo('name'); ?>",
	  "url": "<?php echo get_site_url(); ?>"
	}
<?php

$website_schema = ob_get_clean();