<?php
namespace AGGL\Admin;

class AGG_Admin {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_plugin_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        
        // Notices control
        add_action('admin_head', function() {
            $screen = get_current_screen();
            if ($screen && $screen->id === 'toplevel_page_animated-gutenberg-gallery-lite') {
                remove_all_actions('admin_notices');
                remove_all_actions('all_admin_notices');
                add_action('admin_notices', [$this, 'show_admin_notices']);
            }
        });
    }

    public function show_admin_notices() {
        settings_errors('agg_settings');
        
        // Show premium upgrade notice
        ?>
        <div class="notice notice-info is-dismissible">
            <p><?php 
            printf(
                __('Get more animations and effects with %sAnimated Gutenberg Gallery Premium%s', 'animated-gutenberg-gallery-lite'),
                '<a href="https://matysiewicz.studio/animated-gutenberg-gallery" target="_blank">',
                '</a>'
            ); 
            ?></p>
        </div>
        <?php
    }

    public function add_plugin_admin_menu() {
        if (!current_user_can('manage_options')) {
            return;
        }

        add_menu_page(
            __('AG Gallery Lite', 'animated-gutenberg-gallery-lite'),
            __('AG Gallery Lite', 'animated-gutenberg-gallery-lite'),
            'manage_options',
            'animated-gutenberg-gallery-lite',
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
                    'animation_duration' => 1.5,
                    'hover_effect' => 'none'
                ]
            ]
        );
    }

    public function sanitize_settings($input) {
        try {
            $sanitized = [];
            
            // Animation type - only allow 'fade' in lite version
            $sanitized['animation_type'] = 'fade';

            // Animation style - only allow 'group' in lite version
            $sanitized['animation_style'] = 'group';

            // Fixed animation duration in lite version
            $sanitized['animation_duration'] = 1.5;

            // No hover effects in lite version
            $sanitized['hover_effect'] = 'none';

            return $sanitized;
        } catch (\Exception $e) {
            add_settings_error(
                'agg_settings',
                'agg_settings_error',
                __('Error saving settings. Please try again.', 'animated-gutenberg-gallery-lite')
            );
            return get_option('agg_settings');
        }
    }

    public function display_plugin_admin_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'animated-gutenberg-gallery-lite'));
        }

        require_once AGG_PLUGIN_DIR . 'includes/admin/views/view-agg-admin.php';
    }
}