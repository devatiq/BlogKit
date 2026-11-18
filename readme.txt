=== BlogKit - Advanced Blog Elements for Elementor ===
Contributors: nexibyllc,atiqbd4ever
Tags: blog grid, post grid, blog layout, posts widget, page builder
Requires at least: 5.8
Tested up to: 7.1
Requires PHP: 8.0
Stable tag: 1.3.8
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

BlogKit enhances your WordPress blog with advanced Elementor widgets and features for creating beautiful blog layouts.

== Description ==

BlogKit is a powerful WordPress plugin that extends Elementor with advanced blog widgets and features. Create stunning blog layouts with various styles and customization options.

= Key Features =

* Blog Classic Grid Widget
* Customizable Post Layouts
* Advanced Styling Options
* Post Format Support
* Responsive Design
* Easy to Use Interface

= Blog Classic Grid Widget =

The Blog Classic Grid widget allows you to:
* Display posts in 2-4 columns grid layout
* Customize colors, typography, and spacing
* Show/hide post meta information
* Support for all post formats (Standard, Audio, Video, Gallery, etc.)
* Responsive design for all devices

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/blogkit` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the BlogKit widgets in Elementor editor

== Frequently Asked Questions ==

= Does this plugin require Elementor? =

Yes, BlogKit requires Elementor page builder plugin to be installed and activated.

= Is this plugin compatible with my theme? =

BlogKit works with any WordPress theme that supports Elementor.

= Can I customize the colors and typography? =

Yes, all BlogKit widgets come with extensive styling options in the Elementor editor.


== Screenshots ==

1. Blog Grid widget showing posts on a page.
2. The new Query options (post types, tags, authors, exclude posts, date range) in the Elementor editor.

== Source code ==

BlogKit has no build step. All PHP, CSS and JavaScript in this plugin is readable source. The only minified files are the bundled Swiper slider library (version 11.2.10, MIT licence): `Frontend/Elementor/Assets/js/swiper-bundle.min.js` and `Frontend/Elementor/Assets/css/swiper-bundle.min.css`. Their source is at https://github.com/nolimits4web/swiper and https://swiperjs.com . The plugin source is at https://github.com/devatiq/BlogKit .

== Privacy ==

BlogKit does not collect or store personal data, does not set cookies and does not send anything to its author or any third party. The only data it saves is the optional category image URL, stored as category (term) meta in your own database.

== External services ==

BlogKit does not connect to any external service. All scripts, styles and icons are bundled with the plugin.

== Trademarks ==

"BlogKit" is an original name: a "kit" of blog widgets. "Elementor" is named only to say that this plugin is an add-on that works with the Elementor page builder. BlogKit is not made by or affiliated with Elementor Ltd.

== Changelog ==
= 1.3.8 =
* New - Optional reading time ("N min read") for Blog Grid, Post Card and Card Grid (style 1), with a words-per-minute setting.

= 1.3.7 =
* New - Optional Schema Markup switch (schema.org ItemList JSON-LD) for Blog Grid, Blog Classic Grid, Post Card, Card Grid, Featured Grid and Smart Posts List. Off by default.

= 1.3.6 =
* New - Load more button and infinite scroll pagination for Blog Grid, Post Card, Card Grid (style 1) and Smart Posts List.
* Fix - "No posts found." no longer shows under the posts when there is only one page (Post Card, Smart Posts List).

= 1.3.5 =
* Fix - Plugin now uses its own lightweight autoloader (no Composer vendor folder shipped).
* Fix - Readme: added Screenshots, Source code, Privacy, External services and Trademarks sections, and removed a product name from the tags.
* Fix - Added GPL license file and the missing direct-access guard in the SVG helper.

= 1.3.4 =
* New - Post widgets (Blog Grid, Blog Classic Grid, Post Card, Card Grid, Featured Grid, Smart Posts List) now have extra query options: Post Types, Tags, Authors, Exclude Posts and Date Range.
* Fix - Added missing direct-access guards to the Featured Grid template and SVG helper.

= 1.3.3 =
* Security - Title HTML tag setting is now restricted to an allowed list (h1-h6, div, span, p).
* Security - Post links, category links and read-more text are now escaped on output.
* Security - Category image field now checks user capability and sanitizes the saved URL.
* Fix - Plugin version constant now matches the plugin header (fixes stale asset cache).
* Fix - Featured Grid text domain corrected so strings are translatable.
* Performance - Skip unneeded total-row counting in Featured Sidebar, Featured Grid and Blog Classic Grid queries.
* Tweak - Use wp_rand() instead of mt_rand(); category image field labels are translatable.

= 1.3.2 =
* Card Grid - Added fallback/placeholder thumbnail feature for posts without a featured image.
* Card Grid - Added Enable/Disable toggle for fallback thumbnail in the Layout settings.
* Card Grid - Fixed fallback thumbnail height to match real thumbnail size.

= 1.3.1 =
* CSS issue fix

= 1.3.0 =
* Card Grid - Layout 2,3 Added.
* Post Card.
* Smart Posts List - Layout 1,2,3.
* Featured Grid (New Widget).
* Taxonomy Slider (New Widget).

= 1.2.1 =
* Added Featured Grid Widget
* Card Grid Widget pagination added and improvement features.

= 1.2.0 =
* Add Taxonomy List Widgets
* Add Grid Layout Widgets

= 1.1.0 =
* Taxonomy List Widgets added
* Added Card Grid Widget

= 1.0.0 =
* Initial release
* Added Blog Classic Grid Widget
* Added post format support
* Added styling options

== Upgrade Notice ==

= 1.3.8 =
Adds optional reading time to the post widgets.

= 1.3.7 =
Adds optional schema.org markup for post lists.

= 1.3.6 =
Adds Load more and infinite scroll pagination.

= 1.3.5 =
Packaging and readme fixes. No functional changes.

= 1.3.4 =
Adds extra query options to the post widgets.

= 1.3.3 =
Security hardening and bug fixes. Update recommended.

= 1.0.0 =
Initial release of BlogKit plugin.
