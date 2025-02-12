<?php
/**
 * @package    AnimatedGutenbergGallery
 * @author     Matysiewicz Studio <support@matysiewicz.studio>
 * @copyright  Copyright (c) 2024 Matysiewicz Studio
 * 
 * This is a commercial plugin, licensed under CodeCanyon's Regular/Extended License.
 * For full license details see: https://codecanyon.net/licenses/terms/regular
 */

namespace AGG\Core;

class AGG_Activator {
    public static function activate() {
        $default_options = array(
            'animation_type' => 'fade-up',
            'animation_style' => 'group',
            'animation_duration' => 2,
            'hover_effect' => 'zoom'
        );

        // Only add option if it doesn't exist
        if (!get_option('agg_settings')) {
            add_option('agg_settings', $default_options);
        }
        
        add_option('agg_version', AGG_VERSION);
        flush_rewrite_rules();
    }
}