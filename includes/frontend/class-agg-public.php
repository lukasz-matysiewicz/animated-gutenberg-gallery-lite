<?php
namespace AGG\Frontend;

class AGG_Public {
    public function __construct() {
        // Add lightbox container to footer
        add_action('wp_footer', [$this, 'add_lightbox_container']);
    }

    public function add_lightbox_container() {
        // Only add if we're not in admin
        if (!is_admin()) {
            echo '<div id="aggGallery"></div>';
        }
    }
}