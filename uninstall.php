<?php
/**
 * Runs when the plugin is deleted from the Plugins screen.
 * Data is only removed when the site owner switched on "Delete all BlogKit settings" in Settings > BlogKit.
 *
 * @package BlogKit
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

if (!get_option('blogkit_delete_data')) {
    return;
}

$blogkit_options = [
    'blogkit_blog_grid_widget',
    'blogkit_classic_blog_grid',
    'blogkit_card_grid',
    'blogkit_taxonomy_list',
    'blogkit_featured_sidebar',
    'blogkit_post_card',
    'blogkit_featured_grid',
    'smart_posts_list',
    'taxonomy_slider',
    'blogkit_post_carousel',
    'blogkit_fallback_image',
    'blogkit_accent_color',
    'blogkit_delete_data',
];

foreach ($blogkit_options as $blogkit_option) {
    delete_option($blogkit_option);
}

// Category images saved by the plugin.
delete_metadata('term', 0, 'cat-image', '', true);
