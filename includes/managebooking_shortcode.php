<?php
if (!defined('ABSPATH')) exit;
function sc_managescbooking_shortcode($atts){
	//if ( is_user_logged_in() ){
		global $table_prefix,$wpdb;
		include_once('add_booking.php');
		//$sql = "select * from ".$table_prefix."scbooking where confirmed=0";
		$sql = "select * from ".$table_prefix."sc_scbooking";
		$bookings = $wpdb->get_results($sql);
		$output = '<style type="text/css">';
		include_once(SCBOOKING_DIR.'operations/get_cssfixfront.php');
		
		$output .='/*css for front end to override theme css*/
			button,input{
				padding:0px!important;
			}
			.entry-wrap {
				min-height:585px;
				padding: 20px 20px 20px 30px;
			}
			.pagination{
				margin:0px;
				text-align:left;
			}
			.pagination a, .pagination span {
				width:auto;
				box-shadow: 1px 1px 1px #E3E3E3;
			}
			.total{
				padding-right:50px!important;
			}
			thead tr, tfoot tr{
				background-color: #F1F1F1;
				border-radious:5px;
			}
			table{
				border: 1px solid #DFDFDF;
				border-radious:5px;
				overflow:hidden;
			}
			form {
				margin: 0px!important;
			}
			/*---set z-index of dialog popup---*/
			.ui-front {
					z-index:1000000 !important; /* The default is 100. !important overrides the default. */
			}
			/*---end css for front end to override theme css--- */
			#btnsearchbooking{
				background:url(\''.SCBOOKING_PLUGIN_URL.'/images/search.png\') no-repeat;
        width: 35px; 
        height: 35px; 
        cursor:pointer;
				border:none;
      }
		</style>
		<script type="text/jscript">
			jQuery(document).ready(function(){
						//============================= Search Script ========================================
						jQuery("#btnsearchbooking").live(\'click\',function(){
								var searchtext = jQuery(\'#txtsearchbooking\').val();
								jQuery.ajax
								({
										type: "POST",
                    url: "'.admin_url( 'admin-ajax.php' ).'",
										data: {
                      action:"sc_search_booking",
                      searchtext: searchtext
                    },
										success: function(data)
										{
												jQuery("#inner_content").ajaxComplete(function(event, request, settings)
												{
														sc_loading_hide();
														jQuery("#inner_content").html(data);
												});
										},
										error : function(s , i , error){
												console.log(error);
										}
								});
								
								
						});
					 //============================= Pagination Script=====================================
						sc_load_moredeals_data(1);
						jQuery(\'#hdnpageno\').val(1);
						/*----------------More Deals------------------*/
						function sc_load_moredeals_data(page){
								
								sc_loading_show();                    
								jQuery.ajax
								({
										type: "POST",
										url: "'.admin_url( 'admin-ajax.php' ).'",
										data: {
											action:"sc_load_managebooking_data_front",
											page: page
										},
										success: function(msg)
										{
												jQuery("#inner_content").ajaxComplete(function(event, request, settings)
												{
														sc_loading_hide();
														jQuery("#inner_content").html(msg);
												});
										}
								});
						
						}
						/*---------------------------------------------*/
						function sc_loading_show(){
								jQuery("#loading").html("<img src=\''.SCBOOKING_PLUGIN_URL.'/images/loading.gif\'/>").fadeIn(\'fast\');
						}
						function sc_loading_hide(){
								jQuery(\'#loading\').fadeOut(\'fast\');
						}                
						jQuery(\'#inner_content .pagination li.active\').live(\'click\',function(){
								var page = jQuery(this).attr(\'p\');
								//alert(page);
								jQuery(\'#hdnpageno\').val(page);
								//loadData(page);
								sc_load_moredeals_data(page);
								jQuery(\'html, body\').animate({
										scrollTop: jQuery("#content_top").offset().top
								}, 1950);
								
						});           
						jQuery(\'#go_btn\').live(\'click\',function(){
								var page = parseInt(jQuery(\'.goto\').val());
								jQuery(\'#hdnpageno\').val(page);
								var no_of_pages = parseInt(jQuery(\'.total\').attr(\'a\'));
								if(page != 0 && page <= no_of_pages){
										//loadData(page);
										sc_load_moredeals_data(page);
										jQuery(\'html, body\').animate({
												scrollTop: jQuery("#content_top").offset().top
										}, 2050);
								}else{
										alert(\'Enter a PAGE between 1 and \'+no_of_pages);
										jQuery(\'.goto\').val("").focus();
										return false;
								}
								
						});
						//=========================== End pagination Script=====================================	
					 jQuery("#lnkapprove").live("click",function(e){
						e.preventDefault();
						var bookingid = jQuery(this).parent().children("#hdnbookingid").val();
						var page_approve = jQuery(\'#hdnpageno\').val();
						//alert(bookingid);return false;
						jQuery.ajax({
								type: "POST",
                url: "'.admin_url( 'admin-ajax.php' ).'",  
								data: {
                  action: "sc_activate_booking",
                  booking_id:bookingid
                },
								success: function (data) {
										var count = data.length;
										if(count>0){
											alert("Booking Activated");
										}
								},
								complete: function(){
										//jQuery(this).parent().parent().remove();
										sc_load_moredeals_data(page_approve);
								},
								error : function(s , i , error){
										console.log(error);
								}
						});
						
					});	
					
					jQuery("#delete_booking").live("click",function(e){
						//alert(\'here\');
						e.preventDefault();
						var bookingid = jQuery(this).parent().children("#hdnbookingid").val();
						var tr_row = jQuery(this).parent().parent();
						var page = jQuery(\'#hdnpageno\').val();
						//alert(page);
						//alert(bookingid);return false;
						var confirmText = "Are you sure want to delete?";
						if(confirm(confirmText)) {
							jQuery.ajax({
									type: "POST",
									url: "'.admin_url( 'admin-ajax.php' ).'", 
									data: {
										action:"sc_delete_booking",
										booking_id:bookingid
									},
									success: function (data) {
											var count = data.length;
											if(count>0){
												
												tr_row.remove();
												alert("Booking Deleted");
												
												//console.log(tr_row.html());
												//console.log(data);
											}
									},
									complete: function(){
											//jQuery(this).parent().parent().remove();
											sc_load_moredeals_data(page);
									},
									error : function(s , i , error){
											console.log(error);
									}
							});
							//jQuery(this).parent().parent().remove();
						}
						return false;
						
					});
						
			});
			jQuery( "#addbooking_dialog" ).dialog({ 
					autoOpen: false,
					height: 590,
					width: 530,
					modal: true,
					zIndex: 4000,
					stack: false,
					buttons: {
						"Add Booking": function() {
								sc_save_booking();
								
								jQuery( this ).dialog( "close" );
								var page_updatedialog = jQuery(\'#hdnpageno\').val();
								sc_load_moredeals_data_pagerefresh(page_updatedialog);
								//jQuery("#frmgetbooking").submit();
						},
						Cancel: function() {
							jQuery( this ).dialog( "close" );
						}
					},
					close: function(event, ui ) {
						sc_cleardata();
						//var page_updatedialog = jQuery(\'#hdnpageno\').val();
						//alert(\'close\'+page_updatedialog);
						//load_moredeals_data_pagerefresh(page_updatedialog);
					} 
			});
			function sc_open_edit_popup(booking_id){
				jQuery( "#addbooking_dialog" ).dialog( "open" );
				sc_setbooking_info(booking_id);
			}
		</script>
		<div style="width:33.5%;float:right;margin-top:-55px;">
    	<form id="frmsearchb" method="post" action="">
      	<input type="text" name="txtsearchbooking" id="txtsearchbooking" value="" style="width:250px;height:40px;" />
      	<input type="button" id="btnsearchbooking" name="btnsearchbooking" value="" />
      </form>
      <!--<img src="<?php// echo SCBOOKING_PLUGIN_URL ?>/images/search.png" height="20px" width="20px" />-->
			<input type="hidden" id="hdnpageno"  name="hdnpageno" value="1" />
    </div>
		<div class="wrapper">
		<div class="wrap" style="float:left; width:100%;">
			 <div class="main_div">
			 <form id="frmmanagebookingdata" method="post" action="">
				<div class="metabox-holder" style="width:100%; float:left;">
					<div id="namediv" class="stuffbox" style="width:99%;">
						<div id="inner_content">		
							<div class="data"></div>
							<div class="pagination"></div>	
						<table class="wp-list-table widefat fixed bookmarks" cellspacing="0">
						<thead>
							<tr>
								<th>'.__("Room","sc-scbooking").'</th>
								<th>'.__("From Date","sc-scbooking").'</th>
								<th>'.__("To Date","sc-scbooking").'</th>
								<th>'.__("Email","sc-scbooking").'</th>
								<th>'.__("Phone","sc-scbooking").'</th>
								<th></th>
							</tr>
						</thead>
						<tr>
							';
						foreach($bookings as $booking){
								$output .= '<tr class="alternate">
									<td>'.sprintf(__("%s","sc-scbooking"),$booking->room).'</td>
									<td>'.sprintf(__("%s","sc-scbooking"),$booking->from_date).'</td>
									<td>'.sprintf(__("%s","sc-scbooking"),$booking->to_date).'</td>
									<td>'.sprintf(__("%s","sc-scbooking"),$booking->email).'</td>
									<td>'.sprintf(__("%s","sc-scbooking"),$booking->phone).'</td>
									<td>';
									if(!$booking->confirmed){
										$output .= '<a id="lnkapprove" href="" > '.__("Approve","sc-scbooking").' </a>&nbsp;&nbsp;&nbsp;';
										
									}
									else{
										$output .= '<span id="" > <b>'.__("Approved","sc-scbooking").' </b></span>&nbsp;&nbsp;&nbsp;';
									}
									$output .= '<a style="cursor:pointer;" id="delete_booking" >'.__("delete","sc-scbooking").'</a>
										<input type="hidden" id="hdnbookingid"  name="hdnbookingid" value="'.$booking->booking_id.'" />
										</td>
									</tr>
									';
							}
							
						 $output .= '
						</tr>
						<tfoot>
							<tr>
								<th>'.__("Room","sc-scbooking").'</th>
								<th>'.__("From Date","sc-scbooking").'</th>
								<th>'.__("To Date","sc-scbooking").'</th>
								<th>'.__("Email","sc-scbooking").'</th>
								<th>'.__("Phone","sc-scbooking").'</th>
								<th></th>
							</tr>
						</tfoot>
					</table>
					
					</div>
				</div>
			</div>
			</form>
		 </div>
		</div>
		
		<div id=\'loading\'></div>
		';	
		return $output;
	/*else{
		return "<div style='color:#C30909;'>Please login to access this page.</div>";
	}*/
}
add_shortcode('sc_managescbooking','sc_managescbooking_shortcode');