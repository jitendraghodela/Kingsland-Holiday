<?php
/**
 * Configuration file for Kingsland Tour Packages plugin
 * 
 * @package Kingsland_Tour_Packages
 * @version 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
<<<<<<< HEAD
// Plugin configuration constants
define('KINGSLAND_TOUR_VERSION', '1.0.0');
define('KINGSLAND_TOUR_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('KINGSLAND_TOUR_PLUGIN_URL', plugin_dir_url(__FILE__));
define('KINGSLAND_TOUR_ASSETS_URL', KINGSLAND_TOUR_PLUGIN_URL . 'assets/');
define('KINGSLAND_TOUR_TEMPLATES_DIR', KINGSLAND_TOUR_PLUGIN_DIR . 'templates/');

// Database configuration
define('KINGSLAND_TOUR_DB_VERSION', '1.0');
define('KINGSLAND_TOUR_TABLE_PREFIX', 'kingsland_tour_');

// Cache configuration
define('KINGSLAND_TOUR_CACHE_ENABLED', true);
define('KINGSLAND_TOUR_CACHE_EXPIRATION', 3600); // 1 hour

// Default settings
define('KINGSLAND_TOUR_DEFAULT_CURRENCY', 'USD');
define('KINGSLAND_TOUR_PER_PAGE', 10);
define('KINGSLAND_TOUR_IMAGE_SIZE', 'large');
=======

// Set default timezone
date_default_timezone_set('UTC');
>>>>>>> a857882d090fd13b7ad06441edcd6f1b4a080c45
