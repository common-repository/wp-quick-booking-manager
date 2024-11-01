<?php
if (!defined('ABSPATH')) exit;
global $table_prefix, $wpdb;
$sql = "select * from " . $table_prefix . "sc_scbooking where order_status !='0' order by booking_id desc";
$bookings = $wpdb->get_results($sql);
?>
<script type="text/javascript">
  jQuery(document).ready(function () {
      //============================= Search Script ========================================
      jQuery('#btnsearchbooking').on('click', function () {
          var searchtext = jQuery('#txtsearchbooking').val();
          jQuery.ajax
                  ({
                      type: "POST",
                      url: '<?php echo admin_url('admin-ajax.php'); ?>',
                      data: {
                          action: 'sc_search_booking',
                          searchtext: searchtext
                      },
                      success: function (data)
                      {
                      },
                      error: function (s, i, error) {
                          console.log(error);
                      }
                  }).done(function (data) {
              data = data.trim();
              sc_loading_hide();
              jQuery("#inner_content").html(data);
          });


      });
      //============================= Pagination Script=====================================
      sc_load_moredeals_data(1);
      /*----------------More Deals------------------*/
      function sc_load_moredeals_data(page) {
          sc_loading_show();
          jQuery.ajax
                  ({
                      type: "POST",
                      url: '<?php echo admin_url('admin-ajax.php'); ?>',
                      data: {
                          action: 'sc_load_managebooking_data',
                          page: page
                      },
                      success: function (msg)
                      {
                      }
                  }).done(function (msg) {
              sc_loading_hide();
              jQuery("#inner_content").html(msg);
          });

      }
      /*---------------------------------------------*/
      function sc_loading_show() {
          jQuery('#loading').html("<img src='<?php echo SCBOOKING_PLUGIN_URL; ?>/images/loading.gif'/>").fadeIn('fast');
      }
      function sc_loading_hide() {
          jQuery('#loading').fadeOut('fast');
      }
      jQuery('#inner_content').delegate('.pagination li.active', 'click', function () {
          var page = jQuery(this).attr('p');
          sc_load_moredeals_data(page);
          jQuery('html, body').animate({
              scrollTop: jQuery("#content_top").offset().top
          }, 1950);

      });
      jQuery('#inner_content').delegate('#go_btn', 'click', function () {
          var page = parseInt(jQuery('.goto').val());
          var no_of_pages = parseInt(jQuery('.total').attr('a'));
          if (page != 0 && page <= no_of_pages) {
              sc_load_moredeals_data(page);
              jQuery('html, body').animate({
                  scrollTop: jQuery("#content_top").offset().top
              }, 2050);
          } else {
              alert('<?php _e("Enter a PAGE between 1 and ", "sc-scbooking"); ?>' + no_of_pages);
              jQuery('.goto').val("").focus();
              return false;
          }

      });
      //=========================== End pagination Script=====================================
      jQuery('#inner_content').delegate('#lnkapprove', 'click', function (e) {
          e.preventDefault();
          var bookingid = jQuery(this).parent().children('#hdnbookingid').val();//jQuery('#hdnbookingid').val();
          jQuery.ajax({
              type: "POST",
              url: '<?php echo admin_url('admin-ajax.php'); ?>',
              data: {
                  action: 'sc_activate_booking',
                  booking_id: bookingid
              },
              success: function (data) {
                  var count = data.length;
                  if (count > 0) {
                      alert('<?php _e("Booking Activated", "sc-scbooking"); ?>');
                  }
              },
              error: function (s, i, error) {
                  console.log(error);
              }
          });

      });

      jQuery('#inner_content').delegate('#delete_booking', 'click', function (e) {
          e.preventDefault();
          if (!confirm('Are you sure want to delete')) {
              return false;
          }
          var bookingid = jQuery(this).parent().children('#hdnbookingid').val();//jQuery('#hdnbookingid').val();
          jQuery.ajax({
              type: "POST",
              url: '<?php echo admin_url('admin-ajax.php'); ?>',
              data: {
                  action: 'sc_delete_booking',
                  booking_id: bookingid
              },
              success: function (data) {
                  var count = data.length;
                  if (count > 0) {
                      alert('<?php _e("Booking Deleted", "sc-scbooking"); ?>');
                      window.location.href = "<?php echo get_option('siteurl'); ?>/wp-admin/edit.php?post_type=sc_custom_booking&page=manage-booking-menu";
                  }
              },
              error: function (s, i, error) {
                  console.log(error);
              }
          });
          console.log(jQuery(this).parent().parent().remove());
      });

  });
</script>
<style type="text/css">
    #btnsearchbooking{
        background:url('<?php echo SCBOOKING_PLUGIN_URL ?>/images/search.png') no-repeat;
        width: 30px; 
        height: 30px; 
        cursor:pointer;
    }
</style>
<div class="wrapper">
    <div class="wrap" style="float:left; width:97%;">
        <div id="icon-options-general" class="icon32"><br />
        </div>        
        <div class="main_div">
            <div class="metabox-holder" style="width:98%; float:left;">
                <div id="namediv" class="stuffbox" style="width:100%;">
                    <h3 class="top_bar"><?php _e("Manage Booking", "sc-scbooking"); ?></h3>
                    <div id="inner_content">		
                        <div class="data"></div>
                        <div class="pagination"></div>			
                        <table class="wp-list-table widefat fixed bookmarks" cellspacing="0">
                            <thead>
                                <tr>
                                    <th><?php _e("Id", "sc-scbooking"); ?></th>
                                    <th><?php _e("Room", "sc-scbooking"); ?></th>
                                    <th><?php _e("Check-in", "sc-scbooking"); ?></th>
                                    <th><?php _e("Check-out", "sc-scbooking"); ?></th>
                                    <th><?php _e("Email", "sc-scbooking"); ?></th>
                                    <th><?php _e("Phone", "sc-scbooking"); ?></th>
                                    <th><?php _e("Status", "sc-scbooking"); ?></th>
                                    <th colspan="2"></th>
                                </tr>
                            </thead>
                            <tr>
                                <?php
                                foreach ($bookings as $booking) {
                                  $to_date = $booking->to_date;
                                  $to_date = date('Y-m-d', strtotime('+1 day', strtotime($to_date)));
                                  ?>
                              <tr class="alternate">
                                  <td><?php printf(__("%s", "sc-scbooking"), $booking->booking_id); ?></td>
                                  <td><?php printf(__("%s", "sc-scbooking"), $booking->room); ?></td>
                                  <td><?php printf(__("%s", "sc-scbooking"), $booking->from_date); ?></td>
                                  <td><?php printf(__("%s", "sc-scbooking"), $to_date); ?></td>
                                  <td><?php printf(__("%s", "sc-scbooking"), $booking->email); ?></td>
                                  <td><?php printf(__("%s", "sc-scbooking"), $booking->phone); ?></td>
                                  <td>
                                      <?php
                                      if ($booking->order_status == 1) {
                                        echo 'Manual Payment';
                                      } else if ($booking->order_status == 2) {
                                        echo 'Paid';
                                      }
                                      ?>
                                  </td>
                                  <td colspan="2">                  
                                      <a style="cursor:pointer;" id="delete_booking" ><?php _e("delete", "sc-scbooking"); ?></a>
                                      <input type="hidden" id="hdnbookingid"  name="hdnbookingid" value="<?php echo $booking->booking_id; ?>" />
                                  </td>
                              </tr>
                              <?php
                            }
                            ?>
                            <tfoot>
                                <tr>
                                    <th><?php _e("Id", "sc-scbooking"); ?></th>
                                    <th><?php _e("Room", "sc-scbooking"); ?></th>
                                    <th><?php _e("Check-in", "sc-scbooking"); ?></th>
                                    <th><?php _e("Check-out", "sc-scbooking"); ?></th>
                                    <th><?php _e("Email", "sc-scbooking"); ?></th>
                                    <th><?php _e("Phone", "sc-scbooking"); ?></th>
                                    <th><?php _e("Status", "sc-scbooking"); ?></th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id='loading'></div>