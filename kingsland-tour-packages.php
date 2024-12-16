<?php
/*
Plugin Name: Kingsland Tour Packages
Plugin URI: https://SERPDIGISOLUTION.com/plugins/kingsland-tour-packages
Description: A plugin to manage tour packages.
Version: 1.0.0
Requires at least: 5.0 
Tested up to: 6.7
Requires PHP: 7.2
Author: Jitendra Kumawat
Author URI: https://SERPDIGISOLUTION.com/about
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: kingsland-tour-packages

=== Kingsland Tour Packages ===
Contributors: jitendraghodela
Tags: tours, packages, travel
Requires at least: 5.0
Tested up to: 6.7
Stable tag: 1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/


if (!defined('ABSPATH')) {
    exit;
}
// Safe media handling function
function kingsland_safe_get_attachment($attachment_id)
{
    if (!$attachment_id || !is_numeric($attachment_id)) {
        return false;
    }

    $attachment = get_post($attachment_id);
    if (!$attachment) {
        return false;
    }

    $metadata = wp_get_attachment_metadata($attachment_id);
    if (!is_array($metadata)) {
        $metadata = array();
    }

    return array(
        'id' => $attachment_id,
        'url' => wp_get_attachment_url($attachment_id),
        'metadata' => $metadata
    );
}
// Load required WordPress dependencies
function kingsland_load_dependencies()
{
    if (!function_exists('get_post_meta')) {
        require_once(ABSPATH . 'wp-includes/post.php');
    }

    if (!function_exists('wp_get_attachment_metadata')) {
        require_once(ABSPATH . 'wp-includes/media.php');
    }

    if (!function_exists('esc_url')) {
        require_once(ABSPATH . 'wp-includes/formatting.php');
    }

    if (!function_exists('checked')) {
        require_once(ABSPATH . 'wp-includes/general-template.php');
    }
}

// Initialize media handling
function kingsland_init_media()
{
    if (is_admin()) {
        wp_enqueue_media();
    }
}


// Add to your main plugin file

// Add WhatsApp Settings Menu
function kingsland_add_whatsapp_menu() {
    add_submenu_page(
        'edit.php?post_type=tour_package',
        'WhatsApp Settings',
        'WhatsApp Settings',
        'manage_options',
        'kingsland-whatsapp-settings',
        'kingsland_whatsapp_settings_page'
    );
}
add_action('admin_menu', 'kingsland_add_whatsapp_menu');

// Register Settings
function kingsland_register_whatsapp_settings() {
    register_setting('kingsland_whatsapp_options', 'kingsland_whatsapp_number');
    
    add_settings_section(
        'kingsland_whatsapp_section',
        'WhatsApp Contact Settings',
        null,
        'kingsland-whatsapp-settings'
    );
    
    add_settings_field(
        'kingsland_whatsapp_number',
        'WhatsApp Number',
        'kingsland_whatsapp_number_callback',
        'kingsland-whatsapp-settings',
        'kingsland_whatsapp_section'
    );
}
add_action('admin_init', 'kingsland_register_whatsapp_settings');

// Settings Field Callback
function kingsland_whatsapp_number_callback() {
    $number = get_option('kingsland_whatsapp_number');
    ?>
    <input type="text" 
           name="kingsland_whatsapp_number" 
           value="<?php echo esc_attr($number); ?>" 
           class="regular-text"
           placeholder="+91XXXXXXXXXX">
    <p class="description">Enter WhatsApp number with country code</p>
    <?php
}

// Settings Page HTML
function kingsland_whatsapp_settings_page() {
    ?>
    <div class="wrap">
        <h1>WhatsApp Contact Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('kingsland_whatsapp_options');
            do_settings_sections('kingsland-whatsapp-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Helper function to get WhatsApp number
function kingsland_get_whatsapp_number() {
    $number = get_option('kingsland_whatsapp_number');
    return preg_replace('/[^0-9]/', '', $number);
}

// Add activation hook to set default number
register_activation_hook(__FILE__, 'kingsland_activate_plugin');
function kingsland_activate_plugin() {
    add_option('kingsland_whatsapp_number', '+916376983416');
}

function register_kingsland_custom_widget($widgets_manager)
{
    require_once(__DIR__ . '/widgets/kingsland-travel-package-widget.php');
    $widgets_manager->register(new \Kingsland_Travel_Package_Widget());
    require_once(__DIR__ . '/widgets/kingsland-grid-widget.php');
    $widgets_manager->register(new \Kingsland_Grid_Widget());
}

// Register Widget Assets
function kingsland_grid_widget_assets()
{
    wp_register_style(
        'kingsland-grid-widget-css',
        plugins_url('assets/css/kingsland-grid-widget.css', __FILE__),
        [],
        '1.0.0'
    );

    wp_register_script(
        'kingsland-grid-widget-js',
        plugins_url('js/kingsland-grid-widget.js', __FILE__),
        ['jquery'],
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'kingsland_grid_widget_assets');
add_action('elementor/widgets/register', 'register_kingsland_custom_widget');

function enqueue_kingsland_styles()
{
    wp_enqueue_style('kingsland-widget-style', plugin_dir_url(__FILE__) . 'assets/css/widget.css');

    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css');

}
add_action('wp_enqueue_scripts', 'enqueue_kingsland_styles');

// Enqueue admin styles
function kingsland_enqueue_admin_styles()
{
    wp_enqueue_style(
        'kingsland-admin-styles', // Handle for the stylesheet
        plugin_dir_url(__FILE__) . 'assets/css/admin.css', // Path to the CSS file
        array(), // Dependencies (if any)
        '1.0.0', // Version number
        'all' // Media type
    );
}
add_action('admin_enqueue_scripts', 'kingsland_enqueue_admin_styles');

// Enqueue admin scripts
function kingsland_enqueue_admin_scripts()
{
    wp_enqueue_script(
        'kingsland-admin-scripts', // Handle for the script
        plugin_dir_url(__FILE__) . 'js/Kings.js', // Path to the JavaScript file
        array('jquery', 'wp-mediaelement'), // Dependencies (if any)
        '1.0.0', // Version number
        true // Load in footer
    );
    wp_enqueue_script(
        'kingsland-gallery-script', // Handle for the gallery script
        plugin_dir_url(__FILE__) . 'js/gallery.js', // Path to the JavaScript file
        array('jquery', 'wp-mediaelement'), // Dependencies (if any)
        '1.0.0', // Version number
        true // Load in footer
    );
    // Enqueue the WordPress media uploader
    if (is_admin()) {
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'kingsland_enqueue_admin_scripts');

// Include tour packages in RSS feed
function kingsland_include_tour_packages_in_rss($query)
{
    if ($query->is_feed() && $query->is_main_query()) {
        $post_types = $query->get('post_type');
        if (empty($post_types)) {
            $post_types = array('post');
        } elseif (!is_array($post_types)) {
            $post_types = array($post_types);
        }
        $post_types[] = 'tour_package';
        $query->set('post_type', $post_types);
    }
}
add_action('pre_get_posts', 'kingsland_include_tour_packages_in_rss');

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include WordPress core functions
require_once(ABSPATH . 'wp-admin/includes/post.php');
require_once(ABSPATH . 'wp-includes/class-wp-query.php');



// Register Custom Post Type for Tour Packages
function kingsland_register_tour_packages()
{
    $labels = array(
        'name' => _x('Tour Packages', 'Post Type General Name', 'kingsland-tour-packages'),
        'singular_name' => _x('Tour Package', 'Post Type Singular Name', 'kingsland-tour-packages'),
        'menu_name' => __('Tour Packages', 'kingsland-tour-packages'),
        'name_admin_bar' => __('Tour Package', 'kingsland-tour-packages'),
        'archives' => __('Item Archives', 'kingsland-tour-packages'),
        'attributes' => __('Item Attributes', 'kingsland-tour-packages'),
        'parent_item_colon' => __('Parent Item:', 'kingsland-tour-packages'),
        'all_items' => __('All Items', 'kingsland-tour-packages'),
        'add_new_item' => __('Add New Item', 'kingsland-tour-packages'),
        'add_new' => __('Add New', 'kingsland-tour-packages'),
        'new_item' => __('New Item', 'kingsland-tour-packages'),
        'edit_item' => __('Edit Item', 'kingsland-tour-packages'),
        'update_item' => __('Update Item', 'kingsland-tour-packages'),
        'view_item' => __('View Item', 'kingsland-tour-packages'),
        'view_items' => __('View Items', 'kingsland-tour-packages'),
        'search_items' => __('Search Item', 'kingsland-tour-packages'),
        'not_found' => __('Not found', 'kingsland-tour-packages'),
        'not_found_in_trash' => __('Not found in Trash', 'kingsland-tour-packages'),
        'featured_image' => __('Featured Image', 'kingsland-tour-packages'),
        'set_featured_image' => __('Set featured image', 'kingsland-tour-packages'),
        'remove_featured_image' => __('Remove featured image', 'kingsland-tour-packages'),
        'use_featured_image' => __('Use as featured image', 'kingsland-tour-packages'),
        'insert_into_item' => __('Insert into item', 'kingsland-tour-packages'),
        'uploaded_to_this_item' => __('Uploaded to this item', 'kingsland-tour-packages'),
        'items_list' => __('Items list', 'kingsland-tour-packages'),
        'items_list_navigation' => __('Items list navigation', 'kingsland-tour-packages'),
        'filter_items_list' => __('Filter items list', 'kingsland-tour-packages'),
    );

    $args = array(
        'label' => __('Tour Package', 'kingsland-tour-packages'),
        'description' => __('Custom post type for tour packages', 'kingsland-tour-packages'),
        'labels' => $labels,
        'supports' => array('title', 'editor', 'thumbnail', 'comments'), // Ensure 'comments' is included
        'taxonomies' => array('category', 'post_tag'),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-palmtree',
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'post',
        'rewrite' => array('slug' => 'tour-packages'),
    );

    register_post_type('tour_package', $args);
}
add_action('init', 'kingsland_register_tour_packages');


// Add settings page
function kingsland_add_settings_page()
{
    add_submenu_page(
        'edit.php?post_type=tour_package', // Parent slug
        'Email Settings',
        'Email Settings',
        'manage_options',
        'kingsland-email-settings',
        'kingsland_render_settings_page'
    );
}
add_action('admin_menu', 'kingsland_add_settings_page');

// Render settings page
function kingsland_render_settings_page()
{
    ?>
    <div class="wrap">
        <!-- how Get User Name OR Password For  -->
        <h2>Gmail SMTP Configuration</h2>
        <p>To use Gmail SMTP, you need to:</p>
        <ol>
            <li>Enable 2-Step Verification in your Google Account</li>
            <li>Generate an App Password for this plugin</li>
            <li>Use your Gmail address as username</li>

        </ol>

        <p>Instructions:</p>
        <ol>
            <li>Go to your Google Account settings</li>
            <li>Search for "App Passwords"</li>
            <li>Generate a new app password for "Mail"</li>
            <li>Copy the 16-character password</li>
        </ol>
        <h1>Tour Package Email Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('kingsland_email_settings_group');
            do_settings_sections('kingsland-email-settings');
            submit_button();
            ?>
        </form>

    </div>
    <?php
}

// Register settings
function kingsland_register_settings()
{
    register_setting('kingsland_email_settings_group', 'kingsland_email_username');
    register_setting('kingsland_email_settings_group', 'kingsland_email_password');

    add_settings_section(
        'kingsland_email_settings_section',
        'Email Credentials',
        null,
        'kingsland-email-settings'
    );

    add_settings_field(
        'kingsland_email_username',
        'Email Username',
        'kingsland_email_username_callback',
        'kingsland-email-settings',
        'kingsland_email_settings_section'
    );

    add_settings_field(
        'kingsland_email_password',
        'Email Password',
        'kingsland_email_password_callback',
        'kingsland-email-settings',
        'kingsland_email_settings_section'
    );
}
add_action('admin_init', 'kingsland_register_settings');

// Callback functions
function kingsland_email_username_callback()
{
    $username = get_option('kingsland_email_username');
    echo '<input type="text" name="kingsland_email_username" value="' . esc_attr($username) . '" class="regular-text">';
}

function kingsland_email_password_callback()
{
    $password = get_option('kingsland_email_password');
    echo '<input type="password" name="kingsland_email_password" value="' . esc_attr($password) . '" class="regular-text">';
}

// Add Meta Boxes for Tour Package Details
function kingsland_add_tour_package_meta_boxes()
{
    add_meta_box('tour_package_details', 'Tour Package Details', 'kingsland_tour_package_details_callback', 'tour_package', 'normal', 'high');

}
add_action('add_meta_boxes', 'kingsland_add_tour_package_meta_boxes');




// Meta Box Callback Function

function kingsland_tour_package_details_callback($post)
{
    // Add nonce for security
    wp_nonce_field('kingsland_tour_package_nonce_action', 'kingsland_tour_package_nonce');
    $gallery_images = get_post_meta($post->ID, '_package_gallery_images', true);

    // Retrieve current meta values
    $fields = [
        'trip_location' => get_post_meta($post->ID, 'trip_location', true),
        'duration' => get_post_meta($post->ID, 'duration', true),
        'hotel_info' => get_post_meta($post->ID, 'hotel_info', true),
        'price' => get_post_meta($post->ID, 'price', true),
        'old_price' => get_post_meta($post->ID, 'old_price', true),
        'highlights' => maybe_unserialize(get_post_meta($post->ID, 'highlights', true)),
        'itinerary' => maybe_unserialize(get_post_meta($post->ID, 'itinerary', true)),
        'hotels' => maybe_unserialize(get_post_meta($post->ID, 'hotels', true)),
        'stay_info' => get_post_meta($post->ID, 'stay_info', true),
        'inclusions' => maybe_unserialize(get_post_meta($post->ID, 'inclusions', true)),
        'exclusions' => maybe_unserialize(get_post_meta($post->ID, 'exclusions', true)),
        'reviews' => get_post_meta($post->ID, 'reviews', true),
        'faqs' => maybe_unserialize(get_post_meta($post->ID, 'faqs', true)),
        'hotel_star' => get_post_meta($post->ID, 'hotel_star', true),
        'services' => maybe_unserialize(get_post_meta($post->ID, 'services', true)),
        'discount' => get_post_meta($post->ID, 'discount', true),
        'destinations_covered' => get_post_meta($post->ID, 'destinations_covered', true), // Corrected line
        'accommodation' => get_post_meta($post->ID, 'accommodation', true), // Added line
        'things_to_do' => get_post_meta($post->ID, 'things_to_do', true),
        'gallery' => maybe_unserialize(get_post_meta($post->ID, 'gallery', true)),
        // Add slideshow fields
        'slideshow_images' => get_post_meta($post->ID, 'slideshow_images', true),
        'slideshow_captions' => get_post_meta($post->ID, 'slideshow_captions', true),
        'slideshow_positions' => get_post_meta($post->ID, 'slideshow_positions', true),
        'destinations' => maybe_unserialize(get_post_meta($post->ID, 'destinations', true)),
    ];

    // Define available services
    $available_services = [
        'guide' => 'Guide',
        'hotel' => 'Hotel',
        'utensils' => 'Utensils',
        'car' => 'Car',
        'sightseeing' => 'Sightseeing',
    ];
    // Convert slideshow arrays if empty
    $fields['slideshow_images'] = is_array($fields['slideshow_images']) ? $fields['slideshow_images'] : array();
    $fields['slideshow_captions'] = is_array($fields['slideshow_captions']) ? $fields['slideshow_captions'] : array();
    $fields['slideshow_positions'] = is_array($fields['slideshow_positions']) ? $fields['slideshow_positions'] : array();

    // Render input fields for the meta box
    ?>

    <div class="Admon-Main">
        <!-- sidebar start -->
        <div class="admon-css-sidebar">
            <ul>
                <li>
                    <a href="#accommodation" class="admon-css-tab-link active" data-tab="accommodation">Package
                        Information</a>
                </li>

                <li>
                    <a href="#gallery" class="admon-css-tab-link" data-tab="gallery">Gallery</a>
                </li>



                <li>
                    <a href="#Inclusions" class="admon-css-tab-link" data-tab="Inclusions">Inc/Exc</a>
                </li>
                <li>
                    <a href="#itinerary" class="admon-css-tab-link" data-tab="itinerary">Itinerary</a>
                </li>

                <li>
                    <a href="#FAQs" class="admon-css-tab-link" data-tab="FAQs">FAQs</a>
                </li>
                <li>
                    <a href="#Hotels" class="admon-css-tab-link" data-tab="Hotels">Hotels</a>
                </li>
                <li>
                    <a href="#destinations" class="admon-css-tab-link" data-tab="destinations">Destinations</a>
                </li>
            </ul>
        </div>
        <!-- sidebar end -->

        <!-- content area -->
        <div class="admon-css-content">

            <div id="accommodation" class="admon-css-tab-content active">
<<<<<<< HEAD
=======
               
>>>>>>> a857882d090fd13b7ad06441edcd6f1b4a080c45
                  <!-- Trip -->
                <label for="trip_location">Cities:</label>
                <input type="text" id="trip_location" name="trip_location"
                    value="<?php echo esc_attr($fields['trip_location']); ?>"
                    style="width: 100%" />

                     <div style="margin-bottom: 10px">
                    <!-- destinations_covered -->
                    <label for="destinations_covered">
                        <strong>Destinations Covered:</strong>
                    </label>
                    <input type="text" name="destinations_covered" id="destinations_covered"
                        value="<?php echo esc_attr($fields['destinations_covered']); ?>" style="width: 100%" />
                    </div>

                <label for="accommodation"><strong>Accommodation:</strong></label>
                <input type="text" name="accommodation" id="accommodation"
                    value="<?php echo esc_attr($fields['accommodation']); ?>" />
                <!-- hotel star -->
                <label for="hotel_star">Hotel Star Rating:</label>
                <select id="hotel_star" name="hotel_star" style="width: 100%;">
                    <option value="1 Star" <?php selected($fields['hotel_star'], '1 Star'); ?>>1 STAR</option>
                    <option value="2 Star" <?php selected($fields['hotel_star'], '2 Star'); ?>>2 STAR</option>
                    <option value="3 Star" <?php selected($fields['hotel_star'], '3 Star'); ?>>3 STAR</option>
                    <option value="4 Star" <?php selected($fields['hotel_star'], '4 Star'); ?>>4 STAR</option>
                    <option value="5 Star" <?php selected($fields['hotel_star'], '5 Star'); ?>>5 STAR </option>
                </select>

                <!-- Things -->
                <label for="things_to_do">Things to do:</label>
                <textarea name="things_to_do"
                    id="things_to_do"><?php echo esc_textarea($fields['things_to_do']); ?>
                                                                                                                                                                                                                                                            </textarea>
              

               
                <!--Duration  -->
                <label for="duration">Duration:</label>
                <input type="text" id="duration" name="duration" value="<?php echo esc_attr($fields['duration']); ?>"
                    style="width: 100%" />
                    <div style="display:flex; justify-content: space-around;">
                <!--price  -->
    <div>
                <label for="price">Price:</label>
                <input type="number" id="price" name="price" value="<?php echo esc_attr($fields['price']); ?>"
                    style="width: 100%" />
</div>
<div>
                <label for="old_price">Old Price:</label>
                <input type="number" id="old_price" name="old_price" value="<?php echo esc_attr($fields['old_price']); ?>"
                    style="width: 100%" />
                </div>
                    </div>
                <label>Services:</label>
<<<<<<< HEAD
                 <!-- Select All Services -->
                <div style="margin-bottom: 10px;">
                        <input type="checkbox" id="select_all_services" 
                         <?php checked(count((array) $fields['services']) === count($available_services)); ?>>
                <label for="select_all_services">Select All Services</label>
                </div>
            
                <div style="display:flex; width: 100%;     justify-content: space-evenly;">
                    <?php foreach ($available_services as $service_key => $service_label): ?>
                        
                        <div>
                            
=======
                <div style="display:flex; width: 100%;     justify-content: space-evenly;">
                    <?php foreach ($available_services as $service_key => $service_label): ?>
                        <div>
>>>>>>> a857882d090fd13b7ad06441edcd6f1b4a080c45
                            <input type="checkbox" id="services_<?php echo esc_attr($service_key); ?>" name="services[]"
                                value="<?php echo esc_attr($service_key); ?>" <?php checked(in_array($service_key, (array) $fields['services'])); ?>>
                            <label
                                for="services_<?php echo esc_attr($service_key); ?>"><?php echo esc_html($service_label); ?></label>
                        </div>
                    <?php endforeach; ?>
                    
                </div>
<<<<<<< HEAD
               

                <script>
                jQuery(document).ready(function($) {
                    // Select All functionality
                    $('#select_all_services').on('change', function() {
                        var isChecked = $(this).prop('checked');
                        $('input[name="services[]"]').prop('checked', isChecked);
                    });

                    // Update Select All when individual checkboxes change
                    $('input[name="services[]"]').on('change', function() {
                        var totalServices = $('input[name="services[]"]').length;
                        var checkedServices = $('input[name="services[]"]:checked').length;
                        $('#select_all_services').prop('checked', totalServices === checkedServices);
                    });
                });
                </script>
=======
>>>>>>> a857882d090fd13b7ad06441edcd6f1b4a080c45
                <label for="highlights">Highlights (comma-separated):</label>
                
                <input type="text" id="highlights" name="highlights" value="<?php echo esc_attr($fields['highlights']); ?>"
                    style="width: 100%" />
            </div>
<<<<<<< HEAD

            <div id="gallery" class="admon-css-tab-content">
                <!-- Slideshow Section -->
                <div class="slideshow-meta-section">
                    <h4>Slideshow Images</h4>
                    <div id="slideshow-items">
                        <?php foreach ($fields['slideshow_images'] as $index => $image): ?>
                            <div class="slideshow-item"
                                style="margin-bottom: 20px; padding: 10px; border: 1px solid #ddd; display:flex; gap:20px">
                                <img src="<?php echo esc_url($image); ?>" alt="Slideshow Image"
                                    style="max-width: 25%; height: 25%; margin-top: 10px;">
                                <p>
                                    <label>Caption:</label><br>
                                    <input type="text" name="slideshow_captions[]"
                                        value="<?php echo esc_attr(isset($fields['slideshow_captions'][$index]) ? $fields['slideshow_captions'][$index] : ''); ?>"
                                        style="width: 100%;">
                                    <!-- <label>Image URL:</label><br> -->
                                    <!-- uploaded img show with -->

                                    <input type="hidden" name="slideshow_images[]" value="<?php echo esc_attr($image); ?>">
                                    <button type="button" class="upload-image button" style="margin-top: 5px;">Upload
                                        Image</button>
                                    <button type="button" class="remove-slide button"
                                        style="width: 100px;height: 20px;    margin-left: 5px;    margin-top: 5px;">Remove
                                        Slide</button>
                                </p>
                                <p>
                                    <label>Position:</label><br>
                                    <select name="slideshow_positions[]">

                                        <option value="top-left" <?php selected(isset($fields['slideshow_positions'][$index]) ? $fields['slideshow_positions'][$index] : '', 'top-left'); ?>>Top Left</option>
                                        <option value="top-right" <?php selected(isset($fields['slideshow_positions'][$index]) ? $fields['slideshow_positions'][$index] : '', 'top-right'); ?>>Top Right</option>
                                        <option value="bottom-left" <?php selected(isset($fields['slideshow_positions'][$index]) ? $fields['slideshow_positions'][$index] : '', 'bottom-left'); ?>>Bottom Left
                                        </option>
                                        <option value="bottom-right" <?php selected(isset($fields['slideshow_positions'][$index]) ? $fields['slideshow_positions'][$index] : '', 'bottom-right'); ?>>Bottom Right
                                        </option>
                                        <option value="middle" <?php selected(isset($fields['slideshow_positions'][$index]) ? $fields['slideshow_positions'][$index] : '', 'middle'); ?>>Middle</option>
                                    </select>

                                </p>

                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" id="add-slide" class="button">Add New Slide</button>
                </div>
                <script>
                    jQuery(document).ready(function ($) {
                        // Add new slide
                        $('#add-slide').click(function () {
                            var newSlide = `
<div class="slideshow-item" style="margin-bottom: 20px; padding: 10px; border: 1px solid #ddd; display:flex; gap:20px">
<img src="" alt="Slideshow Image" style="max-width: 25%; height: 25%; margin-top: 10px;">
<p>
<label>Caption:</label><br>
<input type="text" name="slideshow_captions[]" value="" style="width: 100%;">
                             
<input type="hidden" name="slideshow_images[]" value="">
<button type="button" class="upload-image button" style="margin-top: 5px;">Upload Image</button>
<button type="button" class="remove-slide button" style="width: 100px;height: 20px;margin-left: 5px;margin-top: 5px;">Remove Slide</button>
</p>
<p>
<label>Position:</label><br>
<select name="slideshow_positions[]">
<option value=" ">Top Left</option>
<option value="top-right">Top Right</option>
<option value="bottom-left">Bottom Left</option>
<option value="bottom-right">Bottom Right</option>
<option value="middle">Middle</option>
</select>
</p>
</div>`;
                            $('#slideshow-items').append(newSlide);
                        });

                        // Remove slide
                        $(document).on('click', '.remove-slide', function () {
                            $(this).closest('.slideshow-item').remove();
                        });

                        // Image upload
                        $(document).on('click', '.upload-image', function (e) {
                            e.preventDefault();
                            var button = $(this);
                            var imageInput = button.prev('input');
                            var previewImage = button.closest('.slideshow-item').find('img');

=======

            <div id="gallery" class="admon-css-tab-content">
                <!-- Slideshow Section -->
                <div class="slideshow-meta-section">
                    <h4>Slideshow Images</h4>
                    <div id="slideshow-items">
                        <?php foreach ($fields['slideshow_images'] as $index => $image): ?>
                            <div class="slideshow-item"
                                style="margin-bottom: 20px; padding: 10px; border: 1px solid #ddd; display:flex; gap:20px">
                                <img src="<?php echo esc_url($image); ?>" alt="Slideshow Image"
                                    style="max-width: 25%; height: 25%; margin-top: 10px;">
                                <p>
                                    <label>Caption:</label><br>
                                    <input type="text" name="slideshow_captions[]"
                                        value="<?php echo esc_attr(isset($fields['slideshow_captions'][$index]) ? $fields['slideshow_captions'][$index] : ''); ?>"
                                        style="width: 100%;">
                                    <!-- <label>Image URL:</label><br> -->
                                    <!-- uploaded img show with -->

                                    <input type="hidden" name="slideshow_images[]" value="<?php echo esc_attr($image); ?>">
                                    <button type="button" class="upload-image button" style="margin-top: 5px;">Upload
                                        Image</button>
                                    <button type="button" class="remove-slide button"
                                        style="width: 100px;height: 20px;    margin-left: 5px;    margin-top: 5px;">Remove
                                        Slide</button>
                                </p>
                                <p>
                                    <label>Position:</label><br>
                                    <select name="slideshow_positions[]">

                                        <option value="top-left" <?php selected(isset($fields['slideshow_positions'][$index]) ? $fields['slideshow_positions'][$index] : '', 'top-left'); ?>>Top Left</option>
                                        <option value="top-right" <?php selected(isset($fields['slideshow_positions'][$index]) ? $fields['slideshow_positions'][$index] : '', 'top-right'); ?>>Top Right</option>
                                        <option value="bottom-left" <?php selected(isset($fields['slideshow_positions'][$index]) ? $fields['slideshow_positions'][$index] : '', 'bottom-left'); ?>>Bottom Left
                                        </option>
                                        <option value="bottom-right" <?php selected(isset($fields['slideshow_positions'][$index]) ? $fields['slideshow_positions'][$index] : '', 'bottom-right'); ?>>Bottom Right
                                        </option>
                                        <option value="middle" <?php selected(isset($fields['slideshow_positions'][$index]) ? $fields['slideshow_positions'][$index] : '', 'middle'); ?>>Middle</option>
                                    </select>

                                </p>

                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" id="add-slide" class="button">Add New Slide</button>
                </div>
                <script>
                    jQuery(document).ready(function ($) {
                        // Add new slide
                        $('#add-slide').click(function () {
                            var newSlide = `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="slideshow-item" style="margin-bottom: 20px; padding: 10px; border: 1px solid #ddd; display:flex; gap:20px">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <img src="" alt="Slideshow Image" style="max-width: 25%; height: 25%; margin-top: 10px;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <p>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <label>Caption:</label><br>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <input type="text" name="slideshow_captions[]" value="" style="width: 100%;">
                             
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <input type="hidden" name="slideshow_images[]" value="">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <button type="button" class="upload-image button" style="margin-top: 5px;">Upload Image</button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <button type="button" class="remove-slide button" style="width: 100px;height: 20px;margin-left: 5px;margin-top: 5px;">Remove Slide</button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </p>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <p>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <label>Position:</label><br>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <select name="slideshow_positions[]">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value=" ">Top Left</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="top-right">Top Right</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="bottom-left">Bottom Left</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="bottom-right">Bottom Right</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="middle">Middle</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </select>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </p>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>`;
                            $('#slideshow-items').append(newSlide);
                        });

                        // Remove slide
                        $(document).on('click', '.remove-slide', function () {
                            $(this).closest('.slideshow-item').remove();
                        });

                        // Image upload
                        $(document).on('click', '.upload-image', function (e) {
                            e.preventDefault();
                            var button = $(this);
                            var imageInput = button.prev('input');
                            var previewImage = button.closest('.slideshow-item').find('img');

>>>>>>> a857882d090fd13b7ad06441edcd6f1b4a080c45
                            var frame = wp.media({
                                title: 'Select or Upload Image',
                                button: {
                                    text: 'Use this image'
                                },
                                multiple: false
                            });

                            frame.on('select', function () {
                                var attachment = frame.state().get('selection').first().toJSON();
                                imageInput.val(attachment.url);
                                previewImage.attr('src', attachment.url); // Update the preview image
                            });

                            frame.open();
                        });
                    });
                </script>
            </div>

            <div id="Inclusions" class="admon-css-tab-content">
                <div style="display:flex;">
                    <div class="width-inc-exc">
                        <label>Inclusions:</label>
                        <div id="inclusions-repeater">
                            <?php
                            if (!empty($fields['inclusions']) && is_array($fields['inclusions'])) {
                                foreach ($fields['inclusions'] as $index => $inclusion) { ?>
                                    <div class="inclusion-item" style="margin-bottom: 10px">
                                        <input type="text" name="inclusions[<?php echo $index; ?>]"
                                            value="<?php echo esc_attr($inclusion); ?>" style="width: 90%" />
                                        <button type="button" class="remove-inclusion-btn">Remove</button>
                                    </div>
                                    <?php
                                }
                            } else {
                                // Default input field if no inclusions exist
                                ?>
                                <div class="inclusion-item" style="margin-bottom: 10px">
                                    <input type="text" name="inclusions[0]" style="width: 90%" />
                                    <button type="button" class="remove-inclusion-btn">Remove</button>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <button type="button" id="add-inclusion-btn">Add Inclusion</button>
                    </div>
                    <div>
                        <label>Exclusions:</label>
                        <div id="exclusions-repeater">
                            <?php
                            if (!empty($fields['exclusions']) && is_array($fields['exclusions'])) {
                                foreach ($fields['exclusions'] as $index => $exclusion) { ?>
                                    <div class="exclusion-item" style="margin-bottom: 10px">
                                        <input type="text" name="exclusions[<?php echo $index; ?>]"
                                            value="<?php echo esc_attr($exclusion); ?>" style="width: 90%" />
                                        <button type="button" class="remove-exclusion-btn">Remove</button>
                                    </div>
                                    <?php
                                }
                            } else {
                                // Default input field if no exclusions exist
                                ?>
                                <div class="exclusion-item" style="margin-bottom: 10px">
                                    <input type="text" name="exclusions[0]" style="width: 90%" />
                                    <button type="button" class="remove-exclusion-btn">Remove</button>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <button type="button" id="add-exclusion-btn">Add Exclusion</button>
                    </div>
                </div>
            </div>

            <div id="itinerary" class="admon-css-tab-content">

                <label for="itinerary">Itinerary:</label>
<<<<<<< HEAD
                <div class="itinerary-drag-drop">
                    <div id="itinerary-repeater">
=======
                <div id="itinerary-repeater">
>>>>>>> a857882d090fd13b7ad06441edcd6f1b4a080c45
                    <?php
                    if (!empty($fields['itinerary']) && is_array($fields['itinerary'])) {
                        foreach ($fields['itinerary'] as $index => $day) {
                            $day_title = isset($day['day_title']) ? $day['day_title'] : '';
                            $day_label = isset($day['day_label']) ? $day['day_label'] : '';
                            // Convert tags array back to comma-separated string for display
                            $day_tags = isset($day['day_tags']) ? implode(', ', $day['day_tags']) : '';
                            ?>
                            <div class="itinerary-item" style="margin-bottom: 10px">
<<<<<<< HEAD
                                <span class="drag-handle">⋮⋮</span>
=======
>>>>>>> a857882d090fd13b7ad06441edcd6f1b4a080c45
                                <input type="text" name="itinerary[<?php echo $index; ?>][day_title]" placeholder="Day Title"
                                    value="<?php echo esc_attr($day_title); ?>" style="width: 100%; margin-bottom: 5px;" />

                                <input type="text" name="itinerary[<?php echo $index; ?>][day_tags]"
                                    placeholder="Day Tags (comma-separated)" value="<?php echo esc_attr($day_tags); ?>"
                                    style="width: 100%; margin-bottom: 5px;" />

                                <textarea name="itinerary[<?php echo $index; ?>][day_label]" placeholder="Day Activities"
                                    style="width: 100%; margin-bottom: 5px;"><?php echo esc_textarea($day_label); ?></textarea>

                                <button type="button" class="remove-itinerary-btn button">Remove Day</button>
                            </div>
                            <?php
                        }
                    } else {
                        // Default empty fields if no itinerary exists
                        ?>
                        <div class="itinerary-item" style="margin-bottom: 10px">
<<<<<<< HEAD
                            <span class="drag-handle">⋮⋮</span>
=======
>>>>>>> a857882d090fd13b7ad06441edcd6f1b4a080c45
                            <input type="text" name="itinerary[0][day_title]" placeholder="Day Title" value=""
                                style="width: 100%; margin-bottom: 5px;" />

                            <input type="text" name="itinerary[0][day_tags]" placeholder="Day Tags (comma-separated)" value=""
                                style="width: 100%; margin-bottom: 5px;" />

                            <textarea name="itinerary[0][day_label]" placeholder="Day Activities"
                                style="width: 100%; margin-bottom: 5px;"></textarea>

                            <button type="button" class="remove-itinerary-btn button">Remove Day</button>
                        </div>
                        <?php
                    }
                    ?>
<<<<<<< HEAD
                    </div>
                        <button type="button" id="add-itinerary-btn">Add Itinerary Day</button>
                </div>
            </div>
            <style>
.itinerary-drag-drop {
    padding: 20px;
    background: #fff;
    border-radius: 8px;
}

.itinerary-item {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    padding: 15px 15px 15px 35px;
    margin-bottom: 10px;
    position: relative;
    transition: all 0.3s ease;
    border-radius: 4px;
}

.itinerary-item:hover {
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.drag-handle {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: move;
    color: #6c757d;
    font-size: 20px;
}

.ui-sortable-helper {
    background: #fff;
    box-shadow: 0 5px 15px rgba(0,0,0,0.15);
}

.ui-sortable-placeholder {
    border: 2px dashed #ced4da;
    visibility: visible !important;
    height: 100px;
    background: #f8f9fa;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Add drag handles to items
    $('.itinerary-item').prepend('<span class="drag-handle">⋮⋮</span>');

    // Initialize sortable
    $('#itinerary-repeater').sortable({
        handle: '.drag-handle',
        placeholder: 'ui-sortable-placeholder',
        start: function(e, ui) {
            ui.placeholder.height(ui.item.height());
        },
        update: function() {
            updateIndices();
        }
    });

    // Add new itinerary day
    $('#add-itinerary-btn').on('click', function() {
        const index = $('.itinerary-item').length;
        const newItem = `
            <div class="itinerary-item">
                <span class="drag-handle">⋮⋮</span>
                <input type="text" name="itinerary[${index}][day_title]" 
                       placeholder="Day Title" value="" 
                       style="width: 100%; margin-bottom: 5px;" />
                <input type="text" name="itinerary[${index}][day_tags]" 
                       placeholder="Day Tags (comma-separated)" value="" 
                       style="width: 100%; margin-bottom: 5px;" />
                <textarea name="itinerary[${index}][day_label]" 
                         placeholder="Day Activities"
                         style="width: 100%; margin-bottom: 5px;"></textarea>
                <button type="button" class="remove-itinerary-btn button">Remove Day</button>
            </div>`;
        $('#itinerary-repeater').append(newItem);
    });

    // Remove itinerary day
    $(document).on('click', '.remove-itinerary-btn', function() {
        $(this).closest('.itinerary-item').fadeOut(300, function() {
            $(this).remove();
            updateIndices();
        });
    });

    // Update indices after reordering
    function updateIndices() {
        $('.itinerary-item').each(function(idx) {
            $(this).find('input, textarea').each(function() {
                const name = $(this).attr('name');
                if (name) {
                    $(this).attr('name', name.replace(/\[\d+\]/, `[${idx}]`));
                }
            });
        });
    }
});
</script>
            <!-- write css with js for itinerary-drag-drop -->

=======
                </div>
                <button type="button" id="add-itinerary-btn">Add Itinerary Day</button>
            </div>

>>>>>>> a857882d090fd13b7ad06441edcd6f1b4a080c45
            <div id="FAQs" class="admon-css-tab-content">

                <label>FAQs:</label>
                <div id="faq-repeater">
                    <?php
                    // Check if FAQs exist and populate them
                    $faqs = maybe_unserialize(get_post_meta($post->ID, 'faqs', true));
                    if (is_array($faqs) && !empty($faqs)) {
                        foreach ($faqs as $index => $faq) { ?>
                            <div class="faq-item" style="margin-bottom: 10px">
                                <input type="text" name="faqs[<?php echo $index; ?>][question]" placeholder="Question"
                                    value="<?php echo esc_attr($faq['question']); ?>" style="width: 48%; margin-right: 2%" />
                                <input type="text" name="faqs[<?php echo $index; ?>][answer]" placeholder="Answer"
                                    value="<?php echo esc_attr($faq['answer']); ?>" style="width: 48%" />
                            </div>
                            <?php
                        }
                    } else {
                        // Default input fields if no FAQs exist
                        ?>
                        <div class="faq-item" style="margin-bottom: 10px">
                            <input type="text" name="faqs[0][question]" placeholder="Question" class="margin-right-2"
                                style="width: 48%;" />
                            <input type="text" name="faqs[0][answer]" placeholder="Answer" style="width: 48%" />
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <button type="button" id="add-faq-btn">Add FAQ</button>
            </div>
            <div id="Hotels" class="admon-css-tab-content">
<<<<<<< HEAD
<!-- Published hotels list -->
           <div class="saved-hotels">
    <h3 style="text-align: center;">Saved Hotels from Published Posts</h3>
    <div class="saved-hotels-list" style="display: flex; flex-wrap: wrap; gap: 10px; justify-content: center;">
        <?php
            // Query all published tour packages except current one
            $args = array(
                'post_type' => 'tour_package',
                'posts_per_page' => -1,
                'post__not_in' => array($post->ID),
                'post_status' => 'publish'
            );

            $hotels_query = new WP_Query($args);
            $shown_hotels = array();

            if ($hotels_query->have_posts()):
                while ($hotels_query->have_posts()):
                    $hotels_query->the_post();
                    $hotels = maybe_unserialize(get_post_meta(get_the_ID(), 'hotels', true));

                    if (!empty($hotels) && is_array($hotels)):
                        foreach ($hotels as $hotel):
                            // Skip if hotel name already shown or missing required data
                            if (empty($hotel['name']) || in_array($hotel['name'], $shown_hotels)) {
                                continue;
                            }

                            $shown_hotels[] = $hotel['name'];
                            ?>
                        <div class="saved-hotel-item" style="width: 200px; margin-bottom: 15px; border: 1px solid #ddd; padding: 10px;">
                            <?php if (!empty($hotel['image'])): ?>
                                <img src="<?php echo esc_url($hotel['image']); ?>" alt="<?php echo esc_attr($hotel['name']); ?>"
                                    style="width: 100%; height: 150px; object-fit: cover;" />
                            <?php endif; ?>

                            <div style="padding: 5px;">
                                <strong><?php echo esc_html($hotel['name']); ?></strong><br>
                                <small><?php echo esc_html($hotel['address']); ?></small><br>
                                <span><?php echo esc_html($hotel['rating']); ?> STAR</span>


                            </div>
                            <button type="button" class="button add-hotel-from-saved"
                                data-name="<?php echo esc_attr($hotel['name']); ?>"
                                data-address="<?php echo esc_attr($hotel['address']); ?>"
                                data-rating="<?php echo esc_attr($hotel['rating']); ?>"
                                data-image="<?php echo esc_attr($hotel['image']); ?>">
                                Add Hotel
                            </button>
                        </div>
                        <?php
                        endforeach;
                    endif;
                endwhile;
                wp_reset_postdata();
            else:
                echo '<p>No saved hotels found in published posts.</p>';
            endif;
            ?>
    </div>
           </div>

<script>
    jQuery(document).ready(function ($) {
        $('.add-hotel-from-saved').on('click', function () {
            var hotelIndex = $('#hotels-repeater .hotel-item').length;
            var hotelData = {
                name: $(this).data('name'),
                address: $(this).data('address'),
                rating: $(this).data('rating'),
                image: $(this).data('image')
            };

            var html = `
            <div class="hotel-item" style="margin-bottom: 10px; display:flex; gap: 7px; align-items: stretch;">
                <div>
                    <img src="${hotelData.image}" style="width: 150px; height: 150px; margin-top: 10px;" alt="Hotel Image">
                    <div style="display:flex; justify-content: space-evenly;">
                        <button type="button" class="upload-image-btn" data-target="hotels[${hotelIndex}][image]" style="width: 72px; height: 47px; padding:0;">Upload Image</button>
                        <button type="button" class="remove-hotel-btn" style="width: 72px; height: 47px; padding:0;">Remove</button>
                    </div>
                </div>
                <div style="display:inline">
                    <input type="text" name="hotels[${hotelIndex}][name]" value="${hotelData.name}" />
                    <input type="text" name="hotels[${hotelIndex}][address]" value="${hotelData.address}" />
                    <p class="note">Note: Write city in 2nd Last in Address Section</p>
                </div>
                <input type="hidden" name="hotels[${hotelIndex}][image]" value="${hotelData.image}" />
                <select name="hotels[${hotelIndex}][rating]" style="width: 20%; height:10%">
                    ${[1, 2, 3, 4, 5].map(stars => `
                        <option value="${stars}" ${stars == hotelData.rating ? 'selected' : ''}>${stars} Star${stars > 1 ? 's' : ''}</option>
                    `).join('')}
                </select>
            </div>`;

            $('#hotels-repeater').append(html);
        });
    });
</script>
=======

>>>>>>> a857882d090fd13b7ad06441edcd6f1b4a080c45
                <label>Hotels:</label>
                <div id="hotels-repeater">
                    <?php
                    if (!empty($fields['hotels']) && is_array($fields['hotels'])) {
                        foreach ($fields['hotels'] as $index => $hotel) {
                            ?>
                            <div class="hotel-item"
                                style="margin-bottom: 10px; display:flex; gap: 7px;    align-items: stretch</div>;">
                                <!-- uploaded img show with -->
                                <div>
                                    <img src="<?php echo esc_url($hotel['image']); ?>" style="width: 150px; height: 150px; margin-top: 10px;" alt="Hotel Image">
                                    <div style="display:flex;    display: fle</div>x;
    justify-content: space-evenly;">
                                        <button type="button" class="upload-image-btn"
                                            data-target="hotels[<?php echo $index; ?>][image]" style="width: 72px; 
    height: 47px;  padding:0;">Upload
                                            Image</button>
                                        <button type="button" class="remove-hotel-btn" style="width: 72px;
    height: 47px; padding:0;">Remove</button>
                                    </div>
                                </div>

                                <div style="display:inline">
                                    <input type="text" name="hotels[<?php echo $index; ?>][name]" placeholder="Hotel Name"
                                        value="<?php echo esc_attr($hotel['name']); ?>" />
                                    <input type="text" name="hotels[<?php echo $index; ?>][address]" placeholder="Hotel Address"
                                        value="<?php echo esc_attr($hotel['address']); ?>" />
                                    <p class="note">
                                        Note: Write city in 2nd Last in Address Section
                                    </p>
                                </div>

                                <input type="hidden" name="hotels[<?php echo $index; ?>][image]"
                                    value="<?php echo esc_attr($hotel['image']); ?>" />
                                <select name="hotels[<?php echo $index; ?>][rating]" style="width: 20%; height:10%">
<<<<<<< HEAD
                                    <option value="1" <?php selected($hotel['rating'], '1'); ?>>1 STAR</option>
                                    <option value="2" <?php selected($hotel['rating'], '2'); ?>>2 STAR</option>
                                    <option value="3" <?php selected($hotel['rating'], '3'); ?>>3 STAR</option>
                                    <option value="4" <?php selected($hotel['rating'], '4'); ?>>4 STAR</option>
                                    <option value="5" <?php selected($hotel['rating'], '5'); ?>>5 STAR</option>
=======
                                    <option value="1" <?php selected($hotel['rating'], '1'); ?>>1 Star</option>
                                    <option value="2" <?php selected($hotel['rating'], '2'); ?>>2 Stars</option>
                                    <option value="3" <?php selected($hotel['rating'], '3'); ?>>3 Stars</option>
                                    <option value="4" <?php selected($hotel['rating'], '4'); ?>>4 Stars</option>
                                    <option value="5" <?php selected($hotel['rating'], '5'); ?>>5 Stars</option>
>>>>>>> a857882d090fd13b7ad06441edcd6f1b4a080c45
                                </select>
                            </div>
                            <?php
                        }
                    } else {
                        // Default input field if no hotels exist
                        ?>
                        <?php $hotel = array('image' => ''); ?>
                        <div class="hotel-item" style="margin-bottom: 10px; display:flex; gap: 7px; align-items: stretch;">
                            <!-- uploaded img show with -->
                            <div>
                                <img src="" alt="Hotel Image" style="width: 150px; height: 150px; margin-top: 10px;">
                                <div style="display:flex; justify-content: space-evenly;">
                                    <button type="button" class="upload-image-btn" data-target="hotels[0][image]"
                                        style="width: 72px; height: 47px; padding:0;">Upload Image</button>
                                    <button type="button" class="remove-hotel-btn"
                                        style="width: 72px; height: 47px; padding:0;">Remove</button>
                                </div>
                            </div>

                            <div style="display:inline">
                                <input type="text" name="hotels[0][name]" placeholder="Hotel Name" />
                                <input type="text" name="hotels[0][address]" placeholder="Hotel Address" />
                            </div>
                            <input type="hidden" name="hotels[0][image]" value="" />
                            <select name="hotels[0][rating]" style="width: 20%; height:10%">
                                <option value="1">1 Star</option>
                                <option value="2">2 Stars</option>
                                <option value="3">3 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="5">5 Stars</option>
                            </select>
                        </div>
                        <?php
                    }
                    ?>
                </div>
<<<<<<< HEAD
                  <button type="button" id="add-hotel-btn">Add Hotel</button>
            </div>

            <div id="destinations" class="admon-css-tab-content">
                 <!-- Saved Destinations Section -->
                  <div class="saved-destinations">
=======
                <button type="button" id="add-hotel-btn">Add Hotel</button>
            </div>

            <div id="destinations" class="admon-css-tab-content">



                <div class="destinations-container">
                    <label><strong>Destinations:</strong></label>

                    <div id="destinations-wrapper">
                        <?php if ($fields['destinations'] && is_array($fields['destinations'])):
                            foreach ($fields['destinations'] as $index => $destination):
                                ?>
                                <div class="destination-input-group">
                                    <div class="destinations-flex">
                                        <div class="image-preview">
                                            <?php if (!empty($destination['image'])): ?>
                                                <img src="<?php echo esc_url($destination['image']); ?>" />
                                            <?php endif; ?>
                                        </div>
                                        <div style="width:78%">
                                            <input type="text" name="destination[<?php echo $index; ?>][name]"
                                                placeholder="Destination Name"
                                                value="<?php echo esc_attr($destination['name']); ?>" />

                                            <input type="text" name="destination[<?php echo $index; ?>][destination_url]"
                                                placeholder="Destination URL"
                                                value="<?php echo esc_attr($destination['destination_url']); ?>" />
                                        </div>
                                    </div>
                                    <div class="des-button">
                                        <input type="hidden" name="destination[<?php echo $index; ?>][image]"
                                            value="<?php echo esc_attr($destination['image']); ?>"
                                            class="destination-image-input" />

                                        <button type="button" class="upload-destination-image">Upload Image</button>
                                        <button type="button" class="remove-destination">Remove</button>
                                    </div>
                                </div>
                            <?php endforeach;
                        else: ?>
                            <div class="destination-input-group">
                                <div class="destinations-flex">
                                    <div class="image-preview">
                                        <img src="" style="display: none;">
                                    </div>
                                    <div class="destination-fields" style="width: 78%;">
                                        <input type="text" 
                                            name="destination[${destinationIndex}][name]" 
                                            placeholder="Destination Name" 
                                            value="" 
                                            class="destination-name-input" />
                                            
                                        <input type="text" 
                                            name="destination[${destinationIndex}][destination_url]" 
                                            placeholder="Destination URL" 
                                            value="" 
                                            class="destination-url-input" />
                                            
                                        <input type="hidden" 
                                            name="destination[${destinationIndex}][image]" 
                                            value="" 
                                            class="destination-image-input" />
                                    </div>
                                </div>
                                <div class="des-button">
                                    <button type="button" class="upload-destination-image">Upload Image</button>
                                    <button type="button" class="remove-destination">Remove</button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <button type="button" id="add-destination">Add Destination</button>
                </div>

                <!-- Saved Destinations Section -->
<div class="saved-destinations">
>>>>>>> a857882d090fd13b7ad06441edcd6f1b4a080c45
    <h3 style="text-align: center;">Saved Destinations</h3>
    <div style="display: flex; flex-wrap: wrap; gap: 10px; justify-content: center;">
        <?php
        // Debug output
        error_log('Current Post ID: ' . $post->ID);
        
        // Get current post destinations
        $current_destinations = get_post_meta($post->ID, 'destinations', true);
        error_log('Current Destinations: ' . print_r($current_destinations, true));
        
        $current_dest_names = array();
        if (!empty($current_destinations) && is_array($current_destinations)) {
            $current_dest_names = array_map(function ($dest) {
                return isset($dest['name']) ? $dest['name'] : '';
            }, $current_destinations);
        }

        // Query all tour packages
        $args = array(
            'post_type' => 'tour_package',
            'posts_per_page' => -1,
            'post__not_in' => array($post->ID),
            'post_status' => 'publish'
        );

        $destinations_query = new WP_Query($args);
        $shown_destinations = array();

        if ($destinations_query->have_posts()) :
            while ($destinations_query->have_posts()) : $destinations_query->the_post();
                $destinations = get_post_meta(get_the_ID(), 'destinations', true);
                
                if (!empty($destinations) && is_array($destinations)) :
                    foreach ($destinations as $destination) :
                        if (empty($destination['name']) || in_array($destination['name'], $shown_destinations)) {
                            continue;
                        }

                        $shown_destinations[] = $destination['name'];
                        $is_checked = in_array($destination['name'], $current_dest_names);
                        ?>
                        <div class="saved-destination-item" style="width: 200px; margin-bottom: 15px;">
                            <?php if (!empty($destination['image'])) : ?>
                                <img src="<?php echo esc_url($destination['image']); ?>" 
                                     alt="<?php echo esc_attr($destination['name']); ?>" 
                                     style="width: 100%; height: 150px; object-fit: cover;" />
                            <?php endif; ?>
                            
                            <div style="display: flex; gap: 5px; align-items: center; justify-content: space-between; padding: 5px;">
                                <span><?php echo esc_html($destination['name']); ?></span>
                                <input type="checkbox" 
                                       class="destination-checkbox" 
                                       data-name="<?php echo esc_attr($destination['name']); ?>"
                                       data-url="<?php echo esc_attr($destination['destination_url']); ?>"
                                       data-image="<?php echo esc_attr($destination['image']); ?>"
                                       <?php echo $is_checked ? 'checked' : ''; ?> />
                            </div>
                        </div>
                        <?php
                    endforeach;
                endif;
            endwhile;
            wp_reset_postdata();
        endif;
        ?>
    </div>
<<<<<<< HEAD
                  </div>
                <div class="destinations-container">
                    <label>Destinations:</label>

                    <div id="destinations-wrapper">
                        <?php if ($fields['destinations'] && is_array($fields['destinations'])):
                            foreach ($fields['destinations'] as $index => $destination):
                                ?>
                                <div class="destination-input-group">
                                    <div class="destinations-flex">
                                        <div class="image-preview">
                                            <?php if (!empty($destination['image'])): ?>
                                                <img src="<?php echo esc_url($destination['image']); ?>" />
                                            <?php endif; ?>
                                        </div>
                                        <div style="width:78%">
                                            <input type="text" name="destination[<?php echo $index; ?>][name]"
                                                placeholder="Destination Name"
                                                value="<?php echo esc_attr($destination['name']); ?>" />

                                            <input type="text" name="destination[<?php echo $index; ?>][destination_url]"
                                                placeholder="Destination URL"
                                                value="<?php echo esc_attr($destination['destination_url']); ?>" />
                                        </div>
                                    </div>
                                    <div class="des-button">
                                        <input type="hidden" name="destination[<?php echo $index; ?>][image]"
                                            value="<?php echo esc_attr($destination['image']); ?>"
                                            class="destination-image-input" />

                                        <button type="button" class="upload-destination-image">Upload Image</button>
                                        <button type="button" class="remove-destination">Remove</button>
                                    </div>
                                </div>
                            <?php endforeach;
                        else: ?>
                            <div class="destination-input-group">
                                <div class="destinations-flex">
                                    <div class="image-preview">
                                        <img src="" style="display: none;">
                                    </div>
                                    <div class="destination-fields" style="width: 78%;">
                                        <input type="text" 
                                            name="destination[${destinationIndex}][name]" 
                                            placeholder="Destination Name" 
                                            value="" 
                                            class="destination-name-input" />
                                            
                                        <input type="text" 
                                            name="destination[${destinationIndex}][destination_url]" 
                                            placeholder="Destination URL" 
                                            value="" 
                                            class="destination-url-input" />
                                            
                                        <input type="hidden" 
                                            name="destination[${destinationIndex}][image]" 
                                            value="" 
                                            class="destination-image-input" />
                                    </div>
                                </div>
                                <div class="des-button">
                                    <button type="button" class="upload-destination-image">Upload Image</button>
                                    <button type="button" class="remove-destination">Remove</button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <button type="button" id="add-destination">Add Destination</button>
                </div>

              
            </div>
    </div>
    <script>
jQuery(document).ready(function($) {
    // Debug log
    // console.log('Initializing destination checkboxes');
=======
</div>

<script>
jQuery(document).ready(function($) {
    // Debug log
    console.log('Initializing destination checkboxes');
>>>>>>> a857882d090fd13b7ad06441edcd6f1b4a080c45
    
    $('.destination-checkbox').on('change', function() {
        var $this = $(this);
        var name = $this.data('name');
        var url = $this.data('url');
        var image = $this.data('image');
        
<<<<<<< HEAD
        // console.log('Checkbox changed:', name, this.checked);
=======
        console.log('Checkbox changed:', name, this.checked);
>>>>>>> a857882d090fd13b7ad06441edcd6f1b4a080c45

        if (this.checked) {
            addDestination(name, url, image);
        } else {
            removeDestination(name);
        }
    });

    function addDestination(name, url, image) {
        var destIndex = $('#destinations-wrapper .destination-input-group').length;
        
        if (!$('#destinations-wrapper').find(`input[value="${name}"]`).length) {
            var html = `
                <div class="destination-input-group" data-name="${name}">
                    <div class="destinations-flex">
                        <div class="image-preview">
                            <img src="${image}" style="display: block;">
                        </div>
                        <div style="width:78%">
                            <input type="text" name="destination[${destIndex}][name]" 
                                value="${name}" />
                            <input type="text" name="destination[${destIndex}][destination_url]"
                                value="${url}" />
                            <input type="hidden" name="destination[${destIndex}][image]"
                                value="${image}" class="destination-image-input" />
                        </div>
                    </div>
                    <div class="des-button">
                        <button type="button" class="upload-destination-image">Upload Image</button>
                        <button type="button" class="remove-destination">Remove</button>
                    </div>
                </div>`;

            $('#destinations-wrapper').append(html);
        }
    }

    function removeDestination(name) {
        $('#destinations-wrapper').find(`.destination-input-group[data-name="${name}"]`).remove();
        reindexDestinations();
    }

    function reindexDestinations() {
        $('#destinations-wrapper .destination-input-group').each(function(index) {
            $(this).find('input').each(function() {
                var name = $(this).attr('name');
                if (name) {
                    $(this).attr('name', name.replace(/\d+/, index));
                }
            });
        });
    }
});
<<<<<<< HEAD
jQuery(document).ready(function($) {
    // Add handler for remove button clicks
    $(document).on('click', '.remove-destination', function() {
        const $container = $(this).closest('.destination-input-group');
        const destinationName = $container.data('name');
        
        // Remove the destination input group
        $container.remove();
        
        // Uncheck corresponding checkbox
        uncheckDestinationCheckbox(destinationName);
        
        // Reindex remaining destinations
        reindexDestinations();
    });

    function uncheckDestinationCheckbox(name) {
        // Find and uncheck the checkbox with matching data-name
        $(`.destination-checkbox[data-name="${name}"]`).prop('checked', false);
    }

    // Update existing removeDestination function to use new uncheck functionality
    function removeDestination(name) {
        $('#destinations-wrapper').find(`.destination-input-group[data-name="${name}"]`).remove();
        uncheckDestinationCheckbox(name);
        reindexDestinations();
    }
});
=======
</script>
            </div>
        <script>

>>>>>>> a857882d090fd13b7ad06441edcd6f1b4a080c45


            jQuery(document).ready(function ($) {
                let destinationIndex = <?php echo !empty($fields['destinations']) ? count($fields['destinations']) : 1; ?>;

<<<<<<< HEAD
            // Add new destination
            $('#add-destination').click(function () {
                const html = `
    <div class="destination-input-group" >
    <div class="destinations-flex">
    <div class="image-preview">
    <img src="" style="display: none;">
    </div>
    <div style="width:78%">
    <input type="text" name="destination[${destinationIndex}][name]" placeholder="Destination Name" value="" />
    <input type="text" name="destination[${destinationIndex}][destination_url]" placeholder="Destination URL" value="" />
    </div>
    </div>
    <div class="des-button">
    <input type="hidden" name="destination[${destinationIndex}][image]" value="" class="destination-image-input" />
    <button type="button" class="upload-destination-image">Upload Image</button>
    <button type="button" class="remove-destination">Remove</button>
    </div>
    </div>`;
                $('#destinations-wrapper').append(html);
                destinationIndex++;
            });

            // Remove destination
            $(document).on('click', '.remove-destination', function () {
                $(this).closest('.destination-input-group').remove();
            });

            // Image upload
            $(document).on('click', '.upload-destination-image', function (e) {
                e.preventDefault();
                const button = $(this);
                const container = button.closest('.destination-input-group');
                const imageInput = container.find('.destination-image-input');
                const imagePreview = container.find('.image-preview img');

                const frame = wp.media({
                    title: 'Select Destination Image',
                    button: {
                        text: 'Use this image'
                    },
                    multiple: false
                });

                frame.on('select', function () {
                    const attachment = frame.state().get('selection').first().toJSON();
                    imageInput.val(attachment.url);
                    imagePreview.attr('src', attachment.url).css('display', 'block');
                });

                frame.open();
            });
        });
    </script>
=======
                // Add new destination
                $('#add-destination').click(function () {
                    const html = `
<div class="destination-input-group" >
<div class="destinations-flex">
<div class="image-preview">
<img src="" style="display: none;">
</div>
<div style="width:78%">
<input type="text" name="destination[${destinationIndex}][name]" placeholder="Destination Name" value="" />
<input type="text" name="destination[${destinationIndex}][destination_url]" placeholder="Destination URL" value="" />
</div>
</div>
<div class="des-button">
<input type="hidden" name="destination[${destinationIndex}][image]" value="" class="destination-image-input" />
<button type="button" class="upload-destination-image">Upload Image</button>
<button type="button" class="remove-destination">Remove</button>
</div>
</div>`;
                    $('#destinations-wrapper').append(html);
                    destinationIndex++;
                });

                // Remove destination
                $(document).on('click', '.remove-destination', function () {
                    $(this).closest('.destination-input-group').remove();
                });

                // Image upload
                $(document).on('click', '.upload-destination-image', function (e) {
                    e.preventDefault();
                    const button = $(this);
                    const container = button.closest('.destination-input-group');
                    const imageInput = container.find('.destination-image-input');
                    const imagePreview = container.find('.image-preview img');

                    const frame = wp.media({
                        title: 'Select Destination Image',
                        button: {
                            text: 'Use this image'
                        },
                        multiple: false
                    });

                    frame.on('select', function () {
                        const attachment = frame.state().get('selection').first().toJSON();
                        imageInput.val(attachment.url);
                        imagePreview.attr('src', attachment.url).css('display', 'block');
                    });

                    frame.open();
                });
            });
        </script>

    </div>
>>>>>>> a857882d090fd13b7ad06441edcd6f1b4a080c45
    <!-- content area End -->
 
    <?php
}

// Add this function to saving

function kingsland_save_tour_package_meta_data($post_id)
{

    // Check if nonce is set and valid
    if (!isset($_POST['kingsland_tour_package_nonce']) || !wp_verify_nonce($_POST['kingsland_tour_package_nonce'], 'kingsland_tour_package_nonce_action')) {
        return;
    }

    // Check if this is an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }


    // Define the fields to save
    $fields = [
        'trip_location',
        'duration',
        'hotel_info',
        'price',
        'old_price',
        'highlights',
        'itinerary',
        'hotels',
        'stay_info',
        'inclusions',
        'exclusions',
        'reviews',
        'faqs',
        'hotel_star',
        'services',
        'deal_pr',
        'destinations_covered',
        'accommodation', // Added accommodation field here
        'things_to_do', // Add this line
        'gallery',
        'destinations',
    ];

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            switch ($field) {
                case 'reviews':
                    update_post_meta($post_id, $field, sanitize_textarea_field($_POST[$field]));
                    break;

                case 'faqs':
                    // Handle FAQ data
                    if (is_array($_POST[$field])) {
                        $faqs = array_filter($_POST[$field], function ($faq) {
                            return !empty($faq['question']) && !empty($faq['answer']);
                        });

                        // Sanitize each FAQ
                        $sanitized_faqs = array_map(function ($faq) {
                            return array(
                                'question' => sanitize_text_field($faq['question']),
                                'answer' => sanitize_text_field($faq['answer'])
                            );
                        }, $faqs);

                        if (!empty($sanitized_faqs)) {
                            update_post_meta($post_id, $field, maybe_serialize($sanitized_faqs));
                        } else {
                            delete_post_meta($post_id, $field);
                        }
                    }
                    break;

                case 'services':
                    // Handle services data
                    if (is_array($_POST[$field])) {
                        $services = array_map('sanitize_text_field', $_POST[$field]);
                        update_post_meta($post_id, $field, maybe_serialize($services));
                    }
                    break;

                case 'itinerary':
                    // Initialize empty array for itinerary
                    $itinerary = array();

                    // Check if itinerary data was posted
                    if (isset($_POST[$field]) && is_array($_POST[$field])) {
                        foreach ($_POST[$field] as $day) {
                            // Process tags
                            $tags = isset($day['day_tags']) ?
                                array_map('trim', explode(',', sanitize_text_field($day['day_tags']))) :
                                array();

                            // Add day to itinerary array with sanitized values
                            $itinerary[] = array(
                                'day_title' => sanitize_text_field($day['day_title'] ?? ''),
                                'day_label' => sanitize_text_field($day['day_label'] ?? ''),
                                'day_tags' => $tags
                            );
                        }
                    }

                    // Always update post meta, even if empty
                    update_post_meta($post_id, $field, $itinerary);
                    break;

                case 'gallery':
                    // Handle gallery data
                    if (is_array($_POST[$field])) {
                        $gallery = array_map('sanitize_text_field', $_POST[$field]);
                        update_post_meta($post_id, $field, maybe_serialize($gallery));
                    }
                    break;
                case 'hotels':
                    // Handle hotels data
                    if (is_array($_POST[$field])) {
                        $hotels = array_filter($_POST[$field], function ($hotel) {
                            return !empty($hotel['name']) && !empty($hotel['rating']) && !empty($hotel['address']) && !empty($hotel['image']);
                        });

                        // Sanitize each hotel
                        $sanitized_hotels = array_map(function ($hotel) {
                            return array(
                                'name' => sanitize_text_field($hotel['name']),
                                'rating' => intval($hotel['rating']),
                                'address' => sanitize_text_field($hotel['address']),
                                'image' => sanitize_text_field($hotel['image'])
                            );
                        }, $hotels);

                        if (!empty($sanitized_hotels)) {
                            update_post_meta($post_id, $field, maybe_serialize($sanitized_hotels));
                        } else {
                            delete_post_meta($post_id, $field);
                        }
                    }
                    break;



                case 'highlights':
                    // Handle highlights data
                    $highlights = sanitize_text_field($_POST[$field]);
                    update_post_meta($post_id, $field, $highlights);
                    break;
                case 'inclusions':
                    // Handle inclusions data
                    if (is_array($_POST[$field])) {
                        $inclusions = array_filter($_POST[$field], function ($inclusion) {
                            return !empty($inclusion);
                        });

                        // Sanitize each inclusion
                        $sanitized_inclusions = array_map('sanitize_text_field', $inclusions);

                        if (!empty($sanitized_inclusions)) {
                            update_post_meta($post_id, $field, maybe_serialize($sanitized_inclusions));
                        } else {
                            delete_post_meta($post_id, $field);
                        }
                    }
                    break;

                case 'exclusions':
                    // Handle exclusions data
                    if (is_array($_POST[$field])) {
                        $exclusions = array_filter($_POST[$field], function ($exclusion) {
                            return !empty($exclusion);
                        });

                        // Sanitize each exclusion
                        $sanitized_exclusions = array_map('sanitize_text_field', $exclusions);

                        if (!empty($sanitized_exclusions)) {
                            update_post_meta($post_id, $field, maybe_serialize($sanitized_exclusions));
                        } else {
                            delete_post_meta($post_id, $field);
                        }
                    }
                    break;
                case 'destinations':

                case 'things_to_do': // Add this line
                    // Handle comma-separated fields
                    $value = sanitize_text_field($_POST[$field]);
                    update_post_meta($post_id, $field, $value);
                    break;

                case 'destinations_covered':
                    // Handle comma-separated fields
                    $value = sanitize_text_field($_POST[$field]);
                    update_post_meta($post_id, $field, $value);
                    break;
                case 'accommodation': // Added accommodation field here
                    // Handle comma-separated fields
                    $value = sanitize_text_field($_POST[$field]);
                    update_post_meta($post_id, $field, $value);
                    break;

                default:
                    update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
                    break;
            }
        } else {
            // Always save even if empty
            update_post_meta($post_id, $field, '');
        }
    }
    // Save slideshow data
    if (isset($_POST['slideshow_images'])) {
        update_post_meta($post_id, 'slideshow_images', array_map('sanitize_text_field', $_POST['slideshow_images']));
    }
    if (isset($_POST['slideshow_captions'])) {
        update_post_meta($post_id, 'slideshow_captions', array_map('sanitize_text_field', $_POST['slideshow_captions']));
    }
    if (isset($_POST['slideshow_positions'])) {
        update_post_meta($post_id, 'slideshow_positions', array_map('sanitize_text_field', $_POST['slideshow_positions']));
    }



    if (isset($_POST['destination']) && is_array($_POST['destination'])) {
        // Filter out empty destinations
        $destinations = array_filter($_POST['destination'], function ($dest) {
            return !empty($dest['name']) || !empty($dest['destination_url']) || !empty($dest['image']);
        });

        // Sanitize and save each destination separately
        $sanitized_destinations = array();
        foreach ($destinations as $destination) {
            if (!empty($destination)) {
                $sanitized_destination = array(
                    'name' => sanitize_text_field($destination['name']),
                    'destination_url' => esc_url_raw($destination['destination_url']),
                    'image' => esc_url_raw($destination['image'])
                );
                $sanitized_destinations[] = $sanitized_destination;
            }
        }

        // Only save if there are valid destinations
        if (!empty($sanitized_destinations)) {
            update_post_meta($post_id, 'destinations', $sanitized_destinations);
        }
    } else {
        // Clear destinations if none submitted
        delete_post_meta($post_id, 'destinations');
    }
    // Save selected categories
    if (isset($_POST['display_categories'])) {
        $categories = array_map('absint', $_POST['display_categories']);
        update_post_meta($post_id, 'display_categories', $categories);
    } else {
        delete_post_meta($post_id, 'display_categories');
    }

    // Update query args in single.php
    $selected_cats = get_post_meta($post_id, 'display_categories', true);
    if (!empty($selected_cats)) {
        $args['category__in'] = $selected_cats;
    }
    // Add debugging
    error_log('POST data: ' . print_r($_POST, true));
    error_log('Saved meta fields: ' . print_r(get_post_meta($post_id), true));
}
add_action('save_post', 'kingsland_save_tour_package_meta_data');


// Load the template for displaying the package
function kingsland_load_single_package_template($single_template)
{
    global $post;

    if ($post->post_type === 'tour_package') {
        $single_template = plugin_dir_path(__FILE__) . 'template/single-tour-package.php';
    }

    return $single_template;
}



add_filter('single_template', 'kingsland_load_single_package_template');


