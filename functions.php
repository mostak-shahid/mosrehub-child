<?php
require_once('theme-init/plugin-update-checker.php');
$themeInit = Puc_v4_Factory::buildUpdateChecker(
	'https://raw.githubusercontent.com/mostak-shahid/update/master/mosrehub-child.json',
	__FILE__,
	'mosrehub-child'
);
add_action( 'wp_enqueue_scripts', 'enqueue_parent_theme_style' );
function enqueue_parent_theme_style() {
    
    wp_enqueue_style( 'slick', get_stylesheet_directory_uri() . '/plugins/slick/slick.css' );
    wp_enqueue_style( 'slick-theme', get_stylesheet_directory_uri() . '/plugins/slick/slick-theme.css' );
    wp_enqueue_script('slick', get_stylesheet_directory_uri() . '/plugins/slick/slick.min.js', 'jquery');
    
    wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
    
	if (is_rtl()) {
		 wp_enqueue_style( 'parent-rtl', get_template_directory_uri().'/rtl.css', array(), RH_MAIN_THEME_VERSION);
	}     
}
require_once 'carbon-fields.php';
require_once 'shortcodes.php';