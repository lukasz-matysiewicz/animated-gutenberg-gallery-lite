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

class AGG_Init {
    protected $loader;

    public function __construct() {
        $this->loader = new AGG_Loader();
        $this->define_hooks();
    }

    private function define_hooks() {
        // Initialize i18n first
        $plugin_i18n = new AGG_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    
        // Rest of initialization
        new AGG_Assets();
        new AGG_Gallery();
        
        if (is_admin()) {
            new \AGG\Admin\AGG_Admin();
        }
    }

    public function run() {
        $this->loader->run();
    }
}