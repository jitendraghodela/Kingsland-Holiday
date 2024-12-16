<?php
/**
 * Uninstall file for Kingsland Tour Packages plugin
 * 
 * This file runs when the plugin is uninstalled from WordPress.
 * It cleans up all plugin-related data from the database and file system.
 * 
 * @package Kingsland_Tour_Packages
 * @version 1.0.0
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN') || !WP_UNINSTALL_PLUGIN) {
    die;
}

// If multisite, handle each site separately
if (is_multisite()) {
    global $wpdb;
    $blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
    foreach ($blog_ids as $blog_id) {
        switch_to_blog($blog_id);
        kingsland_tour_uninstall_cleanup();
        restore_current_blog();
    }
} else {
    kingsland_tour_uninstall_cleanup();
}

/**
 * Perform all cleanup operations for the plugin
 */
function kingsland_tour_uninstall_cleanup()
{
    global $wpdb;

    try {
        // Clear all caches
        wp_cache_flush();

        // Clear plugin transients
        delete_transient('kingsland_tour_packages_cache');
        delete_site_transient('kingsland_tour_packages_cache');

        // Clear cache directory
        $upload_dir = wp_upload_dir();
        $cache_dir = trailingslashit($upload_dir['basedir']) . 'kingsland-tour-cache';
        if (is_dir($cache_dir)) {
            $files = glob($cache_dir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($cache_dir);
        }

        // Delete all plugin options
        delete_option('kingsland_tour_packages_options');
        delete_option('kingsland_tour_packages_version');
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'kingsland_tour_%'");

        // Define meta keys
        $meta_keys = array(
            'kingsland_tour_price',
            'kingsland_tour_duration',
            'kingsland_tour_locations'
        );

        // Delete user meta
        $user_args = array(
            'fields' => 'ID',
            'meta_query' => array(
                'relation' => 'OR',
                array_map(function ($key) {
                    return array(
                        'key' => $key,
                        'compare' => 'EXISTS'
                    );
                }, $meta_keys)
            )
        );

        $users_with_meta = get_users($user_args);
        foreach ($users_with_meta as $user_id) {
            foreach ($meta_keys as $meta_key) {
                delete_user_meta($user_id, $meta_key);
            }
        }

        // Delete tour posts and their meta
        $posts_args = array(
            'post_type' => 'tour_package',
            'posts_per_page' => -1,
            'post_status' => 'any',
            'fields' => 'ids',
            'no_found_rows' => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false
        );

        $tour_posts = get_posts($posts_args);
        foreach ($tour_posts as $post_id) {
            wp_delete_post($post_id, true);
        }

        // Clean up post meta
        foreach ($meta_keys as $meta_key) {
            delete_post_meta_by_key($meta_key);
        }

        // Delete custom taxonomies terms
        $taxonomies = array('tour_category', 'tour_tag'); // Add your custom taxonomies
        foreach ($taxonomies as $taxonomy) {
            $terms = get_terms(array(
                'taxonomy' => $taxonomy,
                'hide_empty' => false,
            ));
            if (!is_wp_error($terms)) {
                foreach ($terms as $term) {
                    wp_delete_term($term->term_id, $taxonomy);
                }
            }
        }

        // Final cleanup
        wp_cache_flush();

        // Log success
        if (WP_DEBUG) {
            error_log('Kingsland Tour Packages plugin uninstalled successfully');
        }

    } catch (Exception $e) {
        if (WP_DEBUG) {
            error_log('Error during Kingsland Tour Packages uninstallation: ' . $e->getMessage());
        }
    }
}
