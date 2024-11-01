<?php
/*
  Plugin Name: WP Quick Booking Manager
  Plugin URI: http://www.codeinterest.com/
  Description: Hotel/Resort Quick Booking Management System(Free Version)
  Version: 1.1
  Author: SolverCircle
  Author URI: http://www.solvercircle.com
 */
if (!defined('ABSPATH')) exit;
define('SCBOOKING_PLUGIN_URL', plugins_url('', __FILE__));
define("SC_BASE_URL", WP_PLUGIN_URL . '/' . plugin_basename(dirname(__FILE__)));
define('SCBOOKING_DIR', plugin_dir_path(__FILE__));

$scbooking_calendar_page = get_page_by_title('Booking Calendar');
$cart_page = get_page_by_title('Shopping Cart');
$checkout_page = get_page_by_title('Checkout');
$ordersuccess_page = get_page_by_title('Order Success');
$scrooms_page = get_page_by_title('Rooms');
$roomdetails_page = get_page_by_title('Room Details');

$scbooking_calendar_page_id = 0;
$cart_page_id = 0;
$checkout_page_id = 0;
$ordersuccess_page_id = 0;
$scrooms_page_id = 0;
$roomdetails_page_id = 0;
if ($scbooking_calendar_page) {
  $scbooking_calendar_page_id = $scbooking_calendar_page->ID;
}
if ($cart_page) {
  $cart_page_id = $cart_page->ID;
}
if ($checkout_page) {
  $checkout_page_id = $checkout_page->ID;
}
if ($ordersuccess_page) {
  $ordersuccess_page_id = $ordersuccess_page->ID;
}
if ($scrooms_page) {
  $scrooms_page_id = $scrooms_page->ID;
}
if ($roomdetails_page) {
  $roomdetails_page_id = $roomdetails_page->ID;
}
define('SCBOOKINGCALENDAR_PAGEID', $scbooking_calendar_page_id);
define('SHOPPINGCART_PAGEID', $cart_page_id);
define('CHECKOUT_PAGEID', $checkout_page_id);
define('ORDERSUCCESS_PAGEID', $ordersuccess_page_id);
define('SCROOM_PAGEID', $scrooms_page_id);
define('ROOMDETAILS_PAGEID', $roomdetails_page_id);

include_once('includes/calendar_shortcode.php');
include_once('includes/managebooking_shortcode.php');
//include_once('front-login/frontLoginForm.php');
include_once('includes/roomsgallery_shortcode.php');
include_once('includes/roomdetails_shortcode.php');

include_once('includes/create_page.php');
include_once('includes/common_function.php');
include_once('operations/scbooking_init.php');
//include_once('includes/user_add_booking_shortcode.php');
//=====Payment System include======================================
include_once('scpd_core/scpd_core.php');
include_once('scpd_admin/settings/scpd_settings.php');

//=================================================
function sc_create_custom_post_type() {
  register_post_type('sc_custom_booking', array(
      'labels' => array(
          'name' => __('Rooms', 'sc-scbooking'),
          'singular_name' => __('Room', 'sc-scbooking'),
          'menu_name' => __('Quick Booking Manager', 'sc-scbooking'),
          'all_items' => __('Rooms', 'sc-scbooking'),
          'add_new_item' => __('Add New Room', 'sc-scbooking'),
          'add_new' => __('Add New Room', 'sc-scbooking'),
          'not_found' => __('No rooms found.', 'sc-scbooking'),
          'search_items' => __('Search Rooms', 'sc-scbooking'),
          'edit_item' => __('Edit Room', 'sc-scbooking'),
          'view_item' => __('View Room', 'sc-scbooking'),
          'not_found_in_trash' => __('No Rooms found in trash', 'sc-scbooking')
      ),
      'public' => true,
      'has_archive' => true,
      'rewrite' => array('slug' => 'custom_bookings'),
      'supports' => array('title', 'thumbnail')
          )
  );
}

add_action('init', 'sc_create_book_taxonomy');

function sc_create_book_taxonomy() {
  register_taxonomy(
          'sc_custom_category', 'sc_custom_booking', array(
      'label' => __('Category'),
      'rewrite' => array('slug' => 'sc_custom_category'),
      'hierarchical' => true,
          )
  );
}

function sc_add_metabox_for_room() {
  add_meta_box(
          'room_attribute_metabox', // ID, should be a string
          '' . __('Room Attribute Settings', 'sc-scbooking') . '', // Meta Box Title
          'sc_room_meta_box_content', // Your call back function, this is where your form field will go
          'sc_custom_booking', // The post type you want this to edit screen section (�post�, �page�, �dashboard�, �link�, �attachment� or �custom_post_type� where custom_post_type is the custom post type slug)
          'normal', // The placement of your meta box, can be �normal�, �advanced�or side
          'high' // The priority in which this will be displayed
  );
}

function sc_room_meta_box_content($post) {
  ?>
  <script type="text/javascript">
    jQuery(document).ready(function () {

        jQuery('#upload_roomimage_button').click(function () {
            formfield = jQuery('#roommetabox_image').attr('name');
            tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
            window.send_to_editor = function (html) {
                imgurl = jQuery('img', html).attr('src');
                jQuery('#roommetabox_image').val(imgurl);
                tb_remove();
            }
            return false;
        });

    });
  </script>
  <style type="text/css">
      #room_attribute_metabox{
          margin-top:-25px;
      }
  </style>
  <?php
  $room_noofbed = get_post_meta($post->ID, '_room_noofbed', true);
  $room_bathroom = get_post_meta($post->ID, '_room_bathroom', true);
  $room_price = get_post_meta($post->ID, '_room_price', true);
  $room_country_currency = get_post_meta($post->ID, '_room_currency', true);
  $room_capacity = get_post_meta($post->ID, '_room_capacity', true);
  $room_description = get_post_meta($post->ID, '_room_description', true);
  $room_image = get_post_meta($post->ID, '_room_image', true);

  $country_data = scpd_currency_table_query();
  ?>
  <table>
      <tbody>
          <tr>
              <th scope="row"><?php _e("No of Bed:", "sc-scbooking"); ?></th>
              <td><input type="text" name="roommetabox_noofbed" id="roommetabox_noofbed" value="<?php if (isset($room_noofbed)) printf(__("%d", "sc-scbooking"), $room_noofbed); ?>" style="width:300px;" /><span style="color:red;" class="asterik">*</span></td>
          </tr>
          <tr>
              <th scope="row"><?php _e("Bath Room:", "sc-scbooking"); ?></th>
              <td>
                  <select id="bathroom" name="bathroom">
                      <option value="ensuite" <?php if ($room_bathroom == 'ensuite') echo 'selected'; ?> ><?php _e("Ensuite", "sc-scbooking"); ?></option>
                      <option value="shared" <?php if ($room_bathroom == 'shared') echo 'selected'; ?> ><?php _e("Shared", "sc-scbooking"); ?></option>
                  </select>
              </td>
          </tr>
          <tr>
              <th scope="row"><?php _e("Price:", "sc-scbooking"); ?></th>
              <td><input type="text" name="roommetabox_price" id="roommetabox_price" value="<?php if (isset($room_price)) printf(__("%d", "sc-scbooking"), $room_price); ?>" style="width:300px;" /><span style="color:red;" class="asterik">*</span></td>
          </tr>
          <tr style="display:none;">
              <th scope="row"><?php _e("Currency:", "sc-scbooking"); ?></th>
              <td>
                  <select name="roommetabox_currency">
                      <option value=""><?php _e("Select", "sc-scbooking"); ?></option>
                      <?php
                      foreach ($country_data as $currency) {
                        echo '<option value="' . $currency->code . '" ' . (($room_country_currency == $currency->code) ? 'selected="selected"' : '') . ' >' . $currency->currency . ' (' . $currency->code . ')</option>';
                      }
                      ?>
                  </select>
              </td>
          </tr>
          <tr>
              <th scope="row"><?php _e("Capacity:", "sc-scbooking"); ?></th>
              <td><input type="text" name="roommetabox_capacity" id="roommetabox_capacity" value="<?php if (isset($room_capacity)) printf(__("%d", "sc-scbooking"), $room_capacity); ?>" style="width:300px;" /><span style="color:red;" class="asterik">*</span></td>
          </tr>
          <tr>
              <th scope="row"><?php _e("Description:", "sc-scbooking"); ?></th>
              <td>
                  <textarea name="roommetabox_Description" id="roommetabox_Description" rows="5" cols="46"><?php if (isset($room_description)) printf(__("%s", "sc-scbooking"), $room_description); ?></textarea>
              </td>
          </tr>
          <tr style="display:none;">
              <th scope="row"><?php _e("Image:", "sc-scbooking"); ?></th>
              <td>
                  <input type="text" class="code"  name="roommetabox_image" id="roommetabox_image" value="<?php if (isset($room_image)) echo $room_image; ?>" style="width:300px;" />
                  <input style="min-width:60px" id="upload_roomimage_button" class="button" type="button" value="<?php _e("Upload Image", "sc-scbooking"); ?>" />
              </td>
          </tr>

      </tbody>
  </table>
  <?php
}

function sc_save_room_metabox($post_id) {
  $post = get_post($post_id);
  if ($_POST) :
    $room_noofbed = 0;
		if(isset($_POST['roommetabox_noofbed'])){	
			$room_noofbed = sanitize_text_field( $_POST['roommetabox_noofbed'] );
			$room_noofbed = intval($room_noofbed);
			if ( ! $room_noofbed ) {
			  $room_noofbed = 1;
			}
		}
		$room_bathroom = 1;
		if(isset($_POST['bathroom'])){
			$room_bathroom = sanitize_text_field( $_POST['bathroom'] );
			$room_bathroom = intval($room_bathroom);
			if (!$room_bathroom) {
			  $room_bathroom = 1;
			}
		}
		$room_price = 0;
		if(isset($_POST['roommetabox_price'])){
			$room_price = sanitize_text_field( $_POST['roommetabox_price'] );
		}
    	$room_currency = "";
		if(isset($_POST['roommetabox_currency'])){
			$room_currency = sanitize_text_field( $_POST['roommetabox_currency'] );
		}
		$room_capacity = 100;
		if(isset($_POST['roommetabox_capacity'])){
			$room_capacity = sanitize_text_field( $_POST['roommetabox_capacity'] );
			$room_capacity = intval($room_capacity);
			if (!$room_capacity) {
			  $room_capacity = 100;
			}
		}
		$room_description = "";
		if(isset($_POST['roommetabox_Description'])){
			$room_description = sanitize_title( $_POST['roommetabox_Description'] );
		}
		$room_image = "";
		if(isset($_POST['roommetabox_image'])){
			$room_image = sanitize_text_field( $_POST['roommetabox_image'] );
		}
    // Update post meta		
    update_post_meta($post->ID, '_room_noofbed', $room_noofbed);
    update_post_meta($post->ID, '_room_bathroom', $room_bathroom);
    update_post_meta($post->ID, '_room_price', $room_price);
    update_post_meta($post->ID, '_room_currency', $room_currency);
    update_post_meta($post->ID, '_room_capacity', $room_capacity);
    update_post_meta($post->ID, '_room_description', $room_description);
    update_post_meta($post->ID, '_room_image', $room_image);
  endif;
}

add_action('save_post', 'sc_save_room_metabox');
add_action('add_meta_boxes', 'sc_add_metabox_for_room');
/* --------------------- */

function sc_custom_manage_booking_menu() {
  add_submenu_page('edit.php?post_type=sc_custom_booking', 'Manage Booking', '' . __("Manage Booking", "sc-scbooking") . '', 'manage_options', 'manage-booking-menu', 'sc_manage_booking_settings');
}

function gen_pro_version() {
  add_submenu_page('edit.php?post_type=sc_custom_booking', 'Booking Pro Version', 'BOOKING PRO VERSION', 'manage_options', 'booking-pro-version', 'gen_booking_pro_version_setting');
}

//-------------Booking Settings-----------------------
function sc_scbooking_get_opt_val($opt_name, $default_val) {
  if (get_option($opt_name) != '') {
    return $value = get_option($opt_name);
  } else {
    return $value = $default_val;
  }
}

function sc_booking_settings_page() {
  include_once('operations/booking_settings.php');
}

function sc_manage_booking_settings() {
  include_once('includes/manage_booking.php');
}

function sc_add_payment_method_settings() {
  include('includes/add_paymentmethod.php');
}

function gen_booking_pro_version_setting() {
  include_once('includes/booking_pro_version.php');
}

add_action('admin_menu', 'sc_custom_manage_booking_menu');


add_action('admin_menu', 'gen_pro_version');

/* --------------------- */

//include_once('operations/scbooking_init.php');

function sc_booking_uninstall() {
  
}

register_activation_hook(__FILE__, 'sc_scbooking_install');
//register_activation_hook( __FILE__, 'dsf_install' );
register_deactivation_hook(__FILE__, 'sc_booking_uninstall');
add_action('init', 'sc_create_custom_post_type');

function sc_prevent_admin_access() {
  if (strpos(strtolower($_SERVER['REQUEST_URI']), '/wp-admin') && !current_user_can('administrator')) {
    wp_redirect(home_url());
  }
}

add_action('init', 'sc_bookingStartSession', 1);

function sc_bookingStartSession() {
  if (!session_id()) {
    session_start();
  }
}

//------ Shopping Cart Session ajax call ------
function sc_shoppingcartjs() {
  wp_enqueue_script('shoppingcartjs', SCBOOKING_PLUGIN_URL . '/shoppingcart/js/shoppingcart.js', true);
  wp_localize_script('shoppingcartjs', 'scCartAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
}

add_action('wp_footer', 'sc_shoppingcartjs');

function sc_cart_session_ajax_request() {
  if (isset($_REQUEST)) {
    $indx = esc_attr($_REQUEST['indx']);
    $shopping_cart_arr = $_SESSION['bookingcart'];
    unset($shopping_cart_arr[$indx]);
    sort($shopping_cart_arr);
    $_SESSION['bookingcart'] = $shopping_cart_arr;
    print_r($shopping_cart_arr); //json_encode($shopping_cart_arr);
  }
  exit;
}

add_action('wp_ajax_nopriv_sc_cart_session_ajax_request', 'sc_cart_session_ajax_request');
add_action('wp_ajax_sc_cart_session_ajax_request', 'sc_cart_session_ajax_request');

//=========Payment System-----------------------------------------------------------------------------------
function sc_get_roomprice_by_custompost() {
  if (isset($_REQUEST)) {
    global $table_prefix, $wpdb;
    $post_ids_arr = $_REQUEST['post_ids_arr'];
    $fromdate = esc_attr($_REQUEST['from_date']);
    $todate = esc_attr($_REQUEST['to_date']);
    $days = sc_howManyDays($fromdate, $todate);
    $price = 0;
    if (!empty($post_ids_arr)) {
      foreach ($post_ids_arr as $post_id) {
        $sql_room_price = "select * from " . $table_prefix . "postmeta where meta_key='_room_price' and post_id=" . $post_id;
        $result = $wpdb->get_results($sql_room_price);
        $price = $price + ($result[0]->meta_value * $days);
      }
    }
    echo $price;
  }
  exit;
}

function sc_howManyDays($startDate, $endDate) {

  $date1 = strtotime($startDate . " 0:00:00");
  $date2 = strtotime($endDate . " 23:59:59");
  $res = (int) (($date2 - $date1) / 86400);
  //return $res+1;
  return $res;
}

add_action('wp_ajax_nopriv_sc_get_roomprice_by_custompost', 'sc_get_roomprice_by_custompost');
add_action('wp_ajax_sc_get_roomprice_by_custompost', 'sc_get_roomprice_by_custompost');

function sc_get_bookings() {
  if (isset($_REQUEST)) {
    global $table_prefix, $wpdb;
    $booking_id = esc_sql($_REQUEST['booking_id']);
    $sql = "select * from " . $table_prefix . "sc_scbooking where booking_id=" . $booking_id;
    $result = $wpdb->get_results($sql);
    echo json_encode($result);
  }
  exit;
}

add_action('wp_ajax_nopriv_sc_get_bookings', 'sc_get_bookings');
add_action('wp_ajax_sc_get_bookings', 'sc_get_bookings');

function sc_load_managebooking_data_front() {
  if ($_POST['page']) {
    $page = $_POST['page'];
    $cur_page = $page;
    $page -= 1;
    $per_page = 10; //15;
    $previous_btn = true;
    $next_btn = true;
    $first_btn = true;
    $last_btn = true;
    $start = $page * $per_page;
    global $table_prefix, $wpdb;
    $sql = "select * from " . $table_prefix . "sc_scbooking where order_status != '0'";
    $result_count = $wpdb->get_results($sql);
    $count = count($result_count);
    $sql = $sql . ' LIMIT ' . $start . ', ' . $per_page . '';
    $result_page_data = $wpdb->get_results($sql);
    $msg = '<style type="text/css">
      #loading{
          width: 50px;
          position: absolute;
          height:50px;
      }
      #inner_content{
         padding: 0 20px 0 0!important;
      }
      #inner_content .pagination ul li.inactive,
      #inner_content .pagination ul li.inactive:hover{
          background-color:#ededed;
          color:#bababa;
          border:1px solid #bababa;
          cursor: default;
      }
      #inner_content .data ul li{
          list-style: none;
          font-family: verdana;
          margin: 5px 0 5px 0;
          color: #000;
          font-size: 13px;
      }
      #inner_content .pagination{
          width: 80%;/*800px;*/
          height: 45px;
      }
      #inner_content .pagination ul li{
          list-style: none;
          float: left;
          border: 1px solid #006699;
          padding: 2px 6px 2px 6px;
          margin: 0 3px 0 3px;
          font-family: arial;
          font-size: 14px;
          color: #006699;
          font-weight: bold;
          background-color: #f2f2f2;
      }
      #inner_content .pagination ul li:hover{
          color: #fff;
          background-color: #006699;
          cursor: pointer;
      }
      .go_button
      {
        background-color:#f2f2f2;
        border:1px solid #006699;
        color:#cc0000;
        padding:2px 6px 2px 6px;
        cursor:pointer;
        position:absolute;
        /*margin-top:-1px;*/
        width:50px;
      }
      .total
      {
        float:right;
        font-family:arial;
        color:#999;
        padding-right:150px;
      }
      #namediv input {
        width:5%!important;
      }
    </style>';
    $msg .= "<div id='content_top'></div>";

    if (count($result_page_data)) {
      $msg .= '<table class="wp-list-table widefat fixed bookmarks" cellspacing="0">
                      <thead>
                        <tr>
                          <th>' . __("Room", "sc-scbooking") . '</th>
                          <th>' . __("From Date", "sc-scbooking") . '</th>
                          <th>' . __("To Date", "sc-scbooking") . '</th>
                          <th>' . __("Email", "sc-scbooking") . '</th>
                          <th>' . __("Phone", "sc-scbooking") . '</th>
                          <th>' . __("Payment Status", "sc-scbooking") . '</th>
                          <th colspan="2"></th>
                        </tr>
                      </thead>
                      <tr>';
      foreach ($result_page_data as $booking) {
        $p_status = '';
        if ($booking->order_status == 1) {
          //echo 'Manual Payment';
          $p_status = 'Manual Payment';
        } else if ($booking->order_status == 2) {
          //echo 'Paid';
          $p_status = 'Paid';
        }
        $msg .= '<tr class="alternate">
                                <td>' . sprintf(__("%s", "sc-scbooking"), $booking->room) . '</td>
                                <td>' . sprintf(__("%s", "sc-scbooking"), $booking->from_date) . '</td>
                                <td>' . sprintf(__("%s", "sc-scbooking"), $booking->to_date) . '</td>
                                <td>' . sprintf(__("%s", "sc-scbooking"), $booking->email) . '</td>
                                <td>' . sprintf(__("%s", "sc-scbooking"), $booking->phone) . '</td>
                                <td>' . sprintf(__("%s", "sc-scbooking"), $p_status) . '</td>
                                <td colspan="2">
                            ';
        $msg .= '<a onclick="sc_open_edit_popup(' . $booking->booking_id . ')" style="cursor:pointer;text-decoration:none;" >' . __("edit", "sc-scbooking") . '</a>
                                  &nbsp;&nbsp;&nbsp;<a id="delete_booking" href="#" >' . __("delete", "sc-scbooking") . '</a>
                                  <input type="hidden" id="hdnbookingid"  name="hdnbookingid" value="' . $booking->booking_id . '" />

                                </td>
                            </tr>';
      }
      $msg .= '</tr>
                            <tfoot>
                              <tr>
                                <th>' . __("Room", "sc-scbooking") . '</th>
                                <th>' . __("From Date", "sc-scbooking") . '</th>
                                <th>' . __("To Date", "sc-scbooking") . '</th>
                                <th>' . __("Email", "sc-scbooking") . '</th>
                                <th>' . __("Phone", "sc-scbooking") . '</th>
                                <th>' . __("Payment Status", "sc-scbooking") . '</th>
                                <th colspan="2"></th>
                              </tr>
                            </tfoot>
                          </table>';
    } else {
      $msg .= '<div style="padding:80px;color:red;">' . __("Sorry! No Data Found!", "sc-scbooking") . '</div>';
    }
    $msg = "<div class='data'>" . _e($msg) . "</div>"; // Content for Data
    $no_of_paginations = ceil($count / $per_page);
    /* ---------------Calculating the starting and endign values for the loop----------------------------------- */
    if ($cur_page >= 7) {
      $start_loop = $cur_page - 3;
      if ($no_of_paginations > $cur_page + 3)
        $end_loop = $cur_page + 3;
      else if ($cur_page <= $no_of_paginations && $cur_page > $no_of_paginations - 6) {
        $start_loop = $no_of_paginations - 6;
        $end_loop = $no_of_paginations;
      } else {
        $end_loop = $no_of_paginations;
      }
    } else {
      $start_loop = 1;
      if ($no_of_paginations > 7)
        $end_loop = 7;
      else
        $end_loop = $no_of_paginations;
    }
    /* ----------------------------------------------------------------------------------------------------------- */
    $msg .= "<div class='pagination'><ul>";
    // FOR ENABLING THE FIRST BUTTON
    if ($first_btn && $cur_page > 1) {
      $msg .= "<li p='1' class='active'>" . __('First', 'sc-scbooking') . "</li>";
    } else if ($first_btn) {
      $msg .= "<li p='1' class='inactive'>" . __('First', 'sc-scbooking') . "</li>";
    }
    // FOR ENABLING THE PREVIOUS BUTTON
    if ($previous_btn && $cur_page > 1) {
      $pre = $cur_page - 1;
      $msg .= "<li p='$pre' class='active'>" . __('Previous', 'sc-scbooking') . "</li>";
    } else if ($previous_btn) {
      $msg .= "<li class='inactive'>" . __('Previous', 'sc-scbooking') . "</li>";
    }
    for ($i = $start_loop; $i <= $end_loop; $i++) {

      if ($cur_page == $i)
        $msg .= "<li p='$i' style='color:#fff;background-color:#006699;' class='active'>{$i}</li>";
      else
        $msg .= "<li p='$i' class='active'>{$i}</li>";
    }
    // TO ENABLE THE NEXT BUTTON
    if ($next_btn && $cur_page < $no_of_paginations) {
      $nex = $cur_page + 1;
      $msg .= "<li p='$nex' class='active'>" . __('Next', 'sc-scbooking') . "</li>";
    } else if ($next_btn) {
      $msg .= "<li class='inactive'>" . __('Next', 'sc-scbooking') . "</li>";
    }
    // TO ENABLE THE END BUTTON
    if ($last_btn && $cur_page < $no_of_paginations) {
      $msg .= "<li p='$no_of_paginations' class='active'>" . __('Last', 'sc-scbooking') . "</li>";
    } else if ($last_btn) {
      $msg .= "<li p='$no_of_paginations' class='inactive'>" . __('Last', 'sc-scbooking') . "</li>";
    }
    $goto = "<input type='text' class='goto' size='1' style='margin-left:30px;height:24px;'/><input type='button' id='go_btn' class='go_button' value='" . __('Go', 'sc-scbooking') . "'/>";
    $total_string = "<span class='total' a='$no_of_paginations'>" . __('Page', 'sc-scbooking') . " <b>" . _e($cur_page) . "</b> of <b>" . _e($no_of_paginations) . "</b></span>";
    $img_loading = "<span ><div id='loading'></div></span>";
    $msg = $msg . "" . $goto . $total_string . $img_loading . "</ul></div>";  // Content for pagination
    echo $msg;
  }
  exit;
}

add_action('wp_ajax_nopriv_sc_load_managebooking_data_front', 'sc_load_managebooking_data_front');
add_action('wp_ajax_sc_load_managebooking_data_front', 'sc_load_managebooking_data_front');

function sc_get_room_bycat() {
  if (isset($_REQUEST)) {
    global $table_prefix, $wpdb;
    $term_id = esc_attr($_REQUEST['term_id']);
    $sql_room = "select * from " . $table_prefix . "term_taxonomy tt inner join " . $table_prefix . "term_relationships tr on tt.term_taxonomy_id = tr.term_taxonomy_id inner join " . $table_prefix . "posts p on p.id=tr.object_id inner join " . $table_prefix . "postmeta pm on pm.post_id= p.id where p.post_status = 'publish' and tt.term_id=" . $term_id . " and pm.meta_key='_room_price'";
    $result = $wpdb->get_results($sql_room);
    echo json_encode($result);
  }
  exit;
}

add_action('wp_ajax_nopriv_sc_get_room_bycat', 'sc_get_room_bycat');
add_action('wp_ajax_sc_get_room_bycat', 'sc_get_room_bycat');

function sc_save_paymentmethod() {
  if (count($_POST) > 0) {
    global $table_prefix, $wpdb;
    $hdnpaymentmethodid = esc_attr($_REQUEST['hdnpaymentmethodid']);
    $paymentmethod_name = esc_attr($_REQUEST['paymentmethod']);
    $values = array(
        'payment_method' => $paymentmethod_name
    );
    if ($hdnpaymentmethodid == "" || $hdnpaymentmethodid == NULL) {
      $wpdb->insert($table_prefix . 'sc_scbooking_paymentmethods', $values);
      $inserted_id = $wpdb->insert_id;
      echo $inserted_id;
    } else {
      $wpdb->update(
              $table_prefix . 'sc_scbooking_paymentmethods', $values, array('id' => $hdnpaymentmethodid)
      );
      echo $hdnpaymentmethodid;
    }
  }
  exit;
}

add_action('wp_ajax_nopriv_sc_save_paymentmethod', 'sc_save_paymentmethod');
add_action('wp_ajax_sc_save_paymentmethod', 'sc_save_paymentmethod');

function sc_save_cssfixfront() {
  if (count($_POST) > 0) {
    global $table_prefix, $wpdb;
    $cssfix = esc_attr($_REQUEST['cssfix']);
    $css = esc_attr($_REQUEST['css']);
    $isupdate = "";
    if ($cssfix == "front") {
      $isupdate = update_option('cssfix_front', $css);
    }
    if ($isupdate) {
      echo "added";
    }
  }
  exit;
}

add_action('wp_ajax_nopriv_sc_save_cssfixfront', 'sc_save_cssfixfront');
add_action('wp_ajax_sc_save_cssfixfront', 'sc_save_cssfixfront');

function sc_search_booking() {	
  if (isset($_REQUEST['searchtext'])) {
    global $table_prefix, $wpdb;
    $search_text = esc_sql($_REQUEST['searchtext']);
    $sql = "select * from " . $table_prefix . "sc_scbooking where email='" . $search_text . "' or phone='" . $search_text . "' or tracking_no='" . $search_text . "'";
    $result = $wpdb->get_results($sql);
    $msg = "<div id='content_top'></div>";
    if (count($result)) {
      $msg .= '<table class="wp-list-table widefat fixed bookmarks" cellspacing="0">
                      <thead>
                        <tr>
                          <th>' . __("Room", "sc-scbooking") . '</th>
                          <th>' . __("From Date", "sc-scbooking") . '</th>
                          <th>' . __("To Date", "sc-scbooking") . '</th>
                          <th>' . __("Email", "sc-scbooking") . '</th>
                          <th>' . __("Phone", "sc-scbooking") . '</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tr>';
      foreach ($result as $booking) {
        $msg .= '<tr class="alternate">
                                <td>' . sprintf(__("%s", "sc-scbooking"), $booking->room) . '</td>
                                <td>' . sprintf(__("%s", "sc-scbooking"), $booking->from_date) . '</td>
                                <td>' . sprintf(__("%s", "sc-scbooking"), $booking->to_date) . '</td>
                                <td>' . sprintf(__("%s", "sc-scbooking"), $booking->email) . '</td>
                                <td>' . sprintf(__("%s", "sc-scbooking"), $booking->phone) . '</td>

                                <td>
                                  ';
        /* if(!$booking->confirmed){
          $msg .= '<a id="lnkapprove" href="" > Approve </a>&nbsp;&nbsp;&nbsp;';
          }
          else {
          $msg .= '<span id="" > <b>Approved </b></span>&nbsp;&nbsp;&nbsp;';
          } */
        $msg .= '<a id="delete_booking" href="#" onclick="return confirm("Are you sure want to delete");">' . __("delete", "sc-scbooking") . '</a>
                                  <input type="hidden" id="hdnbookingid"  name="hdnbookingid" value="' . $booking->booking_id . '" />
                                </td>
                            </tr>';
      }
      $msg .= '</tr>
                            <tfoot>
                              <tr>
                                  <th>' . __("Room", "sc-scbooking") . '</th>
								  <th>' . __("From Date", "sc-scbooking") . '</th>
								  <th>' . __("To Date", "sc-scbooking") . '</th>
								  <th>' . __("Email", "sc-scbooking") . '</th>
								  <th>' . __("Phone", "sc-scbooking") . '</th>
                                <th></th>
                              </tr>
                            </tfoot>
                          </table>';
    } else {
      $msg .= '<div style="padding:80px;color:red;">' . __("Sorry! No Data Found!", "sc-scbooking") . '</div>';
    }
    $msg = "<div class='data'>" . _e($msg) . "</div>";
    echo $msg;
  }
  exit;
}

add_action('wp_ajax_nopriv_sc_search_booking', 'sc_search_booking');
add_action('wp_ajax_sc_search_booking', 'sc_search_booking');

function sc_load_managebooking_data() {	
  if ($_POST['page']) {
    $page = $_POST['page'];
    $cur_page = $page;
    $page -= 1;
    $per_page = 10; //15;
    $previous_btn = true;
    $next_btn = true;
    $first_btn = true;
    $last_btn = true;
    $start = $page * $per_page;
    global $table_prefix, $wpdb;
    $sql = "select * from " . $table_prefix . "sc_scbooking where order_status != '0' order by booking_id desc";
    $result_count = $wpdb->get_results($sql);
    $count = count($result_count);
    $sql = $sql . ' LIMIT ' . $start . ', ' . $per_page . '';
    $result_page_data = $wpdb->get_results($sql);
    $msg = "<style type='text/css'>
      /*-----paginations------*/
      #loading{
          width: 50px;
          position: absolute;
          /*top: 100px;
          left: 100px;
          margin-top:200px;*/
          height:50px;
      }
      #inner_content{
         padding: 0 20px 0 0!important;
      }
      #inner_content .pagination ul li.inactive,
      #inner_content .pagination ul li.inactive:hover{
          background-color:#ededed;
          color:#bababa;
          border:1px solid #bababa;
          cursor: default;
      }
      #inner_content .data ul li{
          list-style: none;
          font-family: verdana;
          margin: 5px 0 5px 0;
          color: #000;
          font-size: 13px;
      }

      #inner_content .pagination{
          width: 80%;/*800px;*/
          height: 45px;
      }
      #inner_content .pagination ul li{
          list-style: none;
          float: left;
          border: 1px solid #006699;
          padding: 2px 6px 2px 6px;
          margin: 0 3px 0 3px;
          font-family: arial;
          font-size: 14px;
          color: #006699;
          font-weight: bold;
          background-color: #f2f2f2;

          /*display:inline;
          cursor:pointer;*/
      }
      #inner_content .pagination ul li:hover{
          color: #fff;
          background-color: #006699;
          cursor: pointer;
      }
      .go_button{
        background-color:#f2f2f2;
        border:1px solid #006699;
        color:#cc0000;
        padding:2px 6px 2px 6px;
        cursor:pointer;
        position:absolute;
        /*margin-top:-1px;*/
        width:50px;
      }
      .total{
        float:right;
        font-family:arial;
        color:#999;
        padding-right:150px;
      }
      
      #namediv input {
        width:5%!important;
      }
   </style> ";
    $msg .= "<div id='content_top'></div>";
    // for manage table _020		
    if (count($result_page_data)) {
      $msg .= '<table class="wp-list-table widefat fixed bookmarks" cellspacing="0">
                  <thead>
                    <tr>
                      <th>' . __("Id", "sc-scbooking") . '</th>
                      <th>' . __("Room", "sc-scbooking") . '</th>
                      <th>' . __("Check-in", "sc-scbooking") . '</th>
                      <th>' . __("Check-out", "sc-scbooking") . '</th>
                      <th>' . __("Email", "sc-scbooking") . '</th>
                      <th>' . __("Phone", "sc-scbooking") . '</th>
                      <th>' . __("Status", "sc-scbooking") . '</th>
                      <th colspan="2"></th>
                    </tr>
                  </thead>
                  <tr>';
      foreach ($result_page_data as $booking) {
        $to_date = $booking->to_date;
        $to_date = date('Y-m-d', strtotime('+1 day', strtotime($to_date)));
        $p_status = '';
        if ($booking->order_status == 1) {
          $p_status = 'Manual Payment';
        } else if ($booking->order_status == 2) {
          $p_status = 'Paid';
        }
        $msg .= '<tr class="alternate">
                            <td>' . sprintf(__("%s", "sc-scbooking"), $booking->booking_id) . '</td> 
                            <td>' . sprintf(__("%s", "sc-scbooking"), $booking->room) . '</td>
                            <td>' . sprintf(__("%s", "sc-scbooking"), $booking->from_date) . '</td>
                            <td>' . sprintf(__("%s", "sc-scbooking"), $to_date) . '</td>
                            <td>' . sprintf(__("%s", "sc-scbooking"), $booking->email) . '</td>
                            <td>' . sprintf(__("%s", "sc-scbooking"), $booking->phone) . '</td>
                            <td>' . sprintf(__("%s", "sc-scbooking"), $p_status) . '</td>
                            <td colspan="2">
                              ';
        $msg .= '<a style="cursor:pointer;" id="delete_booking">' . __("delete", "sc-scbooking") . '</a>
                              <input type="hidden" id="hdnbookingid"  name="hdnbookingid" value="' . $booking->booking_id . '" />
                            </td>
                        </tr>';
      }
      $msg .= '</tr>
                        <tfoot>
                          <tr>
                            <th>' . __("Id", "sc-scbooking") . '</th>
                            <th>' . __("Room", "sc-scbooking") . '</th>
                            <th>' . __("Check-in", "sc-scbooking") . '</th>
                            <th>' . __("Check-out", "sc-scbooking") . '</th>
                            <th>' . __("Email", "sc-scbooking") . '</th>
                            <th>' . __("Phone", "sc-scbooking") . '</th>
                            <th>' . __("Status", "sc-scbooking") . '</th>
                            <th colspan="2"></th>
                          </tr>
                        </tfoot>
                      </table>';
    } else {
      $msg .= '<div style="padding:80px;color:red;">' . __("Sorry! No Data Found!", "sc-scbooking") . '</div>';
    }
    $msg = "<div class='data'>" . _e($msg) . "</div>"; // Content for Data
    $no_of_paginations = ceil($count / $per_page);
    /* ---------------Calculating the starting and endign values for the loop----------------------------------- */
    if ($cur_page >= 7) {
      $start_loop = $cur_page - 3;
      if ($no_of_paginations > $cur_page + 3)
        $end_loop = $cur_page + 3;
      else if ($cur_page <= $no_of_paginations && $cur_page > $no_of_paginations - 6) {
        $start_loop = $no_of_paginations - 6;
        $end_loop = $no_of_paginations;
      } else {
        $end_loop = $no_of_paginations;
      }
    } else {
      $start_loop = 1;
      if ($no_of_paginations > 7)
        $end_loop = 7;
      else
        $end_loop = $no_of_paginations;
    }
    /* ----------------------------------------------------------------------------------------------------------- */
    $msg .= "<div class='pagination'><ul>";
    // FOR ENABLING THE FIRST BUTTON
    if ($first_btn && $cur_page > 1) {
      $msg .= "<li p='1' class='active'>" . __('First', 'sc-scbooking') . "</li>";
    } else if ($first_btn) {
      $msg .= "<li p='1' class='inactive'>" . __('First', 'sc-scbooking') . "</li>";
    }
    // FOR ENABLING THE PREVIOUS BUTTON
    if ($previous_btn && $cur_page > 1) {
      $pre = $cur_page - 1;
      $msg .= "<li p='$pre' class='active'>" . __('Previous', 'sc-scbooking') . "</li>";
    } else if ($previous_btn) {
      $msg .= "<li class='inactive'>" . __('Previous', 'sc-scbooking') . "</li>";
    }
    for ($i = $start_loop; $i <= $end_loop; $i++) {
      if ($cur_page == $i)
        $msg .= "<li p='$i' style='color:#fff;background-color:#006699;' class='active'>{$i}</li>";
      else
        $msg .= "<li p='$i' class='active'>{$i}</li>";
    }
    // TO ENABLE THE NEXT BUTTON
    if ($next_btn && $cur_page < $no_of_paginations) {
      $nex = $cur_page + 1;
      $msg .= "<li p='$nex' class='active'>" . __('Next', 'sc-scbooking') . "</li>";
    } else if ($next_btn) {
      $msg .= "<li class='inactive'>" . __('Next', 'sc-scbooking') . "</li>";
    }
    // TO ENABLE THE END BUTTON
    if ($last_btn && $cur_page < $no_of_paginations) {
      $msg .= "<li p='$no_of_paginations' class='active'>" . __('Last', 'sc-scbooking') . "</li>";
    } else if ($last_btn) {
      $msg .= "<li p='$no_of_paginations' class='inactive'>" . __('Last', 'sc-scbooking') . "</li>";
    }
    $goto = "<input type='text' class='goto' size='1' style='margin-left:30px;height:24px;'/><input type='button' id='go_btn' class='go_button' value='" . __('Go', 'sc-scbooking') . "'/>";
    //$total_string = "<span class='total' a='$no_of_paginations'>".__('Page','sc-scbooking')." <b>" ._e($cur_page) . "</b> of <b>"._e($no_of_paginations)."</b></span>";
    $total_string = "<span class='total' a='$no_of_paginations'>" . __('Page', 'sc-scbooking') . " of <b>" . $no_of_paginations . "</b></span>";
    $img_loading = "<span ><div id='loading'></div></span>";
    $msg = $msg . "" . $goto . $total_string . $img_loading . "</ul></div>";  // Content for pagination
    echo $msg;
  }
  exit;
}

add_action('wp_ajax_nopriv_sc_load_managebooking_data', 'sc_load_managebooking_data');
add_action('wp_ajax_sc_load_managebooking_data', 'sc_load_managebooking_data');

function sc_activate_booking() {
  if (count($_POST) > 0) {
    global $table_prefix, $wpdb;
    $bookingid = esc_attr($_REQUEST['booking_id']);
    $values = array('confirmed' => 1);
    $wpdb->update(
            $table_prefix . 'sc_scbooking', $values, array('booking_id' => $bookingid)
    );
    echo $bookingid;
  }
  exit;
}

add_action('wp_ajax_nopriv_sc_activate_booking', 'sc_activate_booking');
add_action('wp_ajax_sc_activate_booking', 'sc_activate_booking');

function sc_delete_booking() {
  if (count($_POST) > 0) {
    global $table_prefix, $wpdb;
    $bookingid = esc_attr($_REQUEST['booking_id']);
    $aff_rows = $wpdb->query("delete from " . $table_prefix . "sc_scbooking where booking_id='" . $bookingid . "'");
    echo $aff_rows;
  }
  exit;
}

add_action('wp_ajax_nopriv_sc_delete_booking', 'sc_delete_booking');
add_action('wp_ajax_sc_delete_booking', 'sc_delete_booking');

function sc_delete_paymentmethod() {
  if (count($_POST) > 0) {
    global $table_prefix, $wpdb;
    $paymentmethodid = esc_attr($_REQUEST['paymentmethod_id']);
    $aff_rows = $wpdb->query("delete from " . $table_prefix . "sc_scbooking_paymentmethods where id='" . $paymentmethodid . "'");
    echo $aff_rows;
  }
  exit;
}

add_action('wp_ajax_nopriv_sc_delete_paymentmethod', 'sc_delete_paymentmethod');
add_action('wp_ajax_sc_delete_paymentmethod', 'sc_delete_paymentmethod');

function sc_export_booking() {
  if ($_POST) {
    $export_data = esc_attr($_REQUEST['export_data']);
    $file_name = "booking_" . uniqid() . ".csv";
    $file_path = SCBOOKING_PLUGIN_URL . "/operations/" . $file_name;
    $fp = fopen($file_path, 'w');
    fwrite($fp, $export_data);
    fclose($fp);
  }
  exit;
}

add_action('wp_ajax_nopriv_sc_export_booking', 'sc_export_booking');
add_action('wp_ajax_sc_export_booking', 'sc_export_booking');
//===============================End Ajax Call =================================================================
define('WP_CUSTOM_PRODUCT_URL', plugins_url('', __FILE__));
define('WP_CUSTOM_PRODUCT_PATH', plugin_dir_path(__FILE__));

function add_admin_additional_script() {
  wp_enqueue_script('thickbox');
  wp_enqueue_style('thickbox');
  wp_enqueue_media();

  wp_enqueue_script('post');
  //wp_enqueue_style('scpd_admin_style', plugins_url('/scpd_resource/admin/css/admin.css', __FILE__));
}

function add_frontend_additional_script() {
  //wp_enqueue_style('custom.css', plugins_url('/scpd_resource/css/custom.css', __FILE__));
}

//=====fullcalendar script===================
function sc_fullcalendarincludejs() {
  wp_register_script('jquery.multiple.select', plugins_url('/multiselect/multiple-select/jquery.multiple.select.js', __FILE__), array('jquery'));
  wp_register_script('jquery.bt.min', plugins_url('/tooltip/beautytips-master/jquery.bt.min.js', __FILE__), array('jquery'));
  wp_register_script('jscolor', plugins_url('/jscolor/jscolor.js', __FILE__));
  wp_enqueue_script('jquery');
  wp_enqueue_script('jquery-ui-core');
  wp_enqueue_script('jquery-ui-dialog');
  wp_enqueue_script('jquery-ui-datepicker');
  wp_enqueue_script('jquery.multiple.select');
  wp_enqueue_script('jquery.bt.min');
  wp_enqueue_script('jscolor');
}

function sc_fullcalendarincludecss() {
  wp_register_style('jquery-ui-css', plugins_url('/assets/css/jquery/jquery-ui.css', __FILE__));
  wp_register_style('multiple-select', plugins_url('/multiselect/multiple-select/multiple-select.css', __FILE__));
  wp_register_style('addbooking_back_popup_css', plugins_url('/assets/css/add_booking.css', __FILE__));
  wp_register_style('add_booking_backend_popup', plugins_url('/assets/css/add_booking_backend_popup.css', __FILE__));
  wp_register_style('addbooking_backend_css', plugins_url('/assets/css/add_booking_backend.css', __FILE__));
  wp_register_style('jquery.bt', plugins_url('/tooltip/beautytips-master/jquery.bt.css', __FILE__));

  wp_enqueue_style('jquery-ui-css');
  wp_enqueue_style('multiple-select');
  wp_enqueue_style('addbooking_back_popup_css');
  wp_enqueue_style('add_booking_backend_popup');
  wp_enqueue_style('addbooking_backend_css');
  wp_enqueue_style('jquery.bt');
}
add_action('admin_enqueue_scripts', 'sc_fullcalendarincludejs');
add_action('admin_enqueue_scripts', 'sc_fullcalendarincludecss');

function sc_fullcalendarincludejs_front() {
  wp_register_script('jquery.multiple.select', plugins_url('/multiselect/multiple-select/jquery.multiple.select.js', __FILE__), array('jquery'));
  wp_register_script('jquery.bt.min', plugins_url('/tooltip/beautytips-master/jquery.bt.min.js', __FILE__));
  wp_register_script('fullcalendar', plugins_url('/fullcalendar/fullcalendar-1.6.4/fullcalendar/fullcalendar.js', __FILE__), array('jquery'));
  wp_enqueue_script('jquery');
  wp_enqueue_script('jquery-ui-core');
  wp_enqueue_script('jquery-ui-dialog');
  wp_enqueue_script('jquery-ui-datepicker');
  wp_enqueue_script('jquery.multiple.select');
  wp_enqueue_script('jquery.bt.min'); 
  wp_enqueue_script('fullcalendar');
}

function sc_fullcalendarincludecss_front() {
  wp_register_style('add_booking', plugins_url('/assets/css/add_booking.css', __FILE__));
  wp_register_style('multiple-select', plugins_url('/multiselect/multiple-select/multiple-select.css', __FILE__));
  wp_register_style('jquery.bt', plugins_url('/tooltip/beautytips-master/jquery.bt.css', __FILE__));
  wp_register_style('jquery-ui', plugins_url('/assets/css/jquery/jquery-ui.css', __FILE__));
  wp_register_style('fullcalendar', plugins_url('/fullcalendar/fullcalendar-1.6.4/fullcalendar/fullcalendar.css', __FILE__));
  wp_register_style('fullcalendar.print', plugins_url('/fullcalendar/fullcalendar-1.6.4/fullcalendar/fullcalendar.print.css', __FILE__));

  wp_enqueue_style('jquery');
  wp_enqueue_style('add_booking');
  wp_enqueue_style('jquery-ui');
  wp_enqueue_style('multiple-select');
  wp_enqueue_style('jquery.bt');
  wp_enqueue_style('fullcalendar');
  wp_enqueue_style('fullcalendar.print');
}

function add_bookingbackend_js() {
  wp_register_script('calendar_backend_js', plugins_url('/assets/js/calendar.js', __FILE__));
  wp_enqueue_script('calendar_backend_js');
}

add_action('admin_footer', 'add_bookingbackend_js');

function add_bookingfront_js() {
  wp_register_script('calendar_front_js', plugins_url('/assets/js/calendar_front.js', __FILE__));
  wp_enqueue_script('calendar_front_js');
}

add_action('wp_footer', 'add_bookingfront_js');

add_action('wp_enqueue_scripts', 'sc_fullcalendarincludejs_front');
add_action('wp_enqueue_scripts', 'sc_fullcalendarincludecss_front');

//-------------------------------------------------------------------

function wpb_custom_menu_page_removing() {
  remove_menu_page('order-success');
  remove_menu_page('room-details');
}

add_action('admin_menu', 'wpb_custom_menu_page_removing');