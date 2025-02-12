<?php
/**
 * @package    AnimatedGutenbergGallery
 * @author     Matysiewicz Studio <support@matysiewicz.studio>
 * @copyright  Copyright (c) 2024 Matysiewicz Studio
 * 
 * This is a commercial plugin, licensed under CodeCanyon's Regular/Extended License.
 * For full license details see: https://codecanyon.net/licenses/terms/regular
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete options
delete_option('agg_settings');
delete_option('agg_version');

// Clean up any transients
delete_transient('agg_cache');

// Clean up post meta if any
global $wpdb;
$wpdb->delete($wpdb->postmeta, ['meta_key' => 'agg_gallery_settings']);