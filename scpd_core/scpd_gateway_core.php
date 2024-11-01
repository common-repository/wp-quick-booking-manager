<?php
if (!defined('ABSPATH')) exit;
function scpd_gateway_list(){
  $gateway_path = WP_CUSTOM_PRODUCT_PATH .'scpd_gateway';
  $dir = opendir( $gateway_path );
  $i = 0;
  while ( ($file = readdir( $dir )) !== false ) {
    //if($file!='.' && $file!='..' && !stristr( $file, "~" ) && !is_dir($file)){
    if (!is_dir($gateway_path.'/'.$file) ){
      $dirlist[$i] = $file;
      $i++;
    }
  }
  
  return $dirlist;
}

function scpd_admin_gateway_form(){
  $gateway_list = scpd_gateway_list();  
  if($_POST){
    update_option('scpd_active_gateway', sanitize_text_field($_POST['scpd_gateway_active']));
    update_option('scpd_gateway_title', sanitize_text_field($_POST['scpd_gateway_title']));
  }
  $acitive_gateway = get_option('scpd_active_gateway');
  $gateway_title = get_option('scpd_gateway_title');
  $output = '';
  foreach($gateway_list as $dr){
    $gateway_name = 'Scpd_Gateway_'. basename($dr,'.php');
    require_once WP_CUSTOM_PRODUCT_PATH.'scpd_gateway/'.$dr;
    $gateway = new $gateway_name();
    if($gateway->gateway_unique){
      do_action('scpd_gateway_admin_option_save_'.$gateway->gateway_unique);
      
      if($acitive_gateway){
        if(isset($acitive_gateway[$gateway->gateway_unique])){
          $ck_gw='checked="checked"';
        }else{
          $ck_gw='';
        }        
      }
      
      $output .='<div class="postbox closed" id="postexcerpt" style="margin-bottom:2px;">
                  <div title="'.__("Click to toggle","sc-scbooking").'" class="handlediv"><br></div>
                  <h3 class="hndle"><span>'.$gateway->gateway_name.'</span></h3>
                  <div class="inside">
                    <table class="form-table">
                      <tr>
                        <th scope="row">'.__("Enable","sc-scbooking").'</th>
                        <td>
                          <input type="checkbox" name="scpd_gateway_active['.$gateway->gateway_unique.']" '.$ck_gw.' /> 
                          '.__("Enable","sc-scbooking").' '.$gateway->gateway_name.'
                        </td>
                      </tr>
                      <tr>
                        <th scope="row">'.__("Title","sc-scbooking").'</th>
                        <td>
                          <input type="text" name="scpd_gateway_title['.$gateway->gateway_unique.']" value="'.$gateway_title[$gateway->gateway_unique].'" />
                        </td>
                      </tr>
                      '.$gateway->gateway_admin_form().'
                    </table>
                  </div>
                </div>';
    }
  }
  return $output;
}

function scpd_gateway_frontend_form(){
  $active_gateway = get_option('scpd_active_gateway');
  $gateway_title = get_option('scpd_gateway_title');
  
  if($active_gateway){
    foreach($active_gateway as $key=>$val){  
      require_once WP_CUSTOM_PRODUCT_PATH.'scpd_gateway/'.$key.'.php';
      $gateway_name = 'Scpd_Gateway_'.$key;      
      $gateway = new $gateway_name();
      echo '<div>';
      echo '<input type="radio" name="checkout_gateway" class="checkout_gateway checkout_required" value="'.$gateway->gateway_unique.'" /> ';
      if($gateway_title[$gateway->gateway_unique]){
        echo $gateway_title[$gateway->gateway_unique];
      }else{
        echo $gateway->gateway_name;
      }
	  if(isset($gateway->gateway_logo)){
		  if($gateway->gateway_logo){
			//echo ' <img src="'.$gateway->gateway_logo.'" />';
		  }
	  }
      echo '</div>';
      echo '<table class="scpd_gateway scpd_gateway_'.$gateway->gateway_unique.'">';
      echo $gateway->gateway_frontend_form();
      echo '</table>';
    }
  }else{
    echo ''.__("There is no active payment gateway available.","sc-scbooking").'';
  }
}