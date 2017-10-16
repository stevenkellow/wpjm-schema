=== WPJM Schema ===
Contributors: stevenkellow
Tags: jobs, wpjobmanager, wpjm, schema, seo
Requires at least: 4.4
Tested up to: 4.8.2
Stable tag: 0.5
License: GPL

Add Schema.org markup to your WP Job Manager pages and job listings.

== Description ==
Schema.org markup is a way of giving search engines more information about your website so that they can automatically process your pages and display that to users.

Google is now supporting JobPostings schemas, and will eventually use these to produce dedicated job listing search results.  That's why it's important that YOUR site has schema markup installed so that your listings will appear.

This simple plugin will work alongside your WP Job Manager install and provide this schema for you, with no fuss and no editing required.

You can access your own job listings sitemap at yourdomain.com/job-sitemap.xml - which you can submit to search engines to help get your pages ranked.

We also provide built-in support for two helpful WP Job Manager extensions:
* Application deadline
* Geolocation

== Installation ==
AUTOMATIC INSTALLATION

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don’t even need to leave your web browser. To do an automatic install, log in to your WordPress admin panel, navigate to the Plugins menu and click Add New.

In the search field type “WPJM Schema” and click Search Plugins. Once you’ve found the plugin you can view details about it such as the point release, rating and description. Most importantly of course, you can install it by clicking Install Now.

MANUAL INSTALLATION

The manual installation method involves downloading the plugin and uploading it to your web server via your favorite FTP application.

Download the plugin file to your computer and unzip it
Using an FTP program, or your hosting control panel, upload the unzipped plugin folder to your WordPress installation’s wp-content/plugins/ directory.
Activate the plugin from the Plugins menu within the WordPress admin.

The plugin works straight out of the box, so there\'s no other options to configure.

== Filters/Customisation ==
There are filters you can use by placing code in a child theme or functionality plugin to modify how WPJM schema works.

You can read about them all here: https://blog.stevenkellow.com/wpjm-job-schema-plugin-for-wordpress/#filters

== Changelog ==
==== v 0.5 ====
- WP Job Manager 1.28 support
-- Makes sure that this plugin doesn't overlap with WP Job Managers' new schema functions
- Move schema to footer for faster page loading

===== v 0.4.4 =====
- WP Job Manager 1.27 support
- Fix sitemap loading (so creates on install/update and deletes on deactivate)
- Fix job expiry (so those with blank job expiry doesn't load validThrough attribute)
- Fix company logo output
- Fix geolocation settings (check if they exist before loading)

==== v 0.4 ====
- Add filters to allow customisation of field values and types, schemas included and sitemap functionality.
- Make sure schema is output correctly on multi-job pages
- Fix permalink for multi-job pages
- Fix multi job type/category output in JSON
- Fix multi job loop issues where the same schema was output for multiple jobs
- Send ping to Bing when sitemap is updated
- Use wp_remote_get to avoid an issue where CURL not installed

==== v 0.3 ====
- Use PHP data structure to output minified schema
- Re-organise plugin to have clearer structure and less repetition
- Ping Google when sitemap updated
- Support multiple job categories (used as Industry)
- Fix company logo retrieval
- Get more geolocation data
- Add website details to Schema

==== v 0.2 ====
- Added support for XML sitemaps
- Follow Google guidelines more closely
-- Deprecate list schemas
-- Hide schemas on expired listings
-- Introduce identifier attribute
-- Use suggested employment types
- Simplify DB calls by avoiding get_the functions where possible
- Support multiple job listing types