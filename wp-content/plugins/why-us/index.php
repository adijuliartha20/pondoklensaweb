<?php 
/*
 * Plugin Name:       Why Us
 * Description:       Handle the why us content
 * Version:           1.0.0
 * Author:            Adi Juliartha
 * Author URI:        https://id.linkedin.com/in/adijuliartha
 */

 /*Registrasi Custom Post Type*/
 function wporg_cpt_whyus() {
    add_theme_support( 'post-thumbnails' );
	register_post_type('why-us',
		array(
			'labels'      => array(
				'name'          => __( 'Why Us', 'textdomain' ),
				'singular_name' => __( 'Why Us', 'textdomain' ),
			),
			'public'      => true,
			'has_archive' => true,
			'rewrite'     => array( 'slug' => 'why-us' ), // my custom slug
            'supports' => array( 'title', 'editor', 'thumbnail' )
		)
	);
}
add_action('init', 'wporg_cpt_whyus');

/*Registrasi Category Custom Post Type*/
function wporg_rtc_whyus() {
    $name = 'Category';
    $slug = 'cat-why-us';
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
    register_taxonomy($slug, [ 'why-us' ], $args );
}
add_action( 'init', 'wporg_rtc_whyus' );


?>