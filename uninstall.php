<?php
// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete options
delete_option('agg_settings');
delete_option('agg_version');

// Clean up any transients
delete_transient('agg_cache');

// Better approach for cleaning post meta
delete_metadata('post', 0, 'agg_gallery_settings', '', true);