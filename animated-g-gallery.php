<?php
/**
 * Plugin Name: Animated G. Gallery Lite
 * Plugin URI: https://agg.matysiewicz.studio
 * Description: Add beautiful animations to Gutenberg gallery blocks. Lite version.
 * Version: 1.0.4
 * Author: Matysiewicz Studio
 * Author URI: https://matysiewicz.studio
 * License: GPL v2 or later
 * Text Domain: animated-g-gallery-lite
 * Domain Path: /languages
 * 
 * @package Animated-G-Gallery
 * @author Matysiewicz Studio
 * @copyright Copyright (c) 2025, Matysiewicz Studio
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Plugin constants
define('AGG_VERSION', '1.0.4');
define('AGG_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AGG_PLUGIN_URL', plugin_dir_url(__FILE__));
define('AGG_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Core files
require_once AGG_PLUGIN_DIR . 'includes/core/class-agg-loader.php';
require_once AGG_PLUGIN_DIR . 'includes/core/class-agg-i18n.php';
require_once AGG_PLUGIN_DIR . 'includes/core/class-agg-assets.php';
require_once AGG_PLUGIN_DIR . 'includes/core/class-agg-gallery.php';
require_once AGG_PLUGIN_DIR . 'includes/core/class-agg-activator.php';
require_once AGG_PLUGIN_DIR . 'includes/admin/class-agg-admin.php';
require_once AGG_PLUGIN_DIR . 'includes/core/class-agg-init.php';
require_once AGG_PLUGIN_DIR . 'includes/frontend/class-agg-public.php';

// Simple activation hook
register_activation_hook(__FILE__, function() {
    flush_rewrite_rules();
});

// Simple deactivation hook
register_deactivation_hook(__FILE__, 'flush_rewrite_rules');


// Initialize plugin
function run_animated_g_gallery() {
    $plugin = new AGGL\Core\AGGL_Init();
    $plugin->run();
}
run_animated_g_gallery();

