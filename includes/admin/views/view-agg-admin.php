<?php
if (!defined('ABSPATH') || !current_user_can('manage_options')) {
    exit;
}

// Add nonce field
wp_nonce_field('agg_settings_action', 'agg_settings_nonce');

$default_settings = array(
    'animation_type' => 'fade',
    'animation_style' => 'group',
    'animation_duration' => 1.5,
    'hover_effect' => 'none'
);

// Get settings with defaults
$settings = get_option('agg_settings', $default_settings);
$settings = wp_parse_args($settings, $default_settings);
?>

<div class="wrap agg-admin-wrap">
    <!-- Header Section -->
    <div class="agg-header">
        <?php
        $image_attributes = array(
            'class' => 'agg-logo',
            'alt'   => esc_attr__('Animated G. Gallery Logo', 'animated-g-gallery-lite')
        );
        // If you have the image attachment ID
        if (function_exists('attachment_url_to_postid')) {
            $attachment_id = attachment_url_to_postid(AGG_PLUGIN_URL . 'assets/images/logo-agg.webp');
            if ($attachment_id) {
                echo wp_get_attachment_image($attachment_id, 'full', false, $image_attributes);
            } else {
                // Fallback if image is not in media library
                echo '<img src="' . esc_url(AGG_PLUGIN_URL . 'assets/images/logo-agg.webp') . '" 
                    alt="' . esc_attr__('Animated G. Gallery Logo', 'animated-g-gallery-lite') . '" 
                    class="agg-logo">';
            }
        } else {
            // Fallback for older WordPress versions
            echo '<img src="' . esc_url(AGG_PLUGIN_URL . 'assets/images/logo-agg.webp') . '" 
                alt="' . esc_attr__('Animated G. Gallery Logo', 'animated-g-gallery-lite') . '" 
                class="agg-logo">';
        }
        ?>
        <h1 class="agg-admin-title"><?php esc_html_e('Animated G. Gallery Lite', 'animated-g-gallery-lite'); ?></h1>
    </div>

    <form method="post" action="options.php">
        <?php settings_fields('agg_options'); ?>

        <div class="agg-settings-content">
            <!-- Main Settings Section -->
            <div class="agg-main-settings">
                <div class="agg-section">
                    <!-- Animation Effects Section -->
                    <h2 class="agg-section-title"><?php esc_html_e('Animation Effects', 'animated-g-gallery-lite'); ?></h2>
                    <div class="agg-button-group">
                        <?php
                        $effects = [
                            'fade' => [
                                'label' => esc_html__('Fade In', 'animated-g-gallery-lite'),
                                'premium' => false
                            ],
                            'fade-up' => [
                                'label' => esc_html__('Fade Up', 'animated-g-gallery-lite'),
                                'premium' => true
                            ],
                            'fade-left' => [
                                'label' => esc_html__('Fade Left', 'animated-g-gallery-lite'),
                                'premium' => true
                            ],
                            'zoom' => [
                                'label' => esc_html__('Zoom In', 'animated-g-gallery-lite'),
                                'premium' => true
                            ],
                            'alternate-scroll' => [
                                'label' => esc_html__('Alternate Scroll', 'animated-g-gallery-lite'),
                                'premium' => true
                            ]
                        ];
                        
                        foreach ($effects as $value => $effect) : 
                            $is_premium = $effect['premium'];
                            $disabled = $is_premium ? 'disabled' : '';
                            $premium_class = $is_premium ? 'agg-premium-feature' : '';
                            $active_class = ($value === 'fade') ? 'active' : ''; // Make fade always active
                        ?>
                            <button type="button" 
                                    class="agg-button <?php echo esc_attr($active_class . ' ' . $premium_class); ?>"
                                    data-value="<?php echo esc_attr($value); ?>"
                                    <?php echo esc_attr($disabled); ?>>
                                <?php echo esc_html($effect['label']); ?>
                                <?php if ($is_premium) : ?>
                                    <span class="agg-premium-badge"><?php esc_html_e('PRO', 'animated-g-gallery-lite'); ?></span>
                                <?php endif; ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" name="agg_settings[animation_type]" id="animation_type" value="<?php echo esc_attr($settings['animation_type']); ?>">

                    <!-- Animation Style Section -->
                    <h2 class="agg-section-title"><?php esc_html_e('Animation Style', 'animated-g-gallery-lite'); ?></h2>
                    <div class="agg-button-group">
                        <?php
                        $animation_styles = [
                            'group' => [
                                'label' => esc_html__('Group Animation', 'animated-g-gallery-lite'),
                                'premium' => false
                            ],
                            'sequence' => [
                                'label' => esc_html__('Sequence Animation', 'animated-g-gallery-lite'),
                                'premium' => true
                            ]
                        ];
                        foreach ($animation_styles as $value => $style) : 
                            $is_premium = $style['premium'];
                            $disabled = $is_premium ? 'disabled' : '';
                            $premium_class = $is_premium ? 'agg-premium-feature' : '';
                            $active_class = ($settings['animation_style'] === $value && !$is_premium) ? 'active' : '';
                            $active_class = ($value === 'group') ? 'active' : ''; // Make fade always active
                        ?>
                            <button type="button" 
                                    class="agg-button <?php echo esc_attr($active_class . ' ' . $premium_class); ?>"
                                    data-value="<?php echo esc_attr($value); ?>"
                                    <?php echo esc_attr($disabled); ?>>
                                <?php echo esc_html($effect['label']); ?>
                                <?php if ($is_premium) : ?>
                                    <span class="agg-premium-badge"><?php esc_html_e('PRO', 'animated-g-gallery-lite'); ?></span>
                                <?php endif; ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" name="agg_settings[animation_style]" id="animation_style" value="<?php echo esc_attr($settings['animation_style']); ?>">

                    <!-- Animation Duration Section -->
                    <h2 class="agg-section-title"><?php esc_html_e('Animation Duration', 'animated-g-gallery-lite'); ?></h2>
                    <div class="agg-input-group">
                        <input type="number" 
                            name="agg_settings[animation_duration]"
                            value="<?php echo esc_attr($settings['animation_duration']); ?>"
                            step="0.1"
                            min="0.1"
                            max="4"
                            class="agg-input agg-duration-input">
                        <span class="agg-input-hint"><?php esc_html_e('Duration in seconds (0.1 - 4)', 'animated-g-gallery-lite'); ?></span>
                    </div>

                    <!-- Hover Effects Section -->
                    <h2 class="agg-section-title agg-premium-title"><?php esc_html_e('Hover Effects', 'animated-g-gallery-lite'); ?>
                        <span class="agg-premium-badge"><?php esc_html_e('PRO', 'animated-g-gallery-lite'); ?></span>
                    </h2>
                    <div class="agg-button-group">
                        <?php
                        $hover_effects = [
                            'none' => [
                                'label' => esc_html__('None', 'animated-g-gallery-lite'),
                                'premium' => true,
                                'active' => true // Make none selected by default
                            ],
                            'zoom' => [
                                'label' => esc_html__('Zoom', 'animated-g-gallery-lite'),
                                'premium' => true
                            ],
                            'lift' => [
                                'label' => esc_html__('Lift Up', 'animated-g-gallery-lite'),
                                'premium' => true
                            ],
                            'tilt' => [
                                'label' => esc_html__('3D Tilt', 'animated-g-gallery-lite'),
                                'premium' => true
                            ]
                        ];
                        foreach ($hover_effects as $value => $effect) : 
                            $active_class = (!empty($effect['active'])) ? 'active' : '';
                        ?>
                            <button type="button" 
                                    class="agg-button <?php echo esc_attr($active_class . ' ' . $premium_class); ?>"
                                    data-value="<?php echo esc_attr($value); ?>"
                                    <?php echo esc_attr($disabled); ?>>
                                <?php echo esc_html($effect['label']); ?>
                                <?php if ($is_premium) : ?>
                                    <span class="agg-premium-badge"><?php esc_html_e('PRO', 'animated-g-gallery-lite'); ?></span>
                                <?php endif; ?>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <!-- Upgrade Notice -->
                    <div class="agg-upgrade-notice">
                        <h3><?php esc_html_e('Unlock Premium Features', 'animated-g-gallery-lite'); ?></h3>
                        <ul>
                            <li><?php esc_html_e('5 Animation Effects', 'animated-g-gallery-lite'); ?></li>
                            <li><?php esc_html_e('Sequential Animation Style', 'animated-g-gallery-lite'); ?></li>
                            <li><?php esc_html_e('Customizable Animation Duration', 'animated-g-gallery-lite'); ?></li>
                            <li><?php esc_html_e('3 Hover Effects', 'animated-g-gallery-lite'); ?></li>
                        </ul>
                        <a href="https://agg.matysiewicz.studio" target="_blank" class="button button-primary">
                            <?php esc_html_e('Upgrade to Premium', 'animated-g-gallery-lite'); ?>
                        </a>
                    </div>

                    <?php submit_button(__('Save Changes', 'animated-g-gallery-lite')); ?>
                </div>
            </div>

            <!-- Preview Section -->
            <div class="agg-preview-section">
                <h2 class="agg-section-title"><?php esc_html_e('Live Preview', 'animated-g-gallery-lite'); ?></h2>
                <!-- Preview content here -->
            </div>
        </div>
    </form>

    <footer class="agg-footer">
        <p>
            <?php 
                // translators: %s is an HTML link to the support email address
                echo sprintf(
                    /* translators: %s is an HTML link to the support email address */
                    esc_html__('Need help? Contact support at %s', 'animated-g-gallery-lite'),
                    '<a href="mailto:support@matysiewicz.studio">support@matysiewicz.studio</a>'
                );
            ?>
        </p>
    </footer>
</div>