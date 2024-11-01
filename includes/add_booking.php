<?php
if (!defined('ABSPATH')) exit;
	global $table_prefix,$wpdb;
	$sql_taxonomy = "select * from ".$table_prefix."term_taxonomy tt inner join ".$table_prefix."terms t on tt.term_id = t.term_id where tt.taxonomy = 'sc_custom_category'";
	$taxonomies = $wpdb->get_results( $sql_taxonomy );
	$sql_paymentmethod = "select * from ".$table_prefix."sc_scbooking_paymentmethods";
	$payment_methods = $wpdb->get_results( $sql_paymentmethod );
	$current_user = wp_get_current_user();
	?>
  <script type="text/javascript">
    jQuery(function() {
    jQuery('#dtptodate').datepicker({
        dateFormat: "yy-mm-dd" 
    });
    jQuery("#dtpfromdate").datepicker({
        dateFormat: "yy-mm-dd", 
        minDate:  0,
        onSelect: function(date){
            var date2 = jQuery('#dtpfromdate').datepicker('getDate');
            date2.setDate(date2.getDate()+1);
            jQuery('#dtptodate').datepicker('setDate', date2);
        }
    });
})
  
	function sc_getUrlVars()
	{
			var vars = [], hash;
			var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
			for(var i = 0; i < hashes.length; i++)
			{
					hash = hashes[i].split('=');
					vars.push(hash[0]);
					vars[hash[0]] = hash[1];
			}
			return vars;
	}
	//
	function sc_get_roomprice(){
			var arr_rooms = new Array();
			var fromdate = jQuery('#dtpfromdate').val();
			var todate = jQuery('#dtptodate').val();
			
			var ull = jQuery('#multi_rooms_select ul');
			var slis = jQuery('li.selected', ull);
			slis.each(function(i){
			    var sli = jQuery(this).children().children();
	     		arr_rooms[i] = sli.attr('value');
			});
			
			jQuery.ajax({
					type: "POST",
          url: "<?php echo admin_url( 'admin-ajax.php' );?>",
					data: {
            action:'sc_get_roomprice_by_custompost',
            post_ids_arr : arr_rooms,from_date: fromdate,to_date: todate
          },
			 success: function (data) {             
             data = data.trim();
             if(arr_rooms.length != 0){
                jQuery('#txtCustomPrice').val(data);
                jQuery('#txtPaid').val(data);
             }
             if(arr_rooms.length==0){
               jQuery('#txtCustomPrice').val('');
                jQuery('#txtPaid').val('');
             }
					},
					complete: function (data){
						sc_calculate_due();
					},
					error : function(s , i , error){
							console.log(error);
					}
			});
	}
	function sc_setbooking_info(booking_id){
			jQuery.ajax({
					type: "POST",
          url: "<?php echo admin_url( 'admin-ajax.php' );?>",
					dataType:'json', 
					data: {
            action:'sc_get_bookings',
            booking_id:booking_id
          },
					success: function (data) {
							var count = data.length;
							if(data.length > 0 ){
								var booking = data[0];
								jQuery('.hdnbookingidcls').val(booking['booking_id']);
								var roomids = booking['room_id'].split(',');
								//jQuery('#rooms_multiselect').multiselect('select',roomids);
								jQuery('select.multiselect').multipleSelect('setSelects', roomids);								
								jQuery('#dtpfromdate').val(booking['from_date']);
								jQuery('#dtptodate').val(booking['to_date']);								
								jQuery('#txtFirstName').val(booking['first_name']);
								jQuery('#txtLastName').val(booking['last_name']);
								jQuery('#txtEmail').val(booking['email']);
								jQuery('#txtPhone').val(booking['phone']);
								jQuery('#details').val(booking['details']);
								jQuery('#txtbookingby').val(booking['booking_by']);
								jQuery('#optguest_type').val(booking['guest_type']);
								jQuery('#txtCustomPrice').val(booking['custom_price']);
								jQuery('#txtPaid').val(booking['paid']);
								jQuery('#txtDue').val(booking['due']);
								jQuery('#optpaymentmethod').val(booking['payment_method']);
								jQuery('#txtTrackingNo').val(booking['tracking_no']);
							}
					},
					error : function(s , i , error){
							console.log(error);
					}
			});
	}
  
	function sc_cleardata(){
			jQuery('#hdnbookingid').val('');
			jQuery('option', jQuery('#rooms_multiselect')).each(function(element) {
					jQuery(this).removeAttr('selected').prop('selected', false);
			});
			jQuery('#rooms_multiselect').multipleSelect("refresh");
			
			jQuery('#dtpfromdate').val('');
			jQuery('#dtptodate').val('');
			
			jQuery('#txtFirstName').val('');
			jQuery('#txtLastName').val('');
			jQuery('#txtEmail').val('');
			jQuery('#txtPhone').val('');
			jQuery('#details').val('');
			jQuery('#txtbookingby').val("<?php echo $current_user->display_name?>");
			jQuery('#optguest_type').val('');
			jQuery('#txtCustomPrice').val('');
			jQuery('#txtPaid').val('');
			jQuery('#txtDue').val('');
			jQuery('#txtTrackingNo').val('');
	}
	function sc_load_moredeals_data_pagerefresh(page){
			jQuery.ajax
			({
					type: "POST",
          url: "<?php echo admin_url( 'admin-ajax.php' );?>",
					data: {
            action:'sc_load_managebooking_data_front',
            page: page
          },
					success: function(msg)
					{
							jQuery("#inner_content").ajaxComplete(function(event, request, settings)
							{
									jQuery("#inner_content").html(msg);
							});
					}
			});	
	}
	
	jQuery(document).ready(function(){			
			jQuery('.multiselect').multipleSelect({
				placeholder: "<?php _e("Please select Room","sc-scbooking"); ?>",
				selectAll: false,
				//filter: true,
				width:'190px',
				onClick: function(view){
          sc_get_roomprice();
				}
			});			
			//-------------------------------------------------
			jQuery("#txtCustomPrice").keydown(function (e) {
					// Allow: backspace, delete, tab, escape, enter and .
					if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
							 // Allow: Ctrl+A
							(e.keyCode == 65 && e.ctrlKey === true) || 
							 // Allow: home, end, left, right
							(e.keyCode >= 35 && e.keyCode <= 39)) {
									 // let it happen, don't do anything
									 return;
					}
					// Ensure that it is a number and stop the keypress
					if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
							e.preventDefault();
					}
			});
			//=============			
			var calltype = sc_getUrlVars()["calltype"];
			if(calltype){
				if(calltype == 'editbooking'){
					<?php
					if(isset($_REQUEST['id'])){
						$id = esc_sql($_REQUEST['id']);
						global $table_prefix,$wpdb;
						$sql = "select * from ".$table_prefix."sc_scbooking where booking_id=".$id;
						$result = $wpdb->get_results($sql);
						?>
						var booking = <?php echo json_encode($result[0]);?>;
						jQuery('#hdnbookingid').val(booking['booking_id']);
						var roomids = booking['room_id'].split(',');						
						jQuery('select.multiselect').multipleSelect('setSelects', roomids);
						jQuery('#dtpfromdate').val(booking['from_date']);
						jQuery('#dtptodate').val(booking['to_date']);						
						jQuery('#txtFirstName').val(booking['first_name']);
						jQuery('#txtLastName').val(booking['last_name']);
						jQuery('#txtEmail').val(booking['email']);
						jQuery('#txtPhone').val(booking['phone']);
						jQuery('#details').val(booking['details']);
						jQuery('#txtbookingby').val(booking['booking_by']);
						jQuery('#optguest_type').val(booking['guest_type']);
						jQuery('#txtCustomPrice').val(booking['custom_price']);
						jQuery('#txtPaid').val(booking['paid']);
						jQuery('#txtDue').val(booking['due']);
						jQuery('#optpaymentmethod').val(booking['payment_method']);
						jQuery('#txtTrackingNo').val(booking['tracking_no']);
					<?php } ?>	
				}	
			}
      //---------------------------------	
			jQuery('#dtpfromdate').on("change",function(){
				sc_get_roomprice();
			});      
			jQuery('#dtptodate').on("change",function(){
        //_020
        var from_date = jQuery('#dtpfromdate').val();
			  var to_date = jQuery('#dtptodate').val();
        var startDate = new Date(from_date);
        var endDate = new Date(to_date);
        if (startDate >= endDate){
          alert("Please Choose Valid Check-out Date");
          return false;
        }
				sc_get_roomprice();
			});
			
			jQuery('#frmbooking').on('submit',function(e){
	  		 e.preventDefault();
				 sc_save_booking();
			});			
	});
	function sc_save_booking(){
			var hdnbookingid = jQuery('.hdnbookingidcls').val();
			var roomtype = ''; //jQuery('#roomtype').find('option:selected').val();
			var roomsarr = jQuery('select.multiselect').multipleSelect('getSelects', 'text');
			
			var rooms= "";
			for(var j=0;j<roomsarr.length;j++){
				if(j==0){
					rooms += roomsarr[j];
				}else{
					rooms += ","+roomsarr[j];
				}
			}
			//console.log(rooms);
			var arr_ids = new Array();
			var room_id = '';
			
			var ull = jQuery('#multi_rooms_select ul');
			var slis = jQuery('li.selected', ull);
			slis.each(function(i){
			    var sli = jQuery(this).children().children();
	     		arr_ids[i] = sli.attr('value');
				if(i==0){
					room_id += sli.attr('value');	
				}else{
					room_id += ","+sli.attr('value');
				}
			});
      
      //alert(arr_ids.length);
            
			
			var from_date = jQuery('#dtpfromdate').val();
			var to_date = jQuery('#dtptodate').val();
			
			var first_name = jQuery('#txtFirstName').val();
			var last_name = jQuery('#txtLastName').val();
			var email = jQuery('#txtEmail').val();
			var phone = jQuery('#txtPhone').val();
			var details = jQuery('#details').val();
			var bookingby = jQuery('#txtbookingby').val();
			var guest_type = jQuery('#optguest_type').val();
			var price = jQuery('#txtCustomPrice').val();
			var paid = jQuery('#txtPaid').val();
			var due = jQuery('#txtDue').val();
			var payment_method = jQuery('#optpaymentmethod').find('option:selected').val();
			var tracking_no = jQuery('#txtTrackingNo').val();
      
      var startDate = new Date(from_date);
      var endDate = new Date(to_date);
			//---validation----			
      if(arr_ids.length==0){  
				alert("<?php _e("Please choose at Least a Room .","sc-scbooking"); ?>");
				jQuery('button.multiselect').focus();
				return false;
			}else if(from_date==""){
				alert("<?php _e("Please choose a from date.","sc-scbooking"); ?>");
				jQuery('#dtpfromdate').focus();
				return false;
			}else if (startDate >= endDate){
        alert("Please Choose Valid Date");
        return false;
      }else if(to_date==""){
				alert("<?php _e("Please choose a to date.","sc-scbooking"); ?>");
				jQuery('#dtptodate').focus();
				return false;
			}
			else if(email!=''){
				if(!sc_validateEmail(email)){
					alert("<?php _e("Please input a valid email Address.","sc-scbooking"); ?>");
					jQuery('#txtEmail').focus();
					return false;
				}
			}
			else if(phone==''){
				alert("<?php _e("please input your phone number.","sc-scbooking"); ?>");
				jQuery('#txtPhone').focus();
				return false;
			}
			else if(paid == ''){
				alert("<?php _e("Please input paid amount.","sc-scbooking"); ?>");
				jQuery('#txtPaid').focus();
				return false;
			}
			else if(price > paid || price < paid){
				alert("<?php _e("Please pay Full Price.","sc-scbooking"); ?>");
				jQuery('#txtPaid').focus();
				return false;	
			}
			//------------------
			jQuery.ajax({
					type: "POST",
          url: "<?php echo admin_url( 'admin-ajax.php' );?>",
					data: {
            action: 'sc_check_booking',
            hdnbookingid: hdnbookingid,room: rooms,from_date:from_date,to_date:to_date,ct:1
          },
					success: function (data) {
							data = data.trim();
							if(data=='yes'){
								alert("<?php _e("Sorry! Already Booked!","sc-scbooking"); ?>");
								return;
							}
							else if(data=='no'){
                var qb_nonce= '<?php echo wp_create_nonce( "qb-data-ajax-nonce" ); ?>';
 								jQuery.ajax({
											type: "POST",
                      url: "<?php echo admin_url( 'admin-ajax.php' );?>",
											data: {
                        action:'sc_save_booking_session',
                        hdnbookingid: hdnbookingid,room_type:roomtype,roomid: room_id, room: rooms,from_date:from_date,to_date:to_date,first_name:first_name,last_name:last_name,email:email,phone:phone,details: details,bookingby: bookingby, guest_type: guest_type, price: price,paid: paid,due: due, payment_method: payment_method, tracking_no: tracking_no,ct:1, security:qb_nonce
                      },
											success: function (data) {
													if(data.length>0){
														//window.location.href = "<?php echo get_option('siteurl'); ?>/?page_id=<?php echo SHOPPINGCART_PAGEID;?>";
                            alert('Booking Complete');
                            location.reload();
													}
											},
											error : function(s , i , error){
                        alert('Something Went Wrong.Please Try Again');
													console.log(error);
											}
									});
							}
					},
					error : function(s , i , error){
							console.log(error);
					}
			});
	}
	function sc_validateEmail(email) {
			var atpos=email.indexOf("@");
			var dotpos=email.lastIndexOf(".");
			if (atpos < 1 || dotpos < atpos+2 || dotpos+2 >= email.length) {
					return false;
			}
			return true;
	}

	function sc_calculate_due(){
		$price = jQuery('#txtCustomPrice').val();
		$paid = jQuery('#txtPaid').val();
		$due = ($price - $paid);
		jQuery('#txtDue').val($due); 
	}
	//-----------------------add booking dialog-------------------------------===
  </script>
  <?php 
  $current_user = wp_get_current_user();
  $room_currency=scpd_currency_code(get_option('scpd_base_currency'));

  ?>
 <div id="addbooking_dialog" title="<?php _e("Add Booking","sc-scbooking"); ?>" class="wrapper" style="display:none;z-index:5000">
  <div class="wrap" style="float:left; width:100%;">
    <div class="main_div">
     	<div class="metabox-holder" style="width:100%; ">
            <form id="frmbooking" action="" method="post" style="width:100%">
              <table id="tbladdbookingfrontpopup">
                <tr>
                  <td class="bookinglavel"> <label for="room"><?php _e("Room","sc-scbooking"); ?> <span class="asterik" style="color:red;">*</span></label></td>
                  <td class="bookinginput" id="multi_rooms_select" >
                    <select id="rooms_multiselect" class="multiselect" multiple="multiple" >
                      <?php foreach($taxonomies as $taxo){?>
                      <option disabled="disabled" value="<?php echo $taxo->name;?>"><?php printf(__("%s","sc-scbooking"), strtoupper($taxo->name));?></option>
					  <?php 
						$term_id = esc_sql($taxo->term_id);
						$sql_room = "select * from ".$table_prefix."term_taxonomy tt inner join ".$table_prefix."term_relationships tr on tt.term_taxonomy_id = tr.term_taxonomy_id inner join ".$table_prefix."posts p on p.id=tr.object_id inner join ".$table_prefix."postmeta pm on pm.post_id= p.id where p.post_status = 'publish' and tt.term_id=".$term_id." and pm.meta_key='_room_price'";
						 $rooms = $wpdb->get_results($sql_room);	
						 foreach($rooms as $room){
						?>
                        	<option value="<?php echo $room->ID;?>"><?php printf(__("%s","sc-scbooking"), $room->post_title);?></option>
                      <?php } 
						}				  
					  ?>
                    </select>
                  </td>                  
                </tr>
                <tr>
                    <td class="bookinglavel">
                    <label for="dtpfromdate"><?php _e("Check-in:","sc-scbooking"); ?><span class="asterik" style="color:red;">*</span></label>
                  </td>
                  <td class="bookinginput">
                    <input type="text" id="dtpfromdate" name="dtpfromdate" class="rounded" value="" />
                  </td>                  
                </tr>
                <tr>
                    <td class="bookinglavel">
                    <label for="dtptodate"><?php _e("Check-out:","sc-scbooking"); ?><span class="asterik" style="color:red;">*</span> </label>
                  </td>
                  <td class="bookinginput">
                    <input type="text" id="dtptodate" name="dtptodate" value="" class="rounded" />
                  </td>                  
                </tr>
                <tr>
                  <td class="bookinglavel">
                    <label for="txtFirstName"><?php _e("First Name:","sc-scbooking"); ?> </label>
                  </td>
                  <td class="bookinginput">
                    <input type="text" id="txtFirstName" name="txtFirstName" class="rounded" value="" />
                  </td>                  
                </tr>
                <tr>
                  <td class="bookinglavel">
                    <label for="txtLastName"><?php _e("Last Name:","sc-scbooking"); ?> </label>
                  </td>
                  <td class="bookinginput">
                    <input type="text" id="txtLastName" name="txtLastName" class="rounded" value="" />
                  </td>                  
                </tr>
                <tr>
                  <td class="bookinglavel">
                    <label for="email"><?php _e("Email:","sc-scbooking"); ?> </label>
                  </td>
                  <td class="bookinginput">
                    <input type="text" id="txtEmail" name="txtEmail"  class="rounded" value="" /><!--<span style="color:red;">*</span>-->
                  </td>                  
                </tr>
                <tr>
                  <td class="bookinglavel">
                    <label for="phone"><?php _e("Phone:","sc-scbooking"); ?><span class="asterik" style="color:red;">*</span> </label>
                  </td>
                  <td class="bookinginput">
                    <input type="text" id="txtPhone" name="txtPhone" class="rounded" value="" />
                  </td>                  
                </tr>
                <tr>
                    <td class="bookinglavel">
                    <label for="details"><?php _e("Details:","sc-scbooking"); ?> </label>
                  </td>
                  <td class="bookinginput">
                    <textarea cols="30" rows="1" id="details" class="rounded" name="details"></textarea>
                  </td>
                  
                </tr>
                <tr>
                    <td class="bookinglavel">
                    <label for="txtbookingby"><?php _e("Booking By:","sc-scbooking"); ?> </label>
                  </td>
                  <td class="bookinginput">
                    <input type="text" readonly="readonly" id="txtbookingby" name="txtbookingby" class="rounded" value="<?php echo $current_user->display_name; ?>" />
                  </td>                  
                </tr>
                <tr>
                    <td class="bookinglavel">
                    <label for="optguest_type"><?php _e("Guest Type:","sc-scbooking"); ?> </label>
                  </td>
                  <td class="bookinginput">
                    <!--<input type="text" id="txtGuestType" name="txtGuestType" />-->
                    <select id="optguest_type" name="optguest_type" >
                        <option value="single"><?php _e("Single","sc-scbooking"); ?> </option>
                        <option value="business"><?php _e("Business","sc-scbooking"); ?> </option>
                        <option value="couple"><?php _e("Couple","sc-scbooking"); ?> </option>
                        <option value="group_of_adults"><?php _e("Group of Adults","sc-scbooking"); ?> </option>
                        <option value="family_with_kids"><?php _e("Family with Kids","sc-scbooking"); ?> </option>
                    </select>
                  </td>                  
                </tr>
                <tr>
                    <td class="bookinglavel">
                    <label for="txtCustomPrice"><?php _e("Price:","sc-scbooking"); ?> <?php echo $room_currency;?> </label>
                  </td>
                  <td class="bookinginput">
                    <input type="text" id="txtCustomPrice" name="txtCustomPrice" readonly class="rounded" value="" />
                  </td>                  
                </tr>
                <tr style="display:none">
                    <td class="bookinglavel">
                    <label for="txtPaid" style="visibility:hidden"><?php _e("Paid:","sc-scbooking"); ?> </label>
                  </td>
                  <td class="bookinginput">
                    <input style="visibility:hidden" type="text" id="txtPaid" name="txtPaid" class="rounded" onkeyup="sc_calculate_due()" value="" /><span style="color:red;">*</span>
                  </td>                  
                </tr>
                <tr style="display:none;">
                    <td class="bookinglavel">
                    <label for="txtDue" style="visibility:hidden;"><?php _e("Due:","sc-scbooking"); ?> </label>
                  </td>
                  <td class="bookinginput">
                    <input style="visibility:hidden" type="text" id="txtDue" name="txtDue" class="rounded" value="0" />
                    <input type="hidden" class="hdnbookingidcls" id="hdnbookingid" name="hdnbookingid" value="" style="width:150px;"/>
                  </td>                  
                </tr>
                 <tr>
                    <td class="bookinglavel">
                    <label for="optpaymentmethod"><?php _e("Payment Method:","sc-scbooking"); ?> </label>
                  </td>
                  <td class="bookinginput">
                    <select id="optpaymentmethod" name="optpaymentmethod" >
                        <?php foreach($payment_methods as $pm){?>
                        <option value="<?php echo $pm->payment_method;?>"><?php printf(__("%s","sc-scbooking"), $pm->payment_method) ;?></option>
                      <?php }?>  
                    </select>
                  </td>                  
                </tr>
                <tr style="display:none;">
                    <td class="bookinglavel">
                    <label for="txtTrackingNo" style="visibility:hidden;"><?php _e("Receipt/ Tracking No:","sc-scbooking"); ?> </label>
                  </td>
                  <td class="bookinginput">
                    <input style="visibility:hidden;" type="text" id="txtTrackingNo" name="txtTrackingNo" value="0" />
                  </td>                  
                </tr>
              </table>
            </form>
   		</div>
    </div>
  </div>
 </div>