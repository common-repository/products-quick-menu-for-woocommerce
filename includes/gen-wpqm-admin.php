<?php if(!defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
function gen_wpqm_setting_reset(){
  update_option('wpqm_display_variation',1);
  update_option('wpqm_image_size',40);
  update_option('wpqm_display_image_preview',1);
  update_option('wpqm_exc_cat','');
  
  update_option('wpqm_menu_bg_color','F52727');
  update_option('wpqm_menu_hover_color','222222');
  update_option('wpqm_menu_text_color','FFFFFF');
  update_option('wpqm_submenu_bg_color','FFFFFF');
  update_option('wpqm_prod_name_color','000000');
  update_option('wpqm_prod_name_hover_color','FFFFFF');
  update_option('wpqm_prod_des_color','000000');
  
  update_option('wpqm_search_bg_color','ffffff');
  update_option('wpqm_search_border_color','F52727');
  update_option('wpqm_search_text_color','F52727');
      
  
  
}

function gen_wpqm_product_dropdown_categories( $args = array(), $deprecated_hierarchical = 1, $deprecated_show_uncategorized = 1, $deprecated_orderby = '' ) {
	global $wp_query;
  global $woocommerce;
	if ( ! is_array( $args ) ) {
    
		_deprecated_argument( 'wc_product_dropdown_categories()', '2.1', 'show_counts, hierarchical, show_uncategorized and orderby arguments are invalid - pass a single array of values instead.' );

		$args['show_counts']        = $args;
		$args['hierarchical']       = $deprecated_hierarchical;
		$args['show_uncategorized'] = $deprecated_show_uncategorized;
		$args['orderby']            = $deprecated_orderby;
	}

	$current_product_cat = isset( $wp_query->query['product_cat'] ) ? $wp_query->query['product_cat'] : '';
	$defaults            = array(
		'pad_counts'         => 1,
		'show_counts'        => 1,
		'hierarchical'       => 1,
		'hide_empty'         => 1,
		'show_uncategorized' => 0,
		'orderby'            => 'name',
		'selected'           => $current_product_cat,
		'menu_order'         => false
	);

	$args = wp_parse_args( $args, $defaults );

	if ( $args['orderby'] == 'order' ) {
		$args['menu_order'] = 'asc';
		$args['orderby']    = 'name';
	}

	$terms = get_terms( 'product_cat', apply_filters( 'wc_product_dropdown_categories_get_terms_args', $args ) );
  
  if (get_option('wpqm_exc_cat')){
    
      $exc_cats_slug=  explode(',', get_option('wpqm_exc_cat'));
      foreach ($terms as $key=>$val){
        if(in_array($val->slug, $exc_cats_slug)){
          unset($terms[$key]);
        }
      }    
  }
	if ( ! $terms ) {
		return;
	}

	$output  = "<select name='product_cat' class='dropdown_product_cat'>";
	$output .= '<option value="" ' .  selected( $current_product_cat, '', false ) . '>' . __( 'Select a category', 'woocommerce' ) . '</option>';
	$output .= wc_walk_category_dropdown_tree( $terms, 0, $args );
	if ( $args['show_uncategorized'] ) {
		$output .= '<option value="0" ' . selected( $current_product_cat, '0', false ) . '>' . __( 'Uncategorized', 'woocommerce' ) . '</option>';
	}
	$output .= "</select>";

	echo $output;
}

function gen_wpqm_setting(){
  //------------------
    //wpqm_cat_data();
  //------------------
  
    if (!class_exists('Woocommerce')) {
      echo '<div id="message" class="error"><p>Please Activate Wp WooCommerce Plugin</p></div>';
      return false;
    }
    
    if(isset($_POST['wpqm_status_submit']) && $_POST['wpqm_status_submit']==1){
	  if(check_admin_referer('usts_add_product_menu_settings_nonce')){
	  	if( current_user_can( 'administrator' ) ){	
		  update_option('wpqm_display_variation',sanitize_text_field($_POST['wpqm_display_variation']));
		  update_option('wpqm_image_size',sanitize_text_field($_POST['wpqm_image_size']));
		  update_option('wpqm_display_image_preview',sanitize_text_field($_POST['wpqm_display_image_preview']));
		  update_option('wpqm_exc_cat',sanitize_text_field($_POST['wpqm_exc_cat'])); 
		  
		  update_option('wpqm_menu_hover_color',sanitize_text_field($_POST['wpqm_menu_hover_color']));
		  update_option('wpqm_menu_text_color',sanitize_text_field($_POST['wpqm_menu_text_color']));
		  update_option('wpqm_prod_name_color',sanitize_text_field($_POST['wpqm_prod_name_color']));
		  update_option('wpqm_prod_name_hover_color',sanitize_text_field($_POST['wpqm_prod_name_hover_color']));
		  update_option('wpqm_prod_des_color',sanitize_text_field($_POST['wpqm_prod_des_color']));
		  
		  update_option('wpqm_search_bg_color',sanitize_text_field($_POST['wpqm_search_bg_color']));
		  update_option('wpqm_search_border_color',sanitize_text_field($_POST['wpqm_search_border_color']));
		  update_option('wpqm_search_text_color',sanitize_text_field($_POST['wpqm_search_text_color']));
		}
		else{
				echo '<div id="message" class="updated fade"><p>Your Have to be Adminiastrator to Change Settings!</p></div>';
		}  
	  }
    }

    if(isset($_POST['wpqm_status_submit']) && $_POST['wpqm_status_submit']==2){
      if(check_admin_referer('usts_add_product_menu_settings_nonce')){
	  	if( current_user_can( 'administrator' ) ){
	  		gen_wpqm_setting_reset();   
		}
		else{
			echo '<div id="message" class="updated fade"><p>Your Have to be Adminiastrator to Change Settings!</p></div>';
		}
	  }		
	  
    }    
    ?>
    <h2>Settings</h2>
    <form method="post" id="wpqm_options">
    <?php wp_nonce_field('usts_add_product_menu_settings_nonce'); ?>		
        <input type="hidden" name="wpqm_status_submit" id="wpqm_status_submit" value="2"  />
    <div class="tabs">
      <div class="tab" style="z-index:100;">
         <input type="radio" id="tab-1" name="tab-group-1" checked>
         <label for="tab-1">Core Setting</label>
         <div class="content">
      <table width="100%" cellspacing="2" cellpadding="5" class="editform">
        <tr style="display: none;" valign="top"> 
          <td width="150" scope="row">Display Variations:</td>
          <td>
              <select name="wpqm_display_variation">
                  <option value="1"<?php if (get_option('wpqm_display_variation')=='1'):?> selected="selected"<?php endif;?>>Yes</option>
<!--                  <option value="0"<?php //if (get_option('wpqm_display_variation')=='0'):?> selected="selected"<?php //endif;?>>No</option>                -->
              </select>
          </td>
        </tr>
        
        <tr valign="top"> 
          <td width="150" scope="row">Product image size:</td>
          <td>
              <select name="wpqm_image_size">
                  <option value="16"<?php if (get_option('wpqm_image_size')==16):?> selected="selected"<?php endif;?>>16x16</option>
                  <option value="32"<?php if (get_option('wpqm_image_size')==32):?> selected="selected"<?php endif;?>>32x32</option>
                  <option value="40"<?php if (get_option('wpqm_image_size')==40):?> selected="selected"<?php endif;?>>40x40</option>
                  <option value="48"<?php if (get_option('wpqm_image_size')==48):?> selected="selected"<?php endif;?>>48x48</option>
                  <option value="64"<?php if (get_option('wpqm_image_size')==64):?> selected="selected"<?php endif;?>>64x64</option>
              </select>
          </td>
        </tr>
        
        <tr valign="top"> 
          <td width="150" scope="row">Display Image Preview:</td>
          <td>
              <select name="wpqm_display_image_preview">
                  <option value="1"<?php if (get_option('wpqm_display_image_preview')=='1'):?> selected="selected"<?php endif;?>>Yes</option>
                  <option value="0"<?php if (get_option('wpqm_display_image_preview')=='0'):?> selected="selected"<?php endif;?>>No</option>                
              </select>
          </td>
        </tr>
          
        <tr valign="top"> 
            <td width="150" scope="row">Exclude Category</td>
            <td>
                <input type="text" name="wpqm_exc_cat" id="wpqm_exc_cat" value="<?php if (get_option('wpqm_exc_cat')){echo get_option('wpqm_exc_cat');}?>" />
                [Comma seperate Category Slug i.e. vegetables, meat ]
            </td>
        </tr>
        </table>
        </div> 
      </div> 
      
    <div class="tab" style="z-index:100;">
       <input type="radio" id="tab-2" name="tab-group-1">
         <label for="tab-2">Color Settings</label>
         <div class="content" >  
      <table>
      	<tr>
          <td>Item Name Color:</td>
          <td>
            <input type="text" name="wpqm_prod_name_color" size="10" id="wpqm_prod_name_color" class="color" value="<?php echo esc_attr(get_option('wpqm_prod_name_color'))?>" /> 
          </td>
        </tr>
        <tr>
          <td>Item Name Hover Color:</td>
          <td>
            <input type="text" name="wpqm_prod_name_hover_color" size="10" id="wpqm_prod_name_hover_color" class="color" value="<?php echo esc_attr(get_option('wpqm_prod_name_hover_color'))?>" /> 
          </td>
        </tr>
        <tr>
          <td>Item Description Color:</td>
          <td>
            <input type="text" name="wpqm_prod_des_color" size="10" id="wpqm_prod_des_color" class="color" value="<?php echo esc_attr(get_option('wpqm_prod_des_color'))?>" /> 
          </td>
        </tr>

        <tr>
          <td>Search Button Background Color:</td>
          <td>
            <input type="text" name="wpqm_search_bg_color" size="10" id="wpqm_search_bg_color" class="color" value="<?php echo esc_attr(get_option('wpqm_search_bg_color'))?>" /> 
          </td>
        </tr>
        <tr>
          <td>Search button Border Color:</td>
          <td>
            <input type="text" name="wpqm_search_border_color" size="10" id="wpqm_search_border_color" class="color" value="<?php echo esc_attr(get_option('wpqm_search_border_color'))?>" /> 
          </td>
        </tr>
        <tr>
          <td>Search button Text Color:</td>
          <td>
            <input type="text" name="wpqm_search_text_color" size="10" id="wpqm_search_text_color" class="color" value="<?php echo esc_attr(get_option('wpqm_search_text_color'))?>" /> 
          </td>
        </tr>
        
        <tr>
          <td>Menu Hover Color:</td>
          <td>
            <input type="text" name="wpqm_menu_hover_color" size="10" id="wpqm_menu_hover_color" class="color" value="<?php echo esc_attr(get_option('wpqm_menu_hover_color'))?>" /> 
          </td>
        </tr>
        <tr>
          <td>Menu Text Color:</td>
          <td>
            <input type="text" name="wpqm_menu_text_color" size="10" id="wpqm_menu_text_color" class="color" value="<?php echo esc_attr(get_option('wpqm_menu_text_color'))?>" /> 
          </td>
        </tr>
        
    </table>
    </div> 
   </div>
  </div>
  <table width="100%" cellspacing="2" cellpadding="5" class="editform">
        <tr valign="top">
            <td colspan="2" scope="row">			
              <input type="button" name="save" onclick="document.getElementById('wpqm_status_submit').value='1'; document.getElementById('wpqm_options').submit();" value="Save" class="button-primary" />
              <input type="button" name="reset" onclick="document.getElementById('wpqm_status_submit').value='2'; document.getElementById('wpqm_options').submit();" value="Reset to default" class="button-primary" />
            </td> 
        </tr>
  </table>
  </form>   
<?php
}
?>