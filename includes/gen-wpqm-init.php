<?php if(!defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
function gen_wpqm_admin_menu(){
    $wpqm_icon_url= GEN_WPQM_BASE_URL . '/images/logo.jpg';
	add_object_page('Product Menu', 'Product Menu', 'edit_theme_options', __FILE__, 'gen_wpqm_setting',$wpqm_icon_url);
    add_submenu_page( __FILE__, 'Product Menu','Product Menu', 'edit_theme_options', __FILE__,'gen_wpqm_setting');  
	add_submenu_page( __FILE__, 'PRO Version','PRO Version', 'edit_theme_options', 'pro-version','gen_pro_version');  
}
function gen_pro_version(){
	include_once('usts-wpqm-product-menu-pro-version.php');
}
function gen_wpqm_install(){
  gen_wpqm_setting_reset();
}
function gen_wpqm_uninstall(){}

add_action('admin_menu', 'gen_wpqm_admin_menu');

?>