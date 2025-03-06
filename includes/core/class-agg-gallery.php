<?php
namespace AGGL\Core;

class AGGL_Gallery {
    public function __construct() {
        // Block modifications
        add_filter('render_block', [$this, 'modify_gallery_block'], 10, 2);
        add_filter('block_editor_settings_all', [$this, 'add_editor_settings']);
        
        // Asset management
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_editor_assets']);
        
        // LiteSpeed Cache integration
        add_filter('litespeed_media_lazy_img_excludes', [$this, 'exclude_from_litespeed_lazy_load'], 10, 2);
        
        // Add custom block attributes
        add_filter('register_block_type_args', [$this, 'register_gallery_attributes'], 10, 2);
    }

    public function get_default_settings() {
        $defaults = array(
            'animation_type' => 'fade',
            'animation_style' => 'group',
            'animation_duration' => 0.5,
            'hover_effect' => 'none',
            'lightbox' => true,
            'animations' => true
        );

        return apply_filters('agg_default_settings', $defaults);
    }

    public function get_settings() {
        $defaults = $this->get_default_settings();
        $settings = get_option('agg_settings', $defaults);
        return wp_parse_args($settings, $defaults);
    }

    // In class-agg-gallery.php, update the modify_gallery_block function:

public function modify_gallery_block($block_content, $block) {
    // Check for both gallery block names as they may vary in different WP versions
    if ($block['blockName'] !== 'core/gallery' && $block['blockName'] !== 'core/group') {
        return $block_content;
    }
    
    // For core/group blocks, check if it contains gallery-related classes
    if ($block['blockName'] === 'core/group' && 
        (!isset($block['attrs']['className']) || 
         strpos($block['attrs']['className'], 'gallery') === false)) {
        return $block_content;
    }

    try {
        $settings = $this->get_settings();
        
        $classes = ['agg-gallery'];
        if ($settings['lightbox']) $classes[] = 'agg-lightbox';
        if ($settings['animations']) $classes[] = 'agg-animated';

        // Add data attributes for settings
        $data_attrs = sprintf(
            'data-agg-lightbox="%s" data-agg-animations="%s"',
            esc_attr($settings['lightbox'] ? 'true' : 'false'),
            esc_attr($settings['animations'] ? 'true' : 'false')
        );

        // Add classes and data attributes to gallery wrapper
        // Updated pattern to be more flexible with different HTML structures
        $block_content = preg_replace(
            '/<(figure|div)([^>]*?)class="([^"]*)"/',
            '<$1$2class="$3 ' . implode(' ', $classes) . '" ' . $data_attrs,
            $block_content
        );

        return $block_content;
    } catch (\Exception $e) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            // error_log('AGG Gallery Block Modification Error: ' . $e->getMessage());
        }
        return $block_content;
    }
}

    public function add_editor_settings($settings) {
        $plugin_settings = $this->get_settings();
        $settings['aggSettingsUrl'] = admin_url('admin.php?page=animated-g-gallery-lite');
        $settings['aggDefaults'] = [
            'lightbox' => true,
            'animations' => true,
            'animation_type' => 'fade'
        ];
        return $settings;
    }

    public function enqueue_editor_assets() {
        wp_enqueue_script(
            'agg-editor',
            AGG_PLUGIN_URL . 'assets/js/agg-editor.js',
            ['wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor'],
            AGG_VERSION,
            true
        );
    }

    public function register_gallery_attributes($args, $block_type) {
        if ($block_type !== 'core/gallery') {
            return $args;
        }

        if (!isset($args['attributes'])) {
            $args['attributes'] = [];
        }

        $args['attributes']['aggLightbox'] = [
            'type' => 'boolean',
            'default' => true
        ];

        $args['attributes']['aggAnimations'] = [
            'type' => 'boolean',
            'default' => true
        ];

        return $args;
    }

    public function exclude_from_litespeed_lazy_load($excluded, $url = '') {
        if (is_array($excluded)) {
            return $excluded;
        }

        if (empty($url)) {
            return $excluded;
        }
        
        global $post;
        if (!$post) return $excluded;
        
        if (has_block('core/gallery', $post)) {
            $blocks = parse_blocks($post->post_content);
            foreach ($blocks as $block) {
                if ($block['blockName'] === 'core/gallery') {
                    if (isset($block['innerBlocks'])) {
                        foreach ($block['innerBlocks'] as $innerBlock) {
                            if (isset($innerBlock['attrs']['url']) && 
                                $innerBlock['attrs']['url'] === $url) {
                                return true;
                            }
                        }
                    }
                }
            }
        }
        
        return $excluded;
    }

    public function is_premium_feature($feature) {
        $premium_features = [
            'fade-up' => true,
            'fade-left' => true,
            'zoom' => true,
            'alternate-scroll' => true,
            'sequence' => true,
            'hover_zoom' => true,
            'hover_lift' => true,
            'hover_tilt' => true
        ];

        return isset($premium_features[$feature]);
    }
}