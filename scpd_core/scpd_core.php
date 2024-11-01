<?php
if (!defined('ABSPATH')) exit;
include('scpd_gateway_core.php');
//include('scpd_shipping_core.php');

function scpd_set_email_content_type(){
	return "text/html";
}

function scpd_ob_start_function(){
  ob_start();
}

function scpd_session(){
    if(!session_id()) {
        session_start();
    }
}

function get_custom_table_prefix(){
	global $plugin_custom_table_prefix;
	$plugin_custom_table_prefix = "sc_";
	return $plugin_custom_table_prefix;
}

function scpd_session_end(){
    session_destroy();
}

function scpd_get_display_price($price)
{
  //echo 'price: '.$price;
	global $wpdb;
	$currency_symbol;
	$custom_table_prefix = get_custom_table_prefix();
	$currency_info = "Select * from ".$wpdb->prefix.$custom_table_prefix."currency_list where code = '".get_option( 'scpd_base_currency' )."' ORDER BY country ASC limit 1";
	$currency_table_info = $wpdb->get_results($currency_info,ARRAY_A);
  $currency_symbol = "";  
  $currency_symbol=scpd_currency_code(get_option('scpd_base_currency'));
	
	$str_price=number_format($price,2,'.',',');
	$str_price=$currency_symbol.''.$str_price;
  //echo 'modified price: '.$str_price.' ';
	return $str_price;
}

function scpd_currency_table_query (){
	global $wpdb;
	$custom_table_prefix = get_custom_table_prefix();
	$currency_rst = "Select * from ".$wpdb->prefix.$custom_table_prefix."currency_list ORDER BY country ASC";
	$currency_table_rst = $wpdb->get_results($currency_rst);
	$currency_data = $currency_table_rst;
	return $currency_data;
}

function scpd_set_session($session_name, $session_val){
  $_SESSION[$session_name]=$session_val;
  return 1;
}
function scpd_get_session($session_name){
  if(isset($_SESSION[$session_name])){
  	return $_SESSION[$session_name];
  }
  
}

function scpd_get_country_name_by_country_code($country_code){
  global $wpdb;
  $custom_table_prefix = get_custom_table_prefix();
  $sql = "SELECT * FROM ".$wpdb->prefix.$custom_table_prefix."currency_list WHERE isocode = '".$country_code."'";
  $result = $wpdb->get_row($sql);
  return $result->country;
}


add_action( 'admin_enqueue_scripts', 'add_admin_additional_script' );
add_action( 'wp_enqueue_scripts', 'add_frontend_additional_script' );
add_action('init', 'scpd_ob_start_function');
//add_action('init', 'scpd_create_upload_dir');

add_action('init', 'scpd_session', 1);
add_action('wp_logout', 'scpd_session_end');
add_action('wp_login', 'scpd_session_end');


add_filter( 'wp_mail_content_type','scpd_set_email_content_type' );