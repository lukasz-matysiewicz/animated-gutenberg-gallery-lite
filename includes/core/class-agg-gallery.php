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

/**
 * Main Gallery Class
 * 
 * Handles gallery block modifications, settings, and assets management
 */
class AGG_Gallery {
    /**
     * Initialize the gallery functionality
     */
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
        
        // Add custom classes to gallery items
        add_filter('render_block_core/image', [$this, 'modify_gallery_image_block'], 10, 2);
    }

    /**
     * Get default plugin settings
     *
     * @return array Default settings
     */
    public function get_default_settings() {
        $defaults = array(
            'animation_type' => 'fade-up',
            'animation_style' => 'group',
            'animation_duration' => 1,
            'hover_effect' => 'zoom',
            'lightbox' => true,
            'animations' => true
        );

        return apply_filters('agg_default_settings', $defaults);
    }

    /**
     * Get current plugin settings with defaults
     *
     * @return array Current settings
     */
    public function get_settings() {
        $defaults = $this->get_default_settings();
        $settings = get_option('agg_settings', $defaults);
        return wp_parse_args($settings, $defaults);
    }

    /**
     * Modify gallery block output
     *
     * @param string $block_content Block content
     * @param array  $block         Block data
     * @return string Modified block content
     */
    public function modify_gallery_block($block_content, $block) {
        if ($block['blockName'] !== 'core/gallery') {
            return $block_content;
        }

        try {
            $settings = $this->get_settings();
            $attrs = $block['attrs'] ?? [];
            $lightbox = $attrs['aggLightbox'] ?? $settings['lightbox'];
            $animations = $attrs['aggAnimations'] ?? $settings['animations'];

            $classes = ['agg-gallery'];
            if ($lightbox) $classes[] = 'agg-lightbox';
            if ($animations) $classes[] = 'agg-animated';

            // Add data attributes for settings
            $data_attrs = sprintf(
                'data-agg-lightbox="%s" data-agg-animations="%s"',
                esc_attr($lightbox ? 'true' : 'false'),
                esc_attr($animations ? 'true' : 'false')
            );

            // Add classes and data attributes to gallery wrapper
            $block_content = preg_replace(
                '/<figure([^>]*?)class="([^"]*)"/',
                '<figure$1class="$2 ' . implode(' ', $classes) . '" ' . $data_attrs,
                $block_content
            );

            // Add loading="eager" to first few images for better performance
            $block_content = $this->modify_image_loading_attributes($block_content);

            return $block_content;
        } catch (\Exception $e) {
            error_log('AGG Gallery Block Modification Error: ' . $e->getMessage());
            return $block_content;
        }
    }

    /**
     * Modify image loading attributes for better performance
     *
     * @param string $content Block content
     * @return string Modified content
     */
    private function modify_image_loading_attributes($content) {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $content = htmlspecialchars_decode($content);
        $dom->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $images = $dom->getElementsByTagName('img');
        foreach ($images as $index => $img) {
            // First 3 images load eagerly, rest lazy load
            if ($index < 3) {
                $img->setAttribute('loading', 'eager');
            } else {
                $img->setAttribute('loading', 'lazy');
            }
            
            // Add intrinsic dimensions if missing
            if (!$img->hasAttribute('width') || !$img->hasAttribute('height')) {
                $src = $img->getAttribute('src');
                if ($src && file_exists(ABSPATH . str_replace(site_url(), '', $src))) {
                    list($width, $height) = getimagesize(ABSPATH . str_replace(site_url(), '', $src));
                    $img->setAttribute('width', $width);
                    $img->setAttribute('height', $height);
                }
            }
        }

        return $dom->saveHTML();
    }

    /**
     * Add editor settings
     *
     * @param array $settings Current editor settings
     * @return array Modified settings
     */
    public function add_editor_settings($settings) {
        $plugin_settings = $this->get_settings();
        $settings['aggSettingsUrl'] = admin_url('admin.php?page=animated-gutenberg-gallery');
        $settings['aggDefaults'] = [
            'lightbox' => $plugin_settings['lightbox'],
            'animations' => $plugin_settings['animations'],
            'animation_type' => $plugin_settings['animation_type']
        ];
        return $settings;
    }

    /**
     * Enqueue editor assets
     */
    public function enqueue_editor_assets() {
        wp_enqueue_script(
            'agg-editor',
            AGG_PLUGIN_URL . 'assets/js/agg-editor.js',
            ['wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-compose'],
            AGG_VERSION,
            true
        );

        wp_localize_script('agg-editor', 'aggEditorSettings', [
            'aggSettingsUrl' => admin_url('admin.php?page=animated-gutenberg-gallery'),
            'defaultSettings' => $this->get_settings()
        ]);
    }

    /**
     * Register custom block attributes
     *
     * @param array  $args       Block arguments
     * @param string $block_type Block type name
     * @return array Modified arguments
     */
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

    /**
     * Modify gallery image block
     *
     * @param string $block_content Block content
     * @param array  $block         Block data
     * @return string Modified block content
     */
    public function modify_gallery_image_block($block_content, $block) {
        if (!isset($block['attrs']['parent']) || 
            !isset($block['attrs']['parent']['blockName']) || 
            $block['attrs']['parent']['blockName'] !== 'core/gallery') {
            return $block_content;
        }

        $block_content = str_replace(
            'wp-block-image',
            'wp-block-image agg-gallery-item',
            $block_content
        );

        return $block_content;
    }

    /**
     * Handle LiteSpeed Cache integration
     *
     * @param mixed  $excluded Whether the image is excluded or excluded images array
     * @param string $url      Image URL
     * @return mixed Modified exclusion status or array
     */
    public function exclude_from_litespeed_lazy_load($excluded, $url = '') {
        // Handle array input from LiteSpeed
        if (is_array($excluded)) {
            return $excluded;
        }

        // Skip if no URL provided
        if (empty($url)) {
            return $excluded;
        }
        
        global $post;
        if (!$post) return $excluded;
        
        if (has_block('core/gallery', $post)) {
            $blocks = parse_blocks($post->post_content);
            foreach ($blocks as $block) {
                if ($block['blockName'] === 'core/gallery') {
                    $attrs = $block['attrs'] ?? [];
                    if (($attrs['aggAnimations'] ?? true) === true) {
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
        }
        
        return $excluded;
    }


    /**
     * Clean up gallery data
     *
     * @param int $post_id Post ID
     */
    public function cleanup_gallery_data($post_id) {
        delete_post_meta($post_id, '_agg_gallery_settings');
    }
}