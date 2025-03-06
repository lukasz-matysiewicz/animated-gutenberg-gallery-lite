<?php
namespace AGGL\Core;

class AGGL_Init {
    protected $loader;

    public function __construct() {
        $this->loader = new \AGGL\Core\AGGL_Loader();
        $this->define_hooks();
    }

    private function define_hooks() {
        $plugin_i18n = new \AGGL\Core\AGGL_i18n();
        $plugin_assets = new \AGGL\Core\AGGL_Assets();
        $plugin_gallery = new \AGGL\Core\AGGL_Gallery();
        
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    
        // Initialize public class
        $plugin_public = new \AGGL\Frontend\AGGL_Public();
        
        if (is_admin()) {
            $plugin_admin = new \AGGL\Admin\AGGL_Admin();
        }
    }

    public function run() {
        $this->loader->run();
    }
}