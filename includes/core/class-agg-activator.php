<?php
namespace AGG\Core;

class AGG_Activator {
    public static function activate() {
        $default_options = array(
            'animation_type' => 'fade',
            'animation_style' => 'group',
            'animation_duration' => 1.5,
            'hover_effect' => 'none'
        );

        // Only add option if it doesn't exist
        if (!get_option('agg_settings')) {
            add_option('agg_settings', $default_options);
        }
        
        add_option('agg_version', AGG_VERSION);
        flush_rewrite_rules();
    }
}