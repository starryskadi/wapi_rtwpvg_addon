<?php 
/*
Plugin Name: WP All Import - Variation Images Gallery for WooCommerce Add-On 
Description: Import the variation images with WP ALL IMPORT 
Version: 1.0
Author: Starry Skadi
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
include 'rapid-addon.php';

 
class WAPI_RTWPVG_ADDON {
    protected static $instance;

    protected $add_on;

    static public function get_instance() {
        if ( self::$instance == NULL ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function __construct() {        
        $this->add_on = new RapidAddon( 'Variation Images Gallery Settings', 'wpai_rtwpvg_addon_woo' );
        $this->add_on->add_field('rtwpvg_images_new', 'Replace existing images', 'radio', array(
            'yes' => 'Yes',
            'no' => 'No'
        ));

        $this->add_on->import_images( 'rtwpvg_images', 'Variation Images Gallery Images', 'images', [ $this, 'import_image' ]);

        $this->add_on->set_import_function([ $this, 'import' ]);
        

        add_action( 'init', [ $this, 'init' ] );
    }

    public function import( $post_id, $data, $import_options, $article) {
        $this->will_replace_image = $data['rtwpvg_images_new'] === 'yes';

        if ($this->will_replace_image) {
             // Delete the existing variations image
            delete_post_meta($post_id, 'rtwpvg_images');
        }
    }

    public function import_image( $post_id, $attachment_id, $image_filepath, $import_options) {        
        $existing_post_gallery_images = get_post_meta($post_id, 'rtwpvg_images', true);
        
        if (!is_array($existing_post_gallery_images)) {
            $existing_post_gallery_images = [];
        }
        
        array_push($existing_post_gallery_images, $attachment_id);
        
        $existing_post_gallery_images = array_values(array_unique($existing_post_gallery_images));
        
        update_post_meta( $post_id, 'rtwpvg_images', $existing_post_gallery_images );
    }

    public function init() {
        $this->add_on->run(array(
            'plugins' => array( 
                'woo-product-variation-gallery/woo-product-variation-gallery.php',
                // 'wp-all-export/wp-all-export.php'
            )
        ));
    }
}

function wpai_rtwpvg_activate() {
    if ( is_plugin_active( 'wp-all-import/wp-all-import.php' ) || is_plugin_active( 'wp-all-import-pro/wp-all-import-pro.php' )) {
        // The other plugin is not active, throw an error
        
    } else {
        wp_die(
            'This plugin requires the WP ALL IMPORT (or) WP ALL IMPORT PRO to be installed and activated. Please install and activate it first.',
            'Plugin Dependency Check',
            array(
                'back_link' => true, // This will provide a back link to the plugins page
            )
        );
    }
}

// Register the activation hook
register_activation_hook( __FILE__, 'wpai_rtwpvg_activate' );

WAPI_RTWPVG_ADDON::get_instance();