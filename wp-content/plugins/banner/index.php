<?php 
/*
 * Plugin Name:       Banner
 * Description:       Handle the banner content
 * Version:           1.0.0
 * Author:            Adi Juliartha
 * Author URI:        https://id.linkedin.com/in/adijuliartha
 */

 /*Registrasi Custom Post Type*/
 function wporg_cpt_banner() {  
    add_theme_support( 'post-thumbnails' );  
	register_post_type('banner',
		array(
			'labels'      => array(
				'name'          => __( 'Banner', 'textdomain' ),
				'singular_name' => __( 'Banner', 'textdomain' ),
			),
			'public'      => true,
			'has_archive' => true,
			'rewrite'     => array( 'slug' => 'banner' ), // my custom slug
            'supports' => array( 'title', 'editor', 'thumbnail' )
		)
	);
}
add_action('init', 'wporg_cpt_banner');

/*Registrasi Category Custom Post Type*/
function wporg_rtc_banner() {
    $name = 'Category';
    $slug = 'banner-categories';
    $labels = array(
        'name'              => _x( $name, 'taxonomy general name' ),
        'singular_name'     => _x( $name, 'taxonomy singular name' ),
        'search_items'      => __( 'Search '.$name ),
        'all_items'         => __( 'All '.$name ),
        'parent_item'       => __( 'Parent '.$name ),
        'parent_item_colon' => __( 'Parent '.$name.':' ),
        'edit_item'         => __( 'Edit '.$name ),
        'update_item'       => __( 'Update '.$name ),
        'add_new_item'      => __( 'Add New '.$name ),
        'new_item_name'     => __( 'New '.$name.' Name' ),
        'menu_name'         => __( $name ),
    );
    $args   = array(
        'hierarchical'      => true, // make it hierarchical (like categories)
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => [ 'slug' => $slug ],
    );
    register_taxonomy($slug, [ 'banner' ], $args );
}
add_action( 'init', 'wporg_rtc_banner' );
add_action( 'init', function() {
    remove_post_type_support( 'banner', 'editor' );
}, 99);


/**
 * Register meta box(es).
 */
function wpdocs_register_meta_boxes_banner() {
    add_meta_box(
        'banner-template-custom-metabox', // Metabox HTML ID attribute
        'Meta Data', // Metabox title
        'set_page_template_metabox', // callback name
        'banner', // post type
        'normal', // context (advanced, normal, or side)
        'default', // priority (high, core, default or low)
    );  
}
add_action( 'add_meta_boxes', 'wpdocs_register_meta_boxes_banner' );

function set_page_template_metabox($post) {
    // Define the meta box form fields here

    // Display code/markup goes here. Don't forget to include nonces!
    // Add an nonce field so we can check for it later.
  	wp_nonce_field( 'myplugin_inner_custom_box_price_list', 'myplugin_inner_custom_box_price_list_nonce' );
    
    $arr_meta = arr_theme_metabox();
    temp_template_metabox($arr_meta, $post);
}

function arr_theme_metabox() {
    $arr_meta = array(
        'link'=> array('type'=>'text','title'=>'Link')
    );
    return $arr_meta;
}

function temp_template_metabox($arr_meta, $post){
	if(!empty($arr_meta)){
		foreach ($arr_meta as $key => $dt_field) {
			$type = $dt_field['type'];
			$title = $dt_field['title'];
			$value_meta = get_post_meta( $post->ID, $key, true );            
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
				 				#wp-'.$key.'-wrap .mce-toolbar-grp {display: none;} 
				 				#wp-'.$key.'-wrap .wp-editor-tabs {display: none;}
				 	   </style>';	
				 }
                 $settings = array();
				wp_editor( $value_meta, $key );
			}else{	
				echo '<input type="text" id="'.$key.'" name="'.$key.'" value="' . esc_attr( $value_meta ) . '" size="70" />';
			}
			echo '<br/></div>';
			
		}
	}
}

function save_custom_post_meta_page_home($post_id) {
    // Sanitize/validate post meta here, before calling update_post_meta()

    // Save logic goes here. Don't forget to include nonce checks!
    if ( ! isset( $_POST['myplugin_inner_custom_box_price_list_nonce'] ) )return $post_id;  
	$nonce = $_POST['myplugin_inner_custom_box_price_list_nonce'];  
	if (! wp_verify_nonce( $nonce, 'myplugin_inner_custom_box_price_list' ))return $post_id;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )  return $post_id;

	// Check the user's permissions.
	if ( 'price-list' == $_POST['post_type'] ) {
	if ( ! current_user_can( 'edit_page', $post_id ) ) return $post_id;
	} else { 
		if ( ! current_user_can( 'edit_post', $post_id ) ) return $post_id;
	}

    $arr_meta = arr_theme_metabox();
	foreach ($arr_meta as $key => $metabox) {
		if($metabox['type']=='wp_editor') $value_meta = $_POST[$key];
		else $value_meta = sanitize_text_field( $_POST[$key] );
		update_post_meta( $post_id, $key, $value_meta);
	}
}


add_action( 'save_post', 'save_custom_post_meta_page_home' );

?>