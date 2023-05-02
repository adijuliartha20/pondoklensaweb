<?php 
add_action( 'wp_enqueue_scripts', 'theme_name_scripts' );
function theme_name_scripts() {
	$v = '1.0.0'.time();

    wp_enqueue_style( 'style', get_stylesheet_uri() , array(), $v );

    $dt = array();
	$dt['url'] = admin_url('admin-ajax.php');
	$dt['please_wait'] = pll__('Please wait');

	wp_localize_script('script-v','d',$dt);
}
?>