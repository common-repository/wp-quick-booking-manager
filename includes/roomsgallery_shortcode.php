<?php
if (!defined('ABSPATH')) exit;
function sc_roomsgallery_shortcode($atts){
	global $table_prefix,$wpdb;
  if(!get_option('scpd_base_currency')){
    update_option('scpd_base_currency', 136);      
  }
	$sql_rooms = "select * from ".$table_prefix."term_taxonomy tt inner join ".$table_prefix."term_relationships tr on tt.term_taxonomy_id = tr.term_taxonomy_id inner join ".						$table_prefix."posts p on p.id = tr.object_id inner join ".$table_prefix."postmeta pm on pm.post_id = p.id where p.post_status = 'publish' and p.post_type='sc_custom_booking' and pm.meta_key = '_room_image' order by pm.post_id ASC";
	$rooms = $wpdb->get_results($sql_rooms);	
	$output = '<div id="rooms_gallery">';
	include_once(SCBOOKING_DIR."operations/get_cssfixfront.php");
	foreach($rooms as $room){
		global $table_prefix,$wpdb;
		$postid = $room->post_id;
		$sqlprice = "select * from ".$table_prefix."postmeta where post_id=".$postid." and meta_key='_room_price'";
		$room_price = $wpdb->get_results($sqlprice);
    //$room_country_currency = get_post_meta($postid, '_room_currency', true);
    $room_country_currency=scpd_currency_code(get_option('scpd_base_currency'));
		$image = $room->meta_value;
		$img_url = wp_get_attachment_image_src( get_post_thumbnail_id($postid),  'large' );    
		if($img_url == "" || $img_url == NULL){
			$img_url = SCBOOKING_PLUGIN_URL."/images/no-image.png";
		}else{
			$img_url =$img_url[0];
		}
		$output .= '<div style="float:left;padding:5px;"><a href="'.get_option('siteurl').'/?page_id='.ROOMDETAILS_PAGEID.'&roomid='.$postid.'"><img style="height:180px!important;width:250px!important;border:solid 1px #B8B8B8;" src="'.$img_url.'" /></a>
		<div><a href="'.get_option('siteurl').'/?page_id='.ROOMDETAILS_PAGEID.'&roomid='.$postid.'">'.sprintf(__("%s","sc-scbooking"),$room->post_title).'</a></div><div>'.$room_country_currency.''.sprintf(__("%s","sc-scbooking"),$room_price[0]->meta_value).''.__("/Day","sc-scbooking").'</div>
		</div>';
	}
	$output .= '</div>';
	return $output;
}
add_shortcode('sc_roomsgallery','sc_roomsgallery_shortcode');

