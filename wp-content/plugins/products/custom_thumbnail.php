<?php 
//Set New Field custom in taxonomy
$slug_taxonomy_product = 'product-categories';
function taxonomy_add_custom_field() {
    ?>
    <div class="form-field term-image-wrap">
        <label for="cat-image"><?php _e( 'Image' ); ?></label>
        <p><a href="#" class="aw_upload_image_button button button-secondary"><?php _e('Upload Image'); ?></a></p>
        <input type="text" name="category_image" id="cat-image" value="" size="40" />
    </div>
    <?php
}
add_action( $slug_taxonomy_product.'_add_form_fields', 'taxonomy_add_custom_field', 10, 2 );
 
function taxonomy_edit_custom_field($term) {
    $image = get_term_meta($term->term_id, 'category_image', true);
    ?>
    <tr class="form-field term-image-wrap">
        <th scope="row"><label for="category_image"><?php _e( 'Image' ); ?></label></th>
        <td>
            <p><a href="#" class="aw_upload_image_button button button-secondary"><?php _e('Upload Image'); ?></a></p><br/>
            <input type="text" name="category_image" id="cat-image" value="<?php echo $image; ?>" size="40" />
        </td>
    </tr>
    <?php
}
add_action( $slug_taxonomy_product.'_edit_form_fields', 'taxonomy_edit_custom_field', 10, 2 );

//Add Javascript
function aw_include_script() {
  
    if ( ! did_action( 'wp_enqueue_media' ) ) {
        wp_enqueue_media();
    }
  
    //wp_enqueue_script( 'awscript', get_stylesheet_directory_uri() . '/assets/js/awscript.js', array('jquery'), null, false );
    wp_enqueue_script( 'awscript', plugin_dir_url( __FILE__ ) . '/js/awscript.js', array('jquery'), null, false );
}
add_action( 'admin_enqueue_scripts', 'aw_include_script' );

//Action save edit custom field
function save_taxonomy_custom_meta_field( $term_id ) {
    if ( isset( $_POST['category_image'] ) ) {
        update_term_meta($term_id, 'category_image', $_POST['category_image']);
    }
}  
add_action( 'edited_'.$slug_taxonomy_product, 'save_taxonomy_custom_meta_field', 10, 2 );  
add_action( 'create_'.$slug_taxonomy_product, 'save_taxonomy_custom_meta_field', 10, 2 );
//https://artisansweb.net/add-image-field-taxonomy-wordpress/
?>