<?php
// Exit if uninstall not called from WordPress
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Check if user has proper permissions
if (!current_user_can('activate_plugins')) {
    return;
}

// Required WordPress files
require_once(ABSPATH . 'wp-admin/includes/plugin.php');
// Clear all caches
wp_cache_flush();

// Clear WordPress object cache
if (function_exists('wp_cache_clear_cache')) {
    wp_cache_clear_cache();
}

// Clear specific plugin transients and cache
delete_transient('kingsland_tour_packages_cache');
delete_site_transient('kingsland_tour_packages_cache');

// Clear cache directories if they exist
$upload_dir = wp_upload_dir();
$cache_dir = $upload_dir['basedir'] . '/kingsland-tour-cache';
if (is_dir($cache_dir)) {
    array_map('unlink', glob("$cache_dir/*.*"));
    rmdir($cache_dir);
}

global $wpdb;

// Begin cleanup process
try {
    // Delete plugin table
    $table_name = $wpdb->prefix . 'kingsland_tour_packages';
    $wpdb->query("DROP TABLE IF EXISTS {$table_name}");

    // Delete plugin options
    delete_option('kingsland_tour_packages_options');

    // Delete plugin transients
    delete_transient('kingsland_tour_packages_cache');

    // Delete plugin user meta
    $wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE '%kingsland_tour%'");

    // Delete plugin post meta
    $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '%kingsland_tour%'");

    // Delete custom post types
    $posts = get_posts(array(
        'post_type' => 'tour_package',
        'numberposts' => -1,
        'post_status' => 'any'
    ));

    foreach ($posts as $post) {
        wp_delete_post($post->ID, true);
    }

    // Clear any cached data
    wp_cache_flush();

    // Log successful uninstallation
    error_log('Kingsland Tour Packages plugin uninstalled successfully');

} catch (Exception $e) {
    // Log any errors during uninstallation
    error_log('Error during Kingsland Tour Packages uninstallation: ' . $e->getMessage());
}