<?php
/*
Plugin Name: Restaurant Food Menu for WooCommerce
Plugin URI: http://www.upscalethought.com
Description: This is a WooCommerce plugin that list all the products on one page and allow users to quickly order products.
Version: 1.0
Author: UpScaleThought
Author URI: http://www.upscalethought.com
*/

define("GEN_WPQM_BASE_URL", WP_PLUGIN_URL.'/'.plugin_basename(dirname(__FILE__)));

include ('includes/gen-wpqm-admin.php');
include ('includes/gen-wpqm-view.php');
include ('includes/gen-wpqm-init.php');

function gen_wpqm_init(){
  wp_enqueue_style('wpqm-css',GEN_WPQM_BASE_URL.'/css/wpqm.css');
  wp_enqueue_style('colorbox-css',GEN_WPQM_BASE_URL.'/css/colorbox.css'); 
  wp_enqueue_style('ddaccordion-css',GEN_WPQM_BASE_URL.'/css/ddaccordion.css'); 
  
  wp_enqueue_style('tab-css',GEN_WPQM_BASE_URL.'/css/usts-tab-style.css'); 
  
  wp_enqueue_script('jquery');
  wp_enqueue_script('wpqm-jscolor', plugins_url( '/js/colorpicker/jscolor.js', __FILE__ ));
  wp_enqueue_script('wpqm-tooltip', plugins_url( '/js/wpqm_tooltip.js', __FILE__ ));    
  wp_enqueue_script('jquery.colorbox', plugins_url( '/js/jquery.colorbox.js', __FILE__ ));
  
  wp_enqueue_script('wpqm-ddaccordion', plugins_url( '/js/ddaccordion.js', __FILE__ ));
  wp_enqueue_script('wpqm-ddaccordion2', plugins_url( '/js/ddaccordion2.js', __FILE__ ));
}

add_action('init','gen_wpqm_init');
register_activation_hook( __FILE__, 'gen_wpqm_install');
register_deactivation_hook( __FILE__, 'gen_wpqm_uninstall');


function gen_ajax_wpqm_popup_data(){
	global $wpdb;
	global $woocommerce;
	$pid=$_REQUEST['pid'];
	$product = get_product($pid);
	ob_start();
	echo gen_popup_data_content($product,$pid);
	$output_string = ob_get_contents();
    ob_end_clean();
    return $output_string; 
	
}
function gen_popup_data_content($product,$pid){
	global $wpdb;
	global $woocommerce;
?>
	<div class="wpqm_popup_con_main">
	  <div class="wpqm_popup_con_1">
		Product Details
	  </div>
	  <div class="wpqm_popup_con_2">
		<div class="wpqm_popup_con_left">
			<?php 
	
			if (has_post_thumbnail($pid)){ 
			  $imgUlr = wp_get_attachment_url( get_post_thumbnail_id($pid) );
			  $src = '<img src="'.esc_url($imgUlr).'" alt="Placeholder" width="170" />';
			} else {
			  $imgUlr=GEN_WPQM_BASE_URL.'/images/placeholder.png';            
			  $src = '<img src="'.$imgUlr.'" alt="Placeholder" width="170"  />';
			}
			echo $src;
			?>
		</div>
		<div class="wpqm_popup_con_right">
				  <div class="wpqm_popup_title">
					<?php echo $product->get_title();?>
				  </div>
				  <div class="wpqm_popup_price">
					<?php //woocommerce_get_template( 'loop/price.php' );
					wc_get_template('loop/price.php');
					?>
				  </div>    
				  <!-----------sku--------->
				  <div class="wpqm_product_meta">
					<?php do_action( 'woocommerce_product_meta_start' ); ?>
					<?php if ( $product->is_type( array( 'simple', 'variable' ) ) && get_option( 'woocommerce_enable_sku' ) == 'yes' && $product->get_sku() ) : ?>
					  <span itemprop="productID" class="sku_wrapper"><?php _e( 'SKU:', 'woocommerce' ); ?> <span class="sku"><?php echo $product->get_sku(); ?></span>.</span>
					<?php endif; ?>
					<?php
					  $size = sizeof( get_the_terms( $_GET['pid'], 'product_cat' ) );
					  echo $product->get_categories( ', ', '<span class="posted_in">' . _n( 'Category:', 'Categories:', $size, 'woocommerce' ) . ' ', '.</span>' );
					?>
					<?php
					  $size = sizeof( get_the_terms( $_GET['pid'], 'product_tag' ) );
					  echo $product->get_tags( ', ', '<br /><span class="tagged_as">' . _n( 'Tag:', 'Tags:', $size, 'woocommerce' ) . ' ', '.</span>' );
					?>
					<?php do_action( 'woocommerce_product_meta_end' ); ?>
				  </div>
		</div>
	
		<div class="wpqm_clear"></div>
		
	  </div>
	<?php
	$product_description = get_post($pid)->post_content;
	if($product_description!=''){
	  echo '<div class="wpqm_popup_con_3">'.esc_attr($product_description).'</div>';
	}
	?>
	</div> 

	<style>
	.wpqm_popup_con_main{
		padding: 0px;
		margin: 0px;
		width: 500px;
		min-height: 400px;
		padding: 10px;
	  }
	  .wpqm_popup_con_1{
		width: 100%;
		font-size: 20px;
		color: brown;
	  }
	  .wpqm_popup_con_2{
		width: 100%;
		margin-top: 10px;
	  }
	  .wpqm_popup_con_3{
		width: 100%;
		margin-top: 10px;
		font-size: 12px;
		color: #666666;
	  }
	  
	  .wpqm_popup_con_left{
		float: left;
		width: 50%;
		margin: 0px;    
	  }
	  .wpqm_popup_con_left img{
		border: solid 2px #CCCCCC;
	  }  
	  .wpqm_popup_con_right{
		float: right;
		width: 50%;    
	  }
	  .wpqm_clear{
		clear: both;
	  }
	  .wpqm_popup_title{
		font-size: 22px;
		color: darkorange;
		margin-bottom: 20px;
	  }
	  .wpqm_popup_price{
		font-size: 20px;    
		margin-bottom: 20px;
	  }
	</style>
<?php    
}

add_action( 'wp_ajax_nopriv_gen_ajax_wpqm_popup_data','gen_ajax_wpqm_popup_data' );
add_action( 'wp_ajax_gen_ajax_wpqm_popup_data', 'gen_ajax_wpqm_popup_data' );