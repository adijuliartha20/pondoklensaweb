<?php 
/*
 * Plugin Name:       Testimonial
 * Description:       Handle the testimonial content
 * Version:           1.0.0
 * Author:            Adi Juliartha
 * Author URI:        https://id.linkedin.com/in/adijuliartha
 */

 /*Registrasi Custom Post Type*/
 function wporg_cpt_testimonial() {  
    add_theme_support( 'post-thumbnails' );  
	register_post_type('testimonial',
		array(
			'labels'      => array(
				'name'          => __( 'Testimonial', 'textdomain' ),
				'singular_name' => __( 'Testimonial', 'textdomain' ),
			),
			'public'      => true,
			'has_archive' => true,
			'rewrite'     => array( 'slug' => 'testimonial' ), // my custom slug
            'supports' => array( 'title', 'editor', 'thumbnail' )
		)
	);
}
add_action('init', 'wporg_cpt_testimonial');

/*Registrasi Category Custom Post Type*/
function wporg_rtc_testimonial() {
    $name = 'Category';
    $slug = 'cat-testimonial';
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
    register_taxonomy($slug, [ 'testimonial' ], $args );
}
add_action( 'init', 'wporg_rtc_testimonial' );
?>