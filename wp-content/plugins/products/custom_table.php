<?php 
$current_db_version = 1.0;
$tableProducts = 'products';
$installed_db_version = get_option('db_'.$tableProducts.'_version', 0);

if ($current_db_version > $installed_db_version) {
    app_create_products_db();
    update_option('db_'.$tableProducts.'_version', $current_db_version);
}

function app_create_products_db() {
    global $wpdb;

    // define query
    $query = array(
        "CREATE TABLE {$wpdb->prefix}$tableProducts (
            ID BIGINT NOT NULL AUTO_INCREMENT,
            specifications LONGTEXT NOT NULL,
            overview LONGTEXT NOT NULL,
            included LONGTEXT NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            price_promo DECIMAL(10,2) NOT NULL,
            availability VARCHAR(255) NOT NULL,
            PRIMARY KEY (ID)
        )"
    );

    // execute query
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($query);
}



/*SETUP CUSTOM FORM*/
/**
 * Register meta box(es).
 */
function wpdocs_register_meta_boxes_products() {
    add_meta_box(
        'products-template-custom-metabox', // Metabox HTML ID attribute
        'Meta Data', // Metabox title
        'set_page_template_metabox_products', // callback name
        'products', // post type
        'normal', // context (advanced, normal, or side)
        'default', // priority (high, core, default or low)
    );  
}
add_action( 'add_meta_boxes', 'wpdocs_register_meta_boxes_products' );

function set_page_template_metabox_products($post) {
    // Define the meta box form fields here

    // Display code/markup goes here. Don't forget to include nonces!
    // Add an nonce field so we can check for it later.
  	wp_nonce_field( 'myplugin_inner_custom_box_products', 'myplugin_inner_custom_box_products_nonce' );
    
    $arr_meta = arr_theme_metabox_products();
    temp_template_metabox_products($arr_meta, $post);
}

function arr_theme_metabox_products() {
    $arr_meta = array(        
        'specifications'=> array('type'=>'wp_editor','title'=>'Specifications'),
        'overview'=> array('type'=>'wp_editor','title'=>'Overview'),
        'included'=> array('type'=>'wp_editor','title'=>'Included'),
        'price'=> array('type'=>'text','title'=>'Price'),
        'price_promo'=> array('type'=>'text','title'=>'Price Promo'),
        'availability'=> array('type'=>'text','title'=>'Availability')        
    );
    return $arr_meta;
}

function temp_template_metabox_products($arr_meta, $post){
	if(!empty($arr_meta)){
        $getData = app_get_data($post->ID);//print_r($getData);
        $data = array('specifications' => '','overview' => '','included' => '','price' => '','price_promo' => '', 'availability' => '');
               
        if(isset($getData) && !empty($getData)){
            $data['specifications'] = $getData->specifications;
            $data['overview'] = $getData->overview;
            $data['included'] = $getData->included;
            $data['price'] = $getData->price;
            $data['price_promo'] = $getData->price_promo;
            $data['availability'] = $getData->availability;
        }

		foreach ($arr_meta as $key => $dt_field) {
			$type = $dt_field['type'];
			$title = $dt_field['title'];
			$value_meta = $data[$key];           
			$unit_lable = (isset($dt_field['unit_lable'])? '&nbsp;'.$dt_field['unit_lable']:'');

			echo '<div style="margin-bottom:10px;"><label for="'.$key.'" style="width:100px; float:left; margin-top:5px">';
			   _e( $title, 'myplugin_textdomain' );
			echo '</label> ';

			if($type=='select'){
				$options = $dt_field['option'];
				echo '<select id="'.$key.'" name="'.$key.'" >';
				foreach ($options as $key_opt => $label) {
					echo '<option value="'.$key_opt.'" '.($key_opt==$value_meta?'selected':'').'>'.$label.'</option>';
				}
				echo '</select>'.$unit_lable;
			}else if($type=='textarea'){
				echo '<textarea class="" name="'.$key.'" style="width:50%; min-width:500px;min-height:100px;">' . esc_attr( $value_meta ) . '</textarea>';
			}else if($type=='number'){				
				echo '<input type="number" id="'.$key.'" name="'.$key.'" value="' . esc_attr( $value_meta ) . '" size="70" />'.$unit_lable;
			}else if($type=='wp_editor'){
				 if(isset($dt_field['hide_button']) &&  $dt_field['hide_button']){
				 	echo '<style> 
				 				/*#wp-'.$key.'-wrap .mce-toolbar-grp {display: none;} 
				 				#wp-'.$key.'-wrap .wp-editor-tabs {display: none;}*/
				 	   </style>';	
				 }

                $args = array(
                    'tinymce'       => array(
                        'toolbar1'      => 'table,bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
                        'toolbar2'      => '',
                        'toolbar3'      => '',
                    ),
                );
                
				wp_editor( $value_meta, $key );
			}else{	
				echo '<input type="text" id="'.$key.'" name="'.$key.'" value="' . esc_attr( $value_meta ) . '" size="70" />';
			}
			echo '<br/></div>';
			
		}
	}
}

function save_custom_post_meta_page_products($post_id) {
    // Sanitize/validate post meta here, before calling update_post_meta()

    // Save logic goes here. Don't forget to include nonce checks!
    if ( ! isset( $_POST['myplugin_inner_custom_box_products_nonce'] ) )return $post_id;  
	$nonce = $_POST['myplugin_inner_custom_box_products_nonce'];  
	if (! wp_verify_nonce( $nonce, 'myplugin_inner_custom_box_products' ))return $post_id;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )  return $post_id;

	// Check the user's permissions.
	if ( 'products' == $_POST['post_type'] ) {
	if ( ! current_user_can( 'edit_page', $post_id ) ) return $post_id;
	} else { 
		if ( ! current_user_can( 'edit_post', $post_id ) ) return $post_id;
	}

    $specifications = $_POST['specifications'];
    $overview = $_POST['overview'];
    $included = $_POST['included'];
    $price = $_POST['price'];
    $price_promo = $_POST['price_promo'];
    $availability = $_POST['availability'];

    global $wpdb;
    $getData = app_get_data($post_id);
    if(isset($getData) && !empty($getData)){
        $wpdb->update($wpdb->prefix . 'products', array(
            // field to update
            'specifications' => $specifications,
            'overview' => $overview,
            'included' => $included,
            'price' => $price,
            'price_promo' => $price_promo,
            'availability' => $availability
        ), array(
            // where clause
            'ID' => $post_id
        ));
    }else{
        $wpdb->insert($wpdb->prefix . 'products', array(
            'ID' => $post_id,
            'specifications' => $specifications,
            'overview' => $overview,
            'included' => $included,
            'price' => $price,
            'price_promo' => $price_promo,
            'availability' => $availability
        ));
    }
}

function app_get_data($post_id) {
    global $wpdb;
    $query = $wpdb->prepare("SELECT * FROM $wpdb->prefix"."products WHERE ID=%s;", $post_id );    
    $results = $wpdb->get_row( $query );
    return $results;
}

function app_delete_data($post_id) {
    global $wpdb;
    $wpdb->delete($wpdb->prefix . 'products', array(
      'ID' => $post_id
    ));
}

add_action( 'save_post', 'save_custom_post_meta_page_products' );
?>