# WP ALL IMPORT + (Variation Images Gallery for WooCommerce)[https://wordpress.org/plugins/woo-product-variation-gallery/]

This plugin allow to import the variation images with WP ALL IMPORT. 

Tested with `Update Existing Items` process only. Not cover for `Create New Item` yet. 

This is the original code to do the import in function.php 

```php
function my_pmxi_article_data( $articleData, $import, $post_to_update, $current_xml_node ) {
    error_log('Article data: ' . print_r($articleData, true));
    error_log('Post to update data: ' . print_r($post_to_update, true));
    
    $post_id = $post_to_update->ID; 
    
    // Delete the existing variations image
    delete_post_meta($post_id, 'rtwpvg_images');
    
    // Do something with the article data.
    return $articleData;
}
add_filter('pmxi_article_data', 'my_pmxi_article_data', 10, 4);

function wpai_image_imported( $post_id, $att_id, $filepath, $is_keep_existing_images = '' ) {
        $existing_post_gallery_images = get_post_meta($post_id, 'rtwpvg_images', true);
        
        if (!is_array($existing_post_gallery_images)) {
            $existing_post_gallery_images = [];
        }
        
        array_push($existing_post_gallery_images, $att_id);
        
        $existing_post_gallery_images = array_values(array_unique($existing_post_gallery_images));
        
        update_post_meta( $post_id, 'rtwpvg_images', $existing_post_gallery_images );
}
add_action( 'pmxi_gallery_image', 'wpai_image_imported', 10, 4 );
```