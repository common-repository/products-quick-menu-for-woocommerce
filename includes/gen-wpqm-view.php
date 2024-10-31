<?php if(!defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
add_shortcode('gen_wpqm_product_menu', 'gen_wpqm_product');

function gen_wpqm_getvarprice(){
    global $woocommerce;
	if(check_ajax_referer('usts_gen_wpqm_getvarprice')){
		 //ini_set('display_errors','Off');
		 $var_id=sanitize_text_field($_POST['wpqm_var_id']);
		 $product = get_product($var_id);                
		 $product_price=woocommerce_price($product->get_price());
		 echo $product_price;
		 exit();  
	}	 
}

function gen_wpqm_addtocart() { 
  global $woocommerce;
  if(check_ajax_referer('usts_gen_wpqm_addtocart')){
	  ini_set('display_errors','Off'); 
	  //$vid=$_POST['wpqm_prod_var_id'];
	  $pid=sanitize_text_field($_POST['wpqm_prod_id']);
	  $vid=sanitize_text_field($_POST['wpqm_prod_var_id']);
	  $pqty=sanitize_text_field($_POST['wpqm_prod_qty']);
	 
	  if($vid==0){    
		$product = get_product($pid);
		$bool=$product->is_sold_individually();
		if($bool==1){
		  $chk_cart=gen_wpqm_check_cart_item_by_id($pid);
		  if($chk_cart==0){
			echo 'Already added to cart';
			exit;
		  }
		}
	  }else{
		$product = get_product($vid);
		$bool=$product->is_sold_individually();
		if($bool==1){      
		  $chk_cart=gen_wpqm_check_cart_item_by_id($vid);
		  if($chk_cart==0){
			echo 'Already added to cart';
			exit;
		  }
		}
	  }
	
	  $stock=$product->get_stock_quantity();
	  $availability = $product->get_availability();
	  
	  if($availability['class']=='out-of-stock'){
		echo 'Out of stock';
		exit;
	  }
		   
	  if($stock!=''){
			foreach($woocommerce->cart->cart_contents as $cart_item_key => $values ) {
			$c_item_id='';
			$c_stock='';
			if($values['variation_id']!=''){
			  $c_item_id=$values['variation_id'];
			}else{
			  $c_item_id=$values['product_id'];
			}
			$c_stock=$values['quantity']+$pqty;
			
			if($vid==0 && $pid==$c_item_id && $c_stock>$stock){
			  $product = get_product($pid);		  
			  echo 'You have cross the stock limit';
			  exit;
			}else if($vid==$c_item_id && $c_stock>$stock){
			  $product = get_product($vid);
			  echo 'You have cross the stock limit';
			  exit;
			}        
		   }    
	  }
	
	  if($vid==0){
		$z=$woocommerce->cart->add_to_cart($pid,$pqty,null, null, null );
	  }else{    
		$z=$woocommerce->cart->add_to_cart($pid, $pqty, $vid, $product->get_variation_attributes(),null);
	  }  
	  echo '1';  
	  exit;
   }  
}

function gen_wpqm_check_cart_item_by_id($id) { 
	global $woocommerce;
	
	foreach($woocommerce->cart->get_cart() as $cart_item_key => $values ) {
		$_product = $values['data'];
		if($id == $_product->id) {
			return 0;
		}
	}	
	return 1;
}

function gen_wpqm_cart_amount(){
  global $woocommerce;
  if(check_ajax_referer('usts_gen_wpqm_addtocart')){
	  echo $woocommerce->cart->get_cart_total();  
	  exit;
  }
}
function gen_wpqm_product($val) {
  global $woocommerce;
  if (!class_exists('Woocommerce')) {
    echo '<div id="message" class="error"><p>Please Activate Wp WooCommerce Plugin</p></div>';
    $var = ob_get_contents();
    ob_end_clean();
    return $var;
  }
  ob_start();
  echo gen_wpqm_product2($val);
  //die('++');
  $output_string = ob_get_contents();
  ob_end_clean();
  return $output_string;  
}

function gen_wpqm_product2($val) {  
  global $woocommerce;
  if(get_option('wpqm_image_size')){
    $wpqm_img_size=get_option('wpqm_image_size');
  }else{
    $wpqm_img_size=40;
  }
  
  $wpqm_menu_bg_color='FF0000';
  $wpqm_menu_hover_color='222222';
  $wpqm_menu_text_color='FFFFFF';  
  $wpqm_submenu_bg_color='FFFFFF';
  $wpqm_prod_name_color='000000';
  $wpqm_prod_name_hover_color='FFFFFF';
  $wpqm_prod_des_color='000000';
  
  $wpqm_search_bg_color='ffffff';
  $wpqm_search_border_color='FF0000';
  $wpqm_search_text_color='FF0000';
 
  
  if(get_option('wpqm_search_bg_color')){$wpqm_search_bg_color=get_option('wpqm_search_bg_color');}
  if(get_option('wpqm_search_border_color')){$wpqm_search_border_color=get_option('wpqm_search_border_color');}
  if(get_option('wpqm_search_text_color')){$wpqm_search_text_color=get_option('wpqm_search_text_color');}
  
  //if(get_option('wpqm_menu_bg_color')){$wpqm_menu_bg_color=get_option('wpqm_menu_bg_color');}
  if(get_option('wpqm_menu_hover_color')){$wpqm_menu_hover_color=get_option('wpqm_menu_hover_color');}
  if(get_option('wpqm_menu_text_color')){$wpqm_menu_text_color=get_option('wpqm_menu_text_color');}  
   
  //if(get_option('wpqm_submenu_bg_color')){$wpqm_submenu_bg_color=get_option('wpqm_submenu_bg_color');}
  if(get_option('wpqm_prod_name_color')){$wpqm_prod_name_color=get_option('wpqm_prod_name_color');}
  if(get_option('wpqm_prod_name_hover_color')){$wpqm_prod_name_hover_color=get_option('wpqm_prod_name_hover_color');}
  if(get_option('wpqm_prod_des_color')){$wpqm_prod_des_color=get_option('wpqm_prod_des_color');}
  ?>

  <style>
    .wpqm_search{
      <?php 
      echo 'background:#'.$wpqm_search_bg_color.'!important;';
      echo 'border:2px solid #'.$wpqm_search_border_color.'!important;';
      echo 'color:#'.$wpqm_search_text_color.'!important;';
      ?>
    }
    .glossymenu a.menuitem{
      font: bold "Lucida Grande", "Trebuchet MS", Verdana, Helvetica, sans-serif;
      font-size: 18px;
      <?php echo 'background:#'.$wpqm_menu_bg_color.';';?>
    }
    .glossymenu div.submenu{ /*DIV that contains each sub menu*/
      <?php echo 'background:#'.$wpqm_submenu_bg_color.';';?>
     }
    .glossymenu a.menuitem:hover{
      <?php echo 'background:#'.$wpqm_menu_hover_color.';';?>
    }
    .glossymenu a.menuitem{
      <?php echo 'color:#'.$wpqm_menu_text_color.'!important;';?>
    }  
    .glossymenu div.submenu ul li a{
      <?php echo 'color:#'.$wpqm_menu_text_color.'!important;';?>
    }
    .wpqm_name{
      color: black;
      font-size: 13px;
      font-weight: bold;
    }
    .wpqm_name a{
      <?php echo 'color:#'.$wpqm_prod_name_color.';';?>
     }
     .wpqm_name a:hover{
      <?php echo 'color:#'.$wpqm_prod_name_hover_color.';';?>
     }
    .wpqm_des{
      <?php echo 'color:#'.$wpqm_prod_des_color.';';?>
      font-size: 11px;
      line-height: 15px;
    }
    .wpqm_des a{
      <?php echo 'color:#'.$wpqm_prod_name_color.';';?>
    }
    .alert-info {
        <?php echo 'background-color:#'.$wpqm_menu_text_color.';';
         echo 'border-color:#'.$wpqm_menu_bg_color.';';
         echo 'color:#'.$wpqm_menu_bg_color.';';?>
    }
  </style>

<form method="post" id="wpqm_options">
  <?php
    //echo gen_wpqm_product_dropdown_categories( array(12), 1, 0, '' );
    echo wc_product_dropdown_categories( array(), 1, 0, '' );
    //die('okzzz');
  ?>  

  <input type="hidden" value="1" name="wpqm_hval" />
  <input type="submit" class="wpqm_search" name="wpqm_btn_search" value="Search"/>
</form> <br /> 
  <?php
  $cart_url = $woocommerce->cart->get_cart_url();  
  ?>
<div class="span4 alertAdd" style="opacity: 1; display: block;">
  <div class="alert alert-info"id="wpqm_alert_info" style="display: none;"> Added to your cart </div>
</div>
<script> 
  
  //-------------------------------------
  var img_url_plus = '<?php echo plugins_url(); ?>/gen-woo-product-quick-menu/images/plus.png';
  var img_url_minus = '<?php echo plugins_url(); ?>/gen-woo-product-quick-menu/images/minus.png';
  
  ddaccordion.init({
    headerclass: "submenuheader", //Shared CSS class name of headers group
    contentclass: "submenu", //Shared CSS class name of contents group
    revealtype: "click", //Reveal content when user clicks or onmouseover the header? Valid value: "click", "clickgo", or "mouseover"
    mouseoverdelay: 500, //if revealtype="mouseover", set delay in milliseconds before header expands onMouseover
    collapseprev: false, //Collapse previous content (so only one open at any time)? true/false 
    defaultexpanded: [], //index of content(s) open by default [index1, index2, etc] [] denotes no content
    onemustopen: false, //Specify whether at least one header should be open always (so never all headers closed)
    animatedefault: true, //Should contents open by default be animated into view?
    persiststate: false, //persist state of opened contents within browser session?
    toggleclass: ["", ""], //Two CSS classes to be applied to the header when it's collapsed and expanded, respectively ["class1", "class2"]
    togglehtml: ["suffix", "<img src='"+img_url_plus+"' class='statusicon' />", "<img src='"+img_url_minus+"' class='statusicon' />"], //Additional HTML added to the header when it's collapsed and expanded, respectively  ["position", "html1", "html2"] (see docs)
    animatespeed: "slow", //speed of animation: integer in milliseconds (ie: 200), or keywords "fast", "normal", or "slow"
    oninit:function(headers, expandedindices){ //custom code to run when headers have initalized
      //do nothing
    },
    onopenclose:function(header, index, state, isuseractivated){ //custom code to run whenever a header is opened or closed
      //do nothing
    }
  })

  
  //------------------------------------
  
  
  
  //jQuery('#dropdown_product_cat option[value=]').text('All products');
  function gen_wpqm_ddl(var_id, id){
    var_id=var_id.value;
    jQuery('#wpqm_var_id_'+id).val(var_id);
    var ajax_url = '<?php echo wp_nonce_url(esc_url(admin_url( 'admin-ajax.php' )),'usts_gen_wpqm_getvarprice'); ?>';
    jQuery.ajax({
              type: "POST",
              url:ajax_url,
              data : {'action': 'gen_wpqm_getvarprice',
                'wpqm_var_id':     var_id
              },
              success: function(data){
                jQuery('#wpqm_price_'+id).html(data);
              }
            });
  }
  
  function gen_wpqm_add_prod(pid,vid){
    jQuery("#wpqm_loader"+pid).show();
   
    var vid= jQuery('#wpqm_var_id_'+pid).val();
    var qty= jQuery('#product_qty_'+pid).val(); 
    
    if(qty==0 || qty==''){
      jQuery('#wpqm_alert_info').text('Quantity can not be less than 1');
      jQuery('#wpqm_alert_info').show()
      setTimeout(function(){jQuery('#wpqm_alert_info').hide()}, 1500);      
      return false;
    }
    if(qty>1000){
      jQuery('#wpqm_alert_info').text('You have cross the quantity limit');
      jQuery('#wpqm_alert_info').show()
      setTimeout(function(){jQuery('#wpqm_alert_info').hide()}, 1500);      
      return false;
    }
    if(vid==0){
      qty= jQuery('#product_qty_'+pid).val();
    }
  
    
    var ajax_url2 = '<?php echo wp_nonce_url(esc_url(admin_url( 'admin-ajax.php' )),'usts_gen_wpqm_addtocart'); ?>';
    var ajax_url = '<?php echo plugins_url(); ?>';
    ajax_url+='/gen-woo-product-quick-menu/includes/gen-wpqm-add-cart.php';    
        jQuery.ajax({
          type: "POST",
          url:ajax_url2,
          data : {
                  'action':          'gen_wpqm_addtocart',
                  'wpqm_prod_id':     pid,
                  'wpqm_prod_var_id': vid,
                  'wpqm_prod_qty':    qty
          },
          success: function(response){          
            if(response==1){
              jQuery('#wpqm_alert_info').text('Added to your cart');
            }else{
              jQuery('#wpqm_alert_info').text(response);
            }
            
            jQuery.ajax({
              type: "POST",
              url:ajax_url2,
              data : {'action': 'gen_wpqm_cart_amount'},
              success: function(data){                
                jQuery('#wpqm_cart_price').html(data);
              }
            });
            
             jQuery('#wpqm_alert_info').show()
             setTimeout(function(){jQuery('#wpqm_alert_info').hide()}, 2000);
             jQuery("#wpqm_loader"+pid).hide();
          }
        });
        
  }
  
  jQuery(document).ready(function(){
    jQuery(".ajax").colorbox();
  });
</script> 
<script type="text/javascript">
jQuery(document).ready(function(){	
	jQuery('#glossymenu').delegate('#ajax','click',function(e){
		e.preventDefault();
		//var prod_id = jQuery(this).parent().children('#hdn_submenu_cont_popup_id').val();
		var prod_id = jQuery(this).attr('id');
		console.log(prod_id);
		jQuery.ajax({
			  type: "POST",
              url: '<?php echo admin_url( 'admin-ajax.php' );?>',
			  data: {
                action: 'gen_ajax_wpqm_popup_data',
                pid : prod_id
              },
			  success: function (data) {
				var count = data.length;
				if(count>0){
					//alert('');
				}
			  },
			  error : function(s , i , error){
			   		console.log(error);
		      }
		});
	});
});	
</script>
 
<?php
  if(!isset($_POST['wpqm_hval'])){
    if($val){
      $id= $val['category_id'];
      $product_category =  get_term_by( 'id', $id, 'product_cat', 'ARRAY_A' );
      if(!empty($product_category )){
          $_POST['wpqm_hval']=1;
          $_POST['product_cat']=$product_category['slug'];
          $_POST['wpqm_front_order_by']='title';
          $_POST['wpqm_front_order']='ASC';
          ?>
            <script>
              jQuery(".dropdown_product_cat option[value='" + '<?php echo $_POST['product_cat']?>' + "']").attr('selected', 'selected');
            </script>
          <?php
      }
    }
  }
  echo '<div class="glossymenu">';
  if(isset($_POST['wpqm_hval']) && isset($_POST['product_cat']) && $_POST['product_cat']!=''){
    
         //$exc_cats_slug=  explode(',', get_option('wpqm_exc_cat'));
         $args = array(
          'post_type'				=> 'product',
          'post_status' 			=> 'publish',			
          'orderby' 				=> 'title',
          'order' 				      => 'asc',
          'type' => 'numeric',
          'posts_per_page' 		=> 200,
          'meta_query' 			=> array(
            array(
              'key' 			=> '_visibility',
              'value' 		=> array('catalog', 'visible'),
              'compare' 	=> 'IN'
            )
          ),
          'tax_query' 			=> array(
                array(
                'taxonomy' 		=> 'product_cat',
                'terms' 		=> array( ($_POST['product_cat']) ),
                'field' 		=> 'slug',
                'operator' 		=> 'IN'
              )
            )
        );
       
       $cat_data = get_term_by( 'slug', $_POST['product_cat'], 'product_cat', 'ARRAY_A' );
             
       echo '<a class="menuitem submenuheader">'.$cat_data['name'].'</a>';//menu cat
       
       $loop = new WP_Query( $args );
       gen_wpqm_show_prod2($loop, $wpqm_img_size);
  }else{
    $exc_cats_slug=array();
    if (get_option('wpqm_exc_cat')){
          $exc_cats_slug=  explode(',', get_option('wpqm_exc_cat'));
    }
    
    $term 			= get_queried_object();
    $parent_id 		= empty( $term->term_id ) ? 0 : $term->term_id;
    $args2 = array(
      'parent'       => $parent_id,
      'child_of'     => $parent_id,
      'menu_order'   => 'ASC',
      'hide_empty'   => 1,
      'hierarchical' => 1,
      'taxonomy'     => 'product_cat',
      'terms' 		=> 'bag',
      'field' 		=> 'slug'
    );
		
		$product_categories = get_categories( $args2  );
    $total = sizeof($product_categories);
    foreach ($product_categories as $cat_data){      
      if(!in_array($cat_data->slug,$exc_cats_slug)){
        echo '<a class="menuitem submenuheader" >'.$cat_data->name.'</a>';//menu cat
          $args = array(
            'post_type'				=> 'product',
            'post_status' 			=> 'publish',			
            'orderby' 				=> 'title',
            'order' 				      => 'asc',
            'type' => 'numeric',
            'posts_per_page' 		=> 200,
            'meta_query' 			=> array(
              array(
                'key' 			=> '_visibility',
                'value' 		=> array('catalog', 'visible'),
                'compare' 	=> 'IN'
              )
            ),
            'tax_query' 			=> array(
                  array(
                  'taxonomy' 		=> 'product_cat',
                  'terms' 		=> array( $cat_data->slug ),
                  'field' 		=> 'slug',
                  'operator' 		=> 'IN'
                )
              )
          );
        $loop = new WP_Query( $args );
        gen_wpqm_show_prod2($loop, $wpqm_img_size);
      }
    }
  }
  echo '</div>';//glossymenu end
}
function gen_wpqm_show_prod2($loop,$wpqm_img_size){

   global $woocommerce;   
      if ($loop->have_posts()){        
        echo '<div id="submenu_cont_popup" class="submenu"><ul><table class="wpqm_table">';
        foreach($loop->posts as $val){

          $product = get_product($val->ID );
                              
          $att_value='';
          if($product->is_type( 'variable')){
            $default_att=$product->get_variation_default_attributes();
            if(!empty($default_att)){
              foreach ($default_att as $att_val){
                $att_value= $att_val;
              }
            }
          }
          $is_cat=0;
          
          if($is_cat==0){
            $variation_display=false;
            $variation=false;
            if (get_option('wpqm_display_variation')=='1'){
              $variation_display= true;
            }            
            
            if ($variation_display == true){
                $variation_query = new WP_Query();
                $args_variation = array(
                  'post_status' => 'publish',
                  'post_type' => 'product_variation',
                  'posts_per_page'   => -1,  
                  'post_parent' => $val->ID
                );                
                $variation_query->query($args_variation);

                if ($variation_query->have_posts()){
                  $variation=true;
                }
            }
             ini_set('display_errors','Off');
             
            if($variation==true && $product->is_type( 'variable' )){
              //----------------------------------------------------
			  ?>
              <input type="hidden" id="hdn_submenu_cont_popup_id" name="hdn_submenu_cont_popup_id" value="<?php echo $val->ID?>" />
              <?php
              $product_name_org='<div class="wpqm_name">'.$val->post_title.'</div>';
              $prod_des='';
              if($val->post_content){
                $prod_des=$val->post_content;

                    $stringCut = substr($prod_des, 0, 200);
                    $prod_des = substr($stringCut, 0, strrpos($stringCut, ' ')).'...'; 

              }
              
              
              $ddl_att_val='';              
              $vq=$variation_query->posts;
              $prod_price='';
              $wpqm_var_id='';

              foreach($vq as $var_data){
                $product = get_product($var_data->ID);                
                $attributes= woocommerce_get_formatted_variation($product->get_variation_attributes(),true);
                $attributes=  explode(':', $attributes); 
                
                $att_value=strtolower($att_value);
                $att_curr=strtolower($attributes[1]);
                $att_curr = str_replace(' ', '', $att_curr);
                $select='';
                
                //-------price
                  $product_price=woocommerce_price($product->get_price());
                  if($att_value==''){
                    if(!$prod_price){                    
                      $prod_price=$product_price;
                      $wpqm_var_id=$var_data->ID;
                    }
                  
                  }else if($att_value==$att_curr){
                    //die('++++++');
                    $prod_price=$product_price;
                    $wpqm_var_id=$var_data->ID;
                    $select='selected="selected"';
                  }
                  
                //------dropdown product variation
                  $ddl_att_val.='<option '.$select.' value='.$var_data->ID.'>'.$attributes[1].'</option>';
                
                //-------image
                  $img_url = GEN_WPQM_BASE_URL. '/images/placeholder.png';
                  if (has_post_thumbnail($var_data->ID)){
                    $img_url2 = wp_get_attachment_url( get_post_thumbnail_id($var_data->ID) );                    
                    $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($var_data->ID), 'thumbnail' );
                    $img_url = $thumb['0'];
                    
                  } else if (has_post_thumbnail($val->ID)){
                    $img_url2 = wp_get_attachment_url( get_post_thumbnail_id($val->ID) );                    
                    $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($val->ID), 'thumbnail' );
                    $img_url = $thumb['0'];                   
                  }
                  //--------stock
                  $max_stock=1000;
              }//end foreach
              //prod_image
              $img_url = GEN_WPQM_BASE_URL. '/images/placeholder.png';
              if (has_post_thumbnail($val->ID)){
                    $img_url2 = wp_get_attachment_url( get_post_thumbnail_id($val->ID) );                    
                    $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($val->ID), 'thumbnail' );
                    $img_url = $thumb['0'];                   
              }
              $prod_option= '<select onchange="gen_wpqm_ddl(this,'.$val->ID.');" style="max-width:100px;">'.$ddl_att_val.'</select>';
              echo '<tr>';
              if (get_option('wpqm_display_image_preview')=='1'){
                  echo '<td><a href="'.$img_url.'" class="preview"><img src="'.$img_url.'" height="'.$wpqm_img_size.'" width="'.$wpqm_img_size.'" /></a></td>';
                  }else{
                    echo '<td><img src="'.$img_url.'" height="'.$wpqm_img_size.'" width="'.$wpqm_img_size.'" /></td>';
              }
              if (get_option('wpqm_display_image_preview')=='1'){
                echo '<td class="wpqm_td">'.$product_name_org.'<div class="wpqm_des">'.$prod_des.'</div></td><td>'.$prod_option.'</td>';
              }else{
                echo '<td class="wpqm_td">'.$product_name_org.'<div class="wpqm_des">'.$prod_des.'</div></td><td>'.$prod_option.'</td>';
              }  
                  
              ?>
              
                    <td>
                      <input type="hidden" name="wpqm_var_id" id="wpqm_var_id_<?php echo $val->ID?>" value="<?php echo $wpqm_var_id;?>" />
                      
                        <?php
                        if($max_stock!=0){                            
                          ?><input type="number" style="width:70px;" value="1" min="1"  max="<?php echo esc_attr($max_stock);?>" name="product_qty_<?php echo $val->ID?>" id="product_qty_<?php echo $val->ID?>" /><?php                            
                        }else{                            
                           ?><input type="number" style="width:70px;" value="0" min="0" max="0" name="product_qty_<?php echo $val->ID?>" id="product_qty_<?php echo $val->ID ?>" /><?php
                        }
                        ?>  
                     
                    </td>  
                  <?php                  
                  if($product->regular_price && $max_stock!=0){  
                  echo '<td>
                    <div id="wpqm_price_'.$val->ID.'">
                        '.$prod_price.'
                      </div>  
                    <div class="wpqm_add_btn"><a onclick="gen_wpqm_add_prod('.$val->ID.',1);"><div class="wpqm_add_cart"></div></a></div>
                      </td>';
                  }else {
                    echo '<td></td>';
                  }
                  //echo '<td width="30"><div class="wpqm_loading" id="wpqm_loader'.$val->ID.'" style="display: none;"></div></td></tr>';
                  echo '</tr>';
            }else{
                gen_wpqm_show_prod($val->ID,$wpqm_img_size, $val->post_title);
            }
          }//is cat check end  
        }//end foreach
          echo '</table></ul></div>';
      }//if
}

function gen_wpqm_show_prod($id, $wpqm_img_size, $post_title){
    $max_stock=500;
    ini_set('display_errors','Off');
    $product=wc_get_product( $id );
        
    if($product->get_stock_quantity()!=''){
      $max_stock=$product->get_stock_quantity();
    }
    $availability=$product->get_availability();

    if($availability['class']=='out-of-stock'){
      $max_stock=0;
    }

    $product_name='<div class="wpqm_name">'.$post_title.'
      </div>';
    $product = get_product($id);
    $prod_des='';
    if($product->post->post_content){
      $prod_des=$product->post->post_content;      
      $stringCut = substr($prod_des, 0, 200);
      $prod_des = substr($stringCut, 0, strrpos($stringCut, ' ')).'... ';
    }

    $product_price =$product->get_price_html();
    
    if (has_post_thumbnail($id)){
        $img_url2 = wp_get_attachment_url( get_post_thumbnail_id($id,'thumbnail'));
        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($id), 'thumbnail' );
        $img_url = $thumb['0'];

    } else {
        $img_url=GEN_WPQM_BASE_URL. '/images/placeholder.png';
        $img_url2=$img_url;
    }
    if (has_post_thumbnail($id)){
        $img_url2 = wp_get_attachment_url( get_post_thumbnail_id($id,'thumbnail'));
        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($id), 'thumbnail' );
        $img_url = $thumb['0'];

    } else {
        $img_url=GEN_WPQM_BASE_URL. '/images/placeholder.png';
        $img_url2=$img_url;
    }
    if (get_option('wpqm_display_image_preview')=='1'){
      echo '<tr><td><a href="'.$img_url2.'" class="preview"><img src="'.$img_url.'" height="'.$wpqm_img_size.'" width="'.$wpqm_img_size.'" /></a></td>
        <td class="wpqm_td">'.$product_name.'<div class="wpqm_des">'.$prod_des.'</div></td>
          <td></td>';
    }else{                
      echo '<tr><td><img src="'.$img_url.'" height="'.$wpqm_img_size.'" width="'.$wpqm_img_size.'" /></td>
        <td class="wpqm_td">'.$product_name.'<div class="wpqm_des">'.$prod_des.'</div></td>
        ';
    }
    ?>
      <td>
          <?php
          if($max_stock!=0){
          //if($product->regular_price && $max_stock!=0){
            ?><input type="number" style="width:70px;" value="1" min="0" max="0<?php echo esc_attr($max_stock);?>" name="product_qty_<?php echo $id;?>" id="product_qty_<?php echo $id;?>" /><?php
          }else{
            ?><input type="number" style="width:70px;" value="0" min="0" max="0" name="product_qty_<?php echo $id;?>" id="product_qty_<?php echo $id;?>" /><?php
          }
          ?>        
      </td>  
    <?php
    
    if($max_stock!=0){
    //if($product->regular_price && $max_stock!=0){
      echo '<td>
             <div>'.$product_price.'</div>
             <div class="wpqm_add_btn"><a onclick="gen_wpqm_add_prod('.$id.', 0);"><div class="wpqm_add_cart"></div></a></div>
          </td>';
    }else{
      echo '<td><div>'.$product_price.'</div></td>';
    }
    //echo '<td><div class="wpqm_loading" id="wpqm_loader'.$id.'" style="display: none;"></div></td></tr>';
    echo '</tr>';
    
}
add_action( 'wp_ajax_nopriv_gen_wpqm_addtocart','gen_wpqm_addtocart' );
add_action( 'wp_ajax_gen_wpqm_addtocart', 'gen_wpqm_addtocart' );

add_action( 'wp_ajax_nopriv_gen_wpqm_cart_amount','gen_wpqm_cart_amount' );
add_action( 'wp_ajax_gen_wpqm_cart_amount', 'gen_wpqm_cart_amount' );

add_action( 'wp_ajax_nopriv_gen_wpqm_getvarprice','gen_wpqm_getvarprice' );
add_action( 'wp_ajax_gen_wpqm_getvarprice', 'gen_wpqm_getvarprice' );
?>