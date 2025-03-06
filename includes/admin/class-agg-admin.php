<?php
namespace AGGL\Admin;

class AGGL_Admin {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_plugin_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        
        // Notices control
        add_action('admin_head', function() {
            $screen = get_current_screen();
            if ($screen && $screen->id === 'toplevel_page_animated-g-gallery-lite') {
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
                // translators: %1$s is the opening link tag, %2$s is the closing link tag
                echo sprintf(
                    /* translators: %1$s is the opening link tag, %2$s is the closing link tag */
                    esc_html__('Get more animations and effects with %1$sAnimated G. Gallery Premium%2$s', 'animated-g-gallery-lite'),
                    '<a href="https://agg.matysiewicz.studio/" target="_blank">',
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
            __('AG Gallery Lite', 'animated-g-gallery-lite'),
            __('AG Gallery Lite', 'animated-g-gallery-lite'),
            'manage_options',
            'animated-g-gallery-lite',
            [$this, 'display_plugin_admin_page'],
            'dashicons-format-gallery',
            30
        );
    }
    /**
     * Sanitize settings before saving to database
     * 
     * This method enforces fixed values for the lite version of the plugin
     * and ensures that no user input can modify these predetermined settings.
     * 
     * @param array $input The input array from form submission
     * @return array Sanitized settings with predefined values
     * @since 1.0.0
     */   
    
     public function sanitize_settings_instance($input) {
        try {
            $sanitized = [];
            
            // Animation type - only allow 'fade' in lite version
            $sanitized['animation_type'] = 'fade';
            
            // Animation style - only allow 'group' in lite version
            $sanitized['animation_style'] = 'group';
            
            // Animation duration - validate and allow changes in lite version
            $duration = isset($input['animation_duration']) ? floatval($input['animation_duration']) : 0.5;
            // Ensure duration is between 0.1 and 10 seconds for reasonable values
            $sanitized['animation_duration'] = max(0.1, min(10, $duration));
            
            // No hover effects in lite version
            $sanitized['hover_effect'] = 'none';
            
            return $sanitized;
        } catch (\Exception $e) {
            add_settings_error(
                'agg_settings',
                'agg_settings_error',
                esc_html__('Error saving settings. Please try again.', 'animated-g-gallery-lite')
            );
            return get_option('agg_settings');
        }
    }
    public static function sanitize_settings($input) {
        $admin = new self();
        return $admin->sanitize_settings_instance($input);
    }

    public function register_settings() {

        register_setting(
            'agg_options',
            'agg_settings',
            [
                'type' => 'array',
                'sanitize_callback' => [$this, 'sanitize_settings_instance'],
                'default' => [
                    'animation_type' => 'fade',
                    'animation_duration' => 1.5,
                    'hover_effect' => 'none'
                ]
            ]
        );
    }

    public function display_plugin_admin_page() {
        if (!current_user_can('manage_options')) {
            echo esc_html__('Error saving settings. Please try again.', 'animated-g-gallery-lite');
        }

        require_once AGG_PLUGIN_DIR . 'includes/admin/views/view-agg-admin.php';
    }
}