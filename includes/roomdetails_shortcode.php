<?php
if (!defined('ABSPATH')) exit;
function sc_roomdetails_shortcode($atts) {
  global $table_prefix, $wpdb;
  if (!get_option('scpd_base_currency')) {
    update_option('scpd_base_currency', 136);
  }

  $room_post_id = 0;
  if (isset($_REQUEST['roomid'])) {
    $room_post_id = esc_attr($_REQUEST['roomid']);
  }
  if ($room_post_id == 0) {
    return '';
  }
  if (FALSE === get_post_status($room_post_id)) {
    return 'Invalid Room ID.Please check the Room';
  }
  include_once('add_booking.php');
  $sql = "";
  if ($room_post_id != 0) {
    $sql = "select * from " . $table_prefix . "sc_scbooking where room_id like '%" . $room_post_id . "%' and order_status != '0'";
  } else {
    $sql = "select * from " . $table_prefix . "sc_scbooking where order_status != '0'";
  }
  $scbookings = $wpdb->get_results($sql);
  $sql_rooms = "select * from " . $table_prefix . "term_taxonomy tt inner join " . $table_prefix . "term_relationships tr on tt.term_taxonomy_id = tr.term_taxonomy_id inner join " . $table_prefix . "posts p on p.id = tr.object_id where p.post_status = 'publish' and p.post_type='sc_custom_booking' and p.id = " . $room_post_id . " ";
  $rooms = $wpdb->get_results($sql_rooms);

  $output = '<div>';
  $sqlroomatt = "select * from " . $table_prefix . "postmeta where post_id=" . $room_post_id;
  $room_att = $wpdb->get_results($sqlroomatt);
  $image = '';
  $desc = '';
  $noofbed = '';
  $bathroom = '';
  $price = '';
  $capacity = '';

  foreach ($room_att as $ratt) {
    if ($ratt->meta_key == '_room_image') {
      $image = $ratt->meta_value;
    }
    if ($ratt->meta_key == '_room_description') {
      $desc = sanitize_title($ratt->meta_value);
    }
    if ($ratt->meta_key == '_room_noofbed') {
      $noofbed = $ratt->meta_value;
    }
    if ($ratt->meta_key == '_room_bathroom') {
      $bathroom = $ratt->meta_value;
    }
    if ($ratt->meta_key == '_room_price') {
      $price = $ratt->meta_value;
    }
    if ($ratt->meta_key == '_room_capacity') {
      $capacity = $ratt->meta_value;
    }
  }
  $img_url = wp_get_attachment_image_src(get_post_thumbnail_id($room_post_id), 'large');
  if ($img_url == "" || $img_url == NULL) {
    $img_url = SCBOOKING_PLUGIN_URL . "/images/no-image.png";
  } else {
    $img_url = $img_url[0];
  }
  include_once(SCBOOKING_DIR . 'operations/get_cssfixfront.php');
  $output .= '
		<style type="text/css">
			.btnclndr {
				background: #3cb0fd;
				background-image: -webkit-linear-gradient(top, #3cb0fd, #3498db);
				background-image: -moz-linear-gradient(top, #3cb0fd, #3498db);
				background-image: -ms-linear-gradient(top, #3cb0fd, #3498db);
				background-image: -o-linear-gradient(top, #3cb0fd, #3498db);
				background-image: linear-gradient(to bottom, #3cb0fd, #3498db);
				-webkit-border-radius: 14;
				-moz-border-radius: 14;
				border-radius: 3px;
				font-family: Georgia;
				color: #ffffff;
				font-size: 15px;
				padding: 8px 15px 8px 15px;
				text-decoration: none;
			}
			
			.btnclndr:hover {
				background: #ffffff;
				background-image: -webkit-linear-gradient(top, #3498db, #2980b9);
				background-image: -moz-linear-gradient(top, #3498db, #2980b9);
				background-image: -ms-linear-gradient(top, #3498db, #2980b9);
				background-image: -o-linear-gradient(top, #3498db, #2980b9);
				background-image: linear-gradient(to bottom, #3498db, #2980b9);
				text-decoration: none;
			}
      #room_details{
        /*margin: 0 15px 0 15px;*/
      }
		</style>
		<div id="room_details">';
  $post_title = "";
  $postid = 0;
  if (isset($rooms[0])) {
    $post_title = sanitize_title($rooms[0]->post_title);
    $postid = $rooms[0]->ID;
  }  
  $room_country_currency=scpd_currency_code(get_option('scpd_base_currency'));
  if ($bathroom != '' || $bathroom != NULL) {
    $output .='<div><h4>' . sprintf(__("%s", "sc-scbooking"), $post_title) . '</h4></div>
          <div style="float:left;width:39%">
            <img style="border:solid 1px #B8B8B8;" src=' . $img_url . ' width="300px" height="170px"> 
          </div>
          <div style="float:left;width:57%;padding-left:15px;">            
            <div>' . __("Total hall:", "sc-scbooking") . ' ' . sprintf(__("%s", "sc-scbooking"), $noofbed) . '</div>
            <div>' . __("Bathroom:", "sc-scbooking") . ' ' . sprintf(__("%s", "sc-scbooking"), $bathroom) . '</div>
            <div>' . __("Price:", "sc-scbooking") . ' '.$room_country_currency.''.sprintf(__("%s", "sc-scbooking"), $price).'/ Day</div>
            <div>' . __("Room Capacity:", "sc-scbooking") . ' ' . sprintf(__("%s", "sc-scbooking"), $capacity) . ' Person</div>
            <div>' . sprintf(__("%s", "sc-scbooking"), $desc) . '</div>
          </div>
        </div>
        ';
  }
  //calendar---------------------						'.include_once('add_booking.php').'
  $output .= '
			<div style="height:auto;clear:both;padding-top:15px;">
					<div id="icon-options-general" class="icon32">
					</div>
					<div>
						<div style="float:left;width:40%"><h3 style="padding-top:10px;">' . __("Booking Calendar", "sc-scbooking") . '</h3></div>
						<div style="float:left;width:59%;text-align:right;"><a class="btnclndr" href="' . get_option('siteurl') . '/?page_id=' . SCBOOKINGCALENDAR_PAGEID . '" >' . __("Booking Calendar", "sc-scbooking") . '</a></div>
					</div>	
					<div style="height:15px;clear:both;"></div>
		  		<div id="calendar"></div>
          </div>';
  //right: 'month, agendaWeek, agendaDay'
  $output .="
	<script type='text/javascript'>
		function sc_generate_calendar(){
			jQuery('#calendar').fullCalendar({
				header: {
					left: 'prev, next today, agenda',
					center: 'title',
					right: 'month'
				},
				theme:true,
				selectable: true,
				selectHelper: true,
				editable: true,
				dayClick: function(date, allDay, jsEvent, view) {
						 jQuery('#dtpfromdate').val(jQuery.datepicker.formatDate('yy-mm-dd',date));
						 jQuery('#dtptodate').val(jQuery.datepicker.formatDate('yy-mm-dd',date));
             var date2 = jQuery('#dtpfromdate').datepicker('getDate', '+1d');
             date2.setDate(date2.getDate()+1); 
             jQuery('#dtptodate').datepicker('setDate', date2);



						 //jQuery('#rooms_multiselect').multiselect('select',$room_post_id);
						 //jQuery('#rooms_multiselect').multipleSelect('setSelects', $room_post_id);
						 sc_get_roomprice();
						 jQuery('#addbooking_dialog').dialog('open');
				},
				events: [";
  foreach ($scbookings as $booking) {
    $output .="{id: " . $booking->booking_id . ",
						title: '" . __('Booked', 'sc-scbooking') . "',
						start: '" . $booking->from_date . "',
						end: '" . $booking->to_date . "',
						backgroundColor : '#ED5B45',
						editable: true
					},";
  }

  $output .="	],
				eventColor: '#F05133'
			});
		}
	";
  $output .= "jQuery(document).ready(function() {
		sc_generate_calendar();
		jQuery('#addbooking_dialog').dialog({
					autoOpen: false,
					height: 500,
					width: 400,
					modal: true,
					buttons: {
							'" . __('Add Booking', 'sc-scbooking') . "': function () {
									if(sc_save_booking()){
										jQuery(this).dialog('close');
									}
									else{
									}
							},
							" . __('Cancel', 'sc-scbooking') . ": function () {
									jQuery(this).dialog('close');
									sc_cleardata();
							}
					},
					close: function () {
						sc_cleardata();
					}
			});
	});</script>";
  $output .= '</div>';
  return $output;
}

add_shortcode('sc_scbooking_roomdetails', 'sc_roomdetails_shortcode');
