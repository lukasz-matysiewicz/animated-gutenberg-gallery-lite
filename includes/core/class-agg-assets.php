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

class AGG_Assets {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'register_assets']);
        add_action('admin_enqueue_scripts', [$this, 'register_admin_assets']);
    }

    public function register_assets() {
        // Register GSAP
        wp_enqueue_script(
            'gsap',
            'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js',
            [],
            '3.12.2',
            true
        );

        // Register ScrollTrigger
        wp_enqueue_script(
            'gsap-scrolltrigger',
            'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js',
            ['gsap'],
            '3.12.2',
            true
        );

        // Register Lenis Scroll
        wp_enqueue_script(
            'lenis',
            'https://cdn.jsdelivr.net/gh/studio-freight/lenis@1.0.27/bundled/lenis.min.js',
            [],
            '1.0.27',
            true
        );

        // Get default settings
        $settings = get_option('agg_settings', array(
            'animation_type' => 'fade-up',
            'animation_style' => 'group',
            'animation_duration' => 2,
            'animation_stagger' => 0.2,
            'hover_effect' => 'zoom'
        ));

        // Register shared animations
        wp_enqueue_script(
            'agg-animations',
            AGG_PLUGIN_URL . 'assets/js/agg-animations.js',
            ['gsap', 'gsap-scrolltrigger'],
            AGG_VERSION,
            true
        );

        // Plugin CSS
        wp_enqueue_style(
            'agg-public',
            AGG_PLUGIN_URL . 'assets/css/agg-public.css',
            [],
            AGG_VERSION
        );

        // Plugin JS
        wp_enqueue_script(
            'agg-public',
            AGG_PLUGIN_URL . 'assets/js/agg-public.js',
            ['jquery', 'gsap', 'gsap-scrolltrigger', 'agg-animations', 'lenis'],
            AGG_VERSION,
            true
        );

        // Pass settings to JS
        wp_localize_script('agg-public', 'aggSettings', $settings);
    }

    public function register_admin_assets($hook) {
        if (strpos($hook, 'animated-gutenberg-gallery') !== false) {
            // Register GSAP for admin
            wp_enqueue_script(
                'gsap',
                'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js',
                [],
                '3.12.2',
                true
            );

            // Register ScrollTrigger for admin
            wp_enqueue_script(
                'gsap-scrolltrigger',
                'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js',
                ['gsap'],
                '3.12.2',
                true
            );

            // Register shared animations
            wp_enqueue_script(
                'agg-animations',
                AGG_PLUGIN_URL . 'assets/js/agg-animations.js',
                ['gsap', 'gsap-scrolltrigger'],
                AGG_VERSION,
                true
            );

            // Admin styles
            wp_enqueue_style(
                'agg-admin',
                AGG_PLUGIN_URL . 'assets/css/agg-admin.css',
                [],
                AGG_VERSION
            );

            // Admin scripts
            wp_enqueue_script(
                'agg-admin',
                AGG_PLUGIN_URL . 'assets/js/agg-admin.js',
                ['jquery', 'gsap', 'gsap-scrolltrigger', 'agg-animations'],
                AGG_VERSION,
                true
            );

            // Get settings for admin
            $settings = get_option('agg_settings', array(
                'animation_type' => 'fade-up',
                'animation_style' => 'group',
                'animation_duration' => 2,
                'animation_stagger' => 0.2,
                'hover_effect' => 'zoom'
            ));

            // Pass settings to admin JS
            wp_localize_script('agg-admin', 'aggSettings', $settings);
        }
    }
}