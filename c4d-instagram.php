<?php
/*
Plugin Name: C4D Instagram
Plugin URI: http://coffee4dev.com/
Description: Present your Instagram images
Author: Coffee4dev.com
Author URI: http://coffee4dev.com/
Text Domain: c4d-instagram
Version: 2.0.0
*/

define('C4DINSTAGRAM_PLUGIN_URI', plugins_url('', __FILE__));

add_action('wp_enqueue_scripts', 'c4d_instagram_safely_add_stylesheet_to_frontsite');
add_shortcode('c4d-instagram', 'c4d_instagram_shortcode');
add_action( 'wp_ajax_c4d_instagram_get_user', 'c4d_instagram_get_user' );
add_action('wp_ajax_nopriv_c4d_instagram_get_user', 'c4d_instagram_get_user');
add_filter( 'plugin_row_meta', 'c4d_instagram_plugin_row_meta', 10, 2 );

function c4d_instagram_plugin_row_meta( $links, $file ) {

    if ( strpos( $file, basename(__FILE__) ) !== false ) {
        $new_links = array(
            'visit' => '<a href="http://coffee4dev.com">Visit Plugin Site</<a>',
            'forum' => '<a href="http://coffee4dev.com/forums/">Forum</<a>',
            'premium' => '<a href="http://coffee4dev.com">Premium Support</<a>'
        );
        
        $links = array_merge( $links, $new_links );
    }
    
    return $links;
}

function c4d_instagram_get_user() {
	$user = $_GET['username'];
	$url = 'https://www.instagram.com/' . esc_attr($user) . '/media/';
	$response = wp_remote_get($url);

	if( wp_remote_retrieve_response_code($response) !== 200 )
	{
		esc_html_e('Something went wrong.', 'c4d-instagram');
	}

	if( !is_wp_error($response) )
	{
		$data = $response['body'];
	}
	else
	{
		$data = $response->get_error_message();
	}
	echo $data; 
	wp_die(); // this is required to terminate immediately and return a proper response
}

function c4d_instagram_safely_add_stylesheet_to_frontsite( $page ) {
	if(!defined('C4DPLUGINMANAGER')) {
		wp_enqueue_style( 'c4d-instagram-frontsite-style', C4DINSTAGRAM_PLUGIN_URI.'/assets/default.css' );
		wp_enqueue_script( 'c4d-instagram-frontsite-plugin-js', C4DINSTAGRAM_PLUGIN_URI.'/assets/default.js', array( 'jquery' ), false, true ); 
	}
	wp_enqueue_style( 'fancybox', C4DINSTAGRAM_PLUGIN_URI.'/fancybox/jquery.fancybox.min.css'); 
	wp_enqueue_script( 'fancybox', C4DINSTAGRAM_PLUGIN_URI.'/fancybox/jquery.fancybox.min.js', array( 'jquery' ), false, true ); 
    wp_localize_script( 'jquery', 'c4d_instagram',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}

function c4d_instagram_shortcode($params){
	$uid = 'c4d-instagram-'.uniqid();
	$html = '<script>(function($){
		$(document).ready(function(){
			c4dInstagram["'.$uid.'"] = '.json_encode($params).';
		});	
	})(jQuery);</script>';
	$html .= '<div id="'.$uid.'" class="c4d-instagram" data-user="'.$params['username'].'"></div>';
	return $html;
}