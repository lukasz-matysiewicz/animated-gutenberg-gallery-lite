<?php
namespace AGGL\Core;

class AGG_Init {
    protected $loader;

    public function __construct() {
        $this->loader = new \AGGL\Core\AGG_Loader();
        $this->define_hooks();
    }

    private function define_hooks() {
        $plugin_i18n = new \AGGL\Core\AGG_i18n();
        $plugin_assets = new \AGGL\Core\AGG_Assets();
        $plugin_gallery = new \AGGL\Core\AGG_Gallery();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

        if (is_admin()) {
            $plugin_admin = new \AGGL\Admin\AGG_Admin();
        }
    }

    public function run() {
        $this->loader->run();
    }
}