<?php
/**
 * @package    AnimatedGutenbergGallery
 * @author     Matysiewicz Studio <support@matysiewicz.studio>
 * @copyright  Copyright (c) 2024 Matysiewicz Studio
 * 
 * This is a commercial plugin, licensed under CodeCanyon's Regular/Extended License.
 * For full license details see: https://codecanyon.net/licenses/terms/regular
 */

 namespace AGG\Admin;
 
 class AGG_Admin {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_plugin_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        
        // Notices control
        add_action('admin_head', function() {
            $screen = get_current_screen();
            if ($screen && $screen->id === 'toplevel_page_animated-gutenberg-gallery') {
                remove_all_actions('admin_notices');
                remove_all_actions('all_admin_notices');
                add_action('admin_notices', [$this, 'agg_admin_notices']);
            }
        });
    }
    public function agg_admin_notices() {
        // Your plugin's notices only
        settings_errors('agg_settings');
    }
    public function add_plugin_admin_menu() {
         // Capability check
         if (!current_user_can('manage_options')) {
             return;
         }
 
         add_menu_page(
             __('AG Gallery', 'animated-gutenberg-gallery'),
             __('AG Gallery', 'animated-gutenberg-gallery'),
             'manage_options',
             'animated-gutenberg-gallery',
             [$this, 'display_plugin_admin_page'],
             'dashicons-format-gallery',
             30
         );
     }
 
    public function register_settings() {
         register_setting(
             'agg_options',
             'agg_settings',
             [
                 'type' => 'array',
                 'sanitize_callback' => [$this, 'sanitize_settings'],
                 'default' => [
                     'animation_type' => 'fade',
                     'animation_duration' => 1,
                     'animation_stagger' => 0.2,
                     'hover_effect' => 'zoom'
                 ]
             ]
         );
     }
     public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'animated-gutenberg-gallery') !== false) {
            // Register GSAP 
            wp_enqueue_script(
                'gsap',
                'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js',
                [],
                '3.12.2',
                true
            );
    
            // Admin CSS and JS
            wp_enqueue_style(
                'agg-admin',
                AGG_PLUGIN_URL . 'assets/css/agg-admin.css',
                [],
                AGG_VERSION
            );
    
            wp_enqueue_script(
                'agg-admin',
                AGG_PLUGIN_URL . 'assets/js/agg-admin.js',
                ['jquery', 'gsap'],
                AGG_VERSION,
                true
            );
        }
    }
 
    public function sanitize_settings($input) {
        try {
            $sanitized = [];
            
            // Animation type
            $sanitized['animation_type'] = in_array($input['animation_type'], ['none', 'fade', 'fade-up', 'fade-left', 'zoom', 'alternate-scroll']) 
                ? $input['animation_type'] 
                : 'fade';

            // Animation style
            $sanitized['animation_style'] = in_array($input['animation_style'], ['group', 'sequence']) 
                ? $input['animation_style'] 
                : 'group';

            // Animation duration (between 0.1 and 3)
            $sanitized['animation_duration'] = floatval($input['animation_duration']);
            $sanitized['animation_duration'] = max(0.1, min(3, $sanitized['animation_duration']));

            // Hover effect
            $sanitized['hover_effect'] = in_array($input['hover_effect'], ['none', 'zoom', 'lift', 'tilt']) 
                ? $input['hover_effect'] 
                : 'zoom';

            return $sanitized;
        } catch (\Exception $e) {
            add_settings_error(
                'agg_settings',
                'agg_settings_error',
                __('Error saving settings. Please try again.', 'animated-gutenberg-gallery')
            );
            return get_option('agg_settings');
        }
    }

    public function register_strings_for_translation() {
        if (function_exists('pll_register_string')) {
            // Polylang registration
            pll_register_string('animation_effects', 'Animation Effects', 'animated-gutenberg-gallery');
            pll_register_string('animation_duration', 'Animation Duration', 'animated-gutenberg-gallery');
        }
    
        if (function_exists('icl_register_string')) {
            // WPML registration
            do_action('wpml_register_single_string', 'animated-gutenberg-gallery', 'animation_effects', 'Animation Effects');
            do_action('wpml_register_single_string', 'animated-gutenberg-gallery', 'animation_duration', 'Animation Duration');
        }
    }
    public function display_plugin_admin_page() {
         // Security checks
         if (!current_user_can('manage_options')) {
             wp_die(__('You do not have sufficient permissions to access this page.', 'animated-gutenberg-gallery'));
         }
 
         require_once AGG_PLUGIN_DIR . 'includes/admin/views/view-agg-admin.php';
     }
 }