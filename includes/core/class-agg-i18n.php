<?php
namespace AGGL\Core;

class AGG_i18n {
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'animated-gutenberg-gallery-lite',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }
}