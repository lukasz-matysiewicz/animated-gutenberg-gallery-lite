<?php
/**
 * Plugin Name: Animated Gutenberg Gallery
 * Plugin URI: https://matysiewicz.studio/animated-gutenberg-gallery
 * Description: Add beautiful GSAP animations to Gutenberg gallery blocks
 * Requires at least: 6.4
 * Requires PHP: 7.0
 * Version: 1.2.6
 * Author: Matysiewicz Studio
 * Author URI: https://matysiewicz.studio
 * License: Freemius Commercial License
 * Text Domain: animated-gutenberg-gallery
 * Domain Path: /languages
 * 
 * @package AnimatedGutenbergGallery
 * @author Matysiewicz Studio
 * @copyright Copyright (c) 2024, Matysiewicz Studio
 */

/**
 * Freemius
 */

 if ( ! function_exists( 'agg_fs' ) ) {
    // Create a helper function for easy SDK access.
    function agg_fs() {
        global $agg_fs;

        if ( ! isset( $agg_fs ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/vendor/freemius/start.php';
            $agg_fs = fs_dynamic_init( array(
                'id'                  => '17854',
                'slug'                => 'animated-gutenberg-gallery',
                'type'                => 'plugin',
                'public_key'          => 'pk_54869a78ba119c25875ba8ed7ba26',
                'is_premium'          => false,
                'has_addons'          => false,
                'has_paid_plans'      => false,
                'menu'                => array(
                    'slug'           => 'animated-gutenberg-gallery',
                    'contact'        => false,
                    'support'        => false,
                ),
            ) );
        }

        return $agg_fs;
    }

    // Init Freemius.
    agg_fs();
    // Signal that SDK was initiated.
    do_action( 'agg_fs_loaded' );
}
// Ensure the plugin is activated with a valid license
if (!agg_fs()->is_registered()) {
    add_action('admin_notices', function () {
        ?>
        <div class="notice notice-warning">
            <p><?php _e('⚠️ Please activate your license for Animated Gutenberg Gallery to receive updates and support.', 'animated-gutenberg-gallery'); ?></p>
        </div>
        <?php
    });
}

// Enable automatic updates only for licensed users
agg_fs()->add_filter('plugin_updates', function ($updates) {
    if (!agg_fs()->is_registered()) {
        return array(); // No updates if not registered
    }
    return $updates;
});


// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Current plugin version.
 */
define('AGG_VERSION', '1.2.6');
define('AGG_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AGG_PLUGIN_URL', plugin_dir_url(__FILE__));
define('AGG_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'AGG\\';
    $base_dir = AGG_PLUGIN_DIR . 'includes/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir;
    
    $namespace_parts = explode('\\', $relative_class);
    if (count($namespace_parts) > 1) {
        $class_name = array_pop($namespace_parts);
        $file .= strtolower(implode('/', $namespace_parts)) . '/';
    } else {
        $class_name = $relative_class;
    }
    
    $file .= 'class-agg-' . strtolower(str_replace('AGG_', '', $class_name)) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

/**
 * The code that runs during plugin activation.
 */
function activate_animated_gutenberg_gallery() {
    require_once AGG_PLUGIN_DIR . 'includes/core/class-agg-activator.php';
    AGG\Core\AGG_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_animated_gutenberg_gallery() {
    require_once AGG_PLUGIN_DIR . 'includes/core/class-agg-deactivator.php';
    AGG\Core\AGG_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_animated_gutenberg_gallery');
register_deactivation_hook(__FILE__, 'deactivate_animated_gutenberg_gallery');

/**
 * Begins execution of the plugin.
 */
function run_animated_gutenberg_gallery() {
    require_once AGG_PLUGIN_DIR . 'includes/core/class-agg-init.php';
    $plugin = new AGG\Core\AGG_Init();
    $plugin->run();
}
run_animated_gutenberg_gallery();






