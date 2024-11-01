<?php
if (!defined('ABSPATH')) exit;
  function sc_sccalendar_shortcode($atts){		
			global $table_prefix,$wpdb;			
			define('CCB_PROCESSING_BG_COLOR','7FCA27') ;
			define('CCB_BOOKED_BG_COLOR','138219') ;
      if(!get_option('scpd_base_currency')){
        update_option('scpd_base_currency', 136);        
      }
  
			function sc_scbooking_get_opt_val_for_calendar($opt_name,$default_val){
					if(get_option($opt_name)!=''){
						return $value = get_option($opt_name);
					}else{
						return $value =$default_val;
					}
			}
      $processing_bg_color = sc_scbooking_get_opt_val_for_calendar('_processing_bg_color',CCB_PROCESSING_BG_COLOR); 
      $booked_bg_color = sc_scbooking_get_opt_val_for_calendar('_booked_bg_color',CCB_BOOKED_BG_COLOR);
			//
      $cDay = "01";//date('d');
      $cMonth = date("n");
      $cYear = date("Y");
      if($_POST){        
        if(isset($_REQUEST['booking_day'])){
          $cDay = esc_attr($_REQUEST['booking_day']);
        }
        if(isset($_REQUEST['booking_month'])){
          $cMonth = esc_attr($_REQUEST["booking_month"]);
        }
        if(isset($_REQUEST['booking_year'])){
          $cYear = esc_attr($_REQUEST["booking_year"]); 
        }
      }
			
      include_once('add_booking.php');
			
			$sql_taxonomy = "select * from ".$table_prefix."term_taxonomy tt inner join ".$table_prefix."terms t on tt.term_id = t.term_id where tt.taxonomy = 'sc_custom_category' ORDER BY tt.term_id ASC";
			$taxonomies = $wpdb->get_results( $sql_taxonomy );
	
			$sql = "select * from ".$table_prefix."sc_scbooking where order_status != '0' ";
			$scbookings = $wpdb->get_results($sql);
			
			$output = '<style type="text/css">';
			
			$output .='#prev_month{
				background:url("'.SCBOOKING_PLUGIN_URL.'/images/prev.jpg") no-repeat; 
				width: 25px; 
				height: 30px; 
				cursor:pointer;
			}
			#next_month{
				background:url("'.SCBOOKING_PLUGIN_URL.'/images/next.jpg") no-repeat;
				width: 25px; 
				height: 30px; 
				cursor:pointer;
			}
			#btnbookings{
				background:url("'.SCBOOKING_PLUGIN_URL.'/images/search.png") no-repeat;
				width: 30px; 
				height: 30px; 
				cursor:pointer;
        border:none;
			}
			#frmgetbooking table{
				margin: 0px;
			}
      #calendarhead {
        border:none;
      }
			#calendarhead input{
				border-color:#CCCCCC;
				border-width:none;
			}
      #calendarhead tr{
        border:none;
			}
			#calendarhead td{
				height: 10px;
        vertical-align: top;
        border:none;
			}
			#calendarhead #btnbookings{
				/*height: 20px;*/
			}
			/*--------------*/
			.entry-wrap {
				padding:0px;
			}
			.x-container-fluid.width {
				width:100%;
			}
			.tooltip{
				opacity:1;
				padding:0px;
				position:relative;
				color:black;
				z-index:0;
				font-size:10px;
        background:none!important;
        display:block!important;
			}
			/*------- css fix to override theme css----*/	
			form#frmgetbooking{
				margin:0px;
			}
			select{
				margin-bottom:0px;
			}
			.x-container-fluid.offset {
				margin:0px;
			}
			.room_listing_name{
				font-size: 13px;
			}
			.x-btn, .button, [type="submit"] {
					box-shadow: none;
			}
      #booking_month{
        font-size:12px;
        padding:5px 2px;
        vertical-align:top;
      }
      #booking_year{
        font-size:12px;
        padding:5px 2px;
        vertical-align:top;
      }
		</style>';
		include_once(SCBOOKING_DIR.'operations/get_cssfixfront.php');
    $selectedm1 ="";$selectedm2 ="";$selectedm3 ="";$selectedm4 ="";$selectedm5 ="";$selectedm6 ="";$selectedm7 ="";$selectedm8 ="";$selectedm9 ="";$selectedm10 ="";$selectedm11 ="";$selectedm12 ="";
      if($cMonth==1){
        $selectedm1 = "selected='selected'";
      }
      else if($cMonth==2){
        $selectedm2 = "selected='selected'";
      }
      else if($cMonth==3){
        $selectedm3 = "selected='selected'";
      }
      else if($cMonth==4){
        $selectedm4 = "selected='selected'";
      }
      else if($cMonth==5){
        $selectedm5 = "selected='selected'";
      }
      else if($cMonth==6){
        $selectedm6 = "selected='selected'";
      }
      else if($cMonth==7){
        $selectedm7 = "selected='selected'";
      }
      else if($cMonth==8){
        $selectedm8 = "selected='selected'";
      }
      else if($cMonth==9){
        $selectedm9 = "selected='selected'";
      }
      else if($cMonth==10){
        $selectedm10 = "selected='selected'";
      }
      else if($cMonth==11){
        $selectedm11 = "selected='selected'";
      }
      else if($cMonth==12){
        $selectedm12 = "selected='selected'";
      }          
    $c_year=  date("Y");
    $startYear=date("Y")-2;
    $endYear=date("Y")+2;
    $y= '<select id="booking_year" name="booking_year" >';
        for ($i=$startYear;$i<=$endYear;$i++){
          $s='';
          if($i==$c_year){
            $s="selected='selected'";
          }          
          $y.= "<option value=".$i." ".$s.">".$i."</option>";          
        } 
    $y.= "</select>";  
		$output .='<div style="width:100%;">
			<form id="frmgetbooking" method="post" name="frmgetbooking" action="'.str_replace( '%7E', '~', $_SERVER['REQUEST_URI']).'" />
			<table id="calendarhead" style="width:100%!important;">
				<tr>
					<td style="max-width:168px;">
							<input type="hidden" name="booking_day" id="booking_day" value="1" />
							<select id="booking_month" name="booking_month" >
                  <option value="1" '.$selectedm1.'>'.__("January","sc-scbooking").'</option>
									<option value="2" '.$selectedm2.'>'.__("February","sc-scbooking").'</option>
									<option value="3" '.$selectedm3.'>'.__("March","sc-scbooking").'</option>
									<option value="4" '.$selectedm4.'>'.__("April","sc-scbooking").'</option>
									<option value="5" '.$selectedm5.'>'.__("May","sc-scbooking").'</option>
									<option value="6" '.$selectedm6.'>'.__("June","sc-scbooking").'</option>
									<option value="7" '.$selectedm7.'>'.__("July","sc-scbooking").'</option>
									<option value="8" '.$selectedm8.'>'.__("August","sc-scbooking").'</option>
									<option value="9" '.$selectedm9.'>'.__("September","sc-scbooking").'</option>
									<option value="10" '.$selectedm10.'>'.__("October","sc-scbooking").'</option>
									<option value="11" '.$selectedm11.'>'.__("November","sc-scbooking").'</option>
									<option value="12" '.$selectedm12.'>'.__("December","sc-scbooking").'</option>
							</select>
              '.$y.'
					   <input type="submit" id="btnbookings" name="btnbookings" value="" />
            </td>					
					
					<td id="showmonthyear" style="padding-left:10px;font-size:12px; display:none;">
             
                  
              <div style="float:left;">
                <div style="float:left;width:25px;height:26px;padding-top: 5px;text-align:center; background-color:#'.$processing_bg_color.'">
                    '.__("P","sc-scbooking").'
                </div>
                <div style="float:left; padding: 5px;">'.__("Processing","sc-scbooking").' </div>&emsp;
              </div>  
              <div style="float:left;">
                <div style="float:left;width:25px;height:26px;padding-top: 5px; text-align:center;background-color:#'.$booked_bg_color.'">
                  '.__("B","sc-scbooking").'
                </div>
                <div style="float:left; padding: 5px"> '.__("Booked","sc-scbooking").'</div>
              </div>
          </td>
					
				</tr>
			</table>
			<input type="hidden" id="txthdnmonth" name="txthdnmonth" value="" />
		 </form> 
		</div>';
			global $table_prefix,$wpdb;
			$output .= '<style type="text/css">
				#calendar_fronttable tr td{
					/*border:solid 1px #E2E2E2;*/
					height: 30px;
					width:30px;
					padding:0;
          vertical-align: middle!important;
				}
        table#calendar_front tr td{
          border-right: 1px solid #dddeee!important;
          vertical-align:middle;
        } 
				#calendar_front table{
					font-size:11px;
          line-height:1.5em;
          table-layout: auto!important;
				}
        body{
          line-height: 1.1em!important;
        }
        #calendar_front{
          border: 1px solid #cccccc;
        }
				</style>';      
				
				$monthNames = Array("January", "February", "March", "April", "May", "June", "July","August", "September", "October", "November", "December");
				$weekNames = Array("SUN", "MON", "TUE", "WED", "THU", "FRI", "SAT");
								
				$next_day = getDate(mktime(0,0,0,$cMonth,$cDay+1,$cYear));
				$prev_day = getDate(mktime(0,0,0,$cMonth,$cDay-1,$cYear));
				 
				$next_month = getDate(mktime(0,0,0,$cMonth+1,$cDay,$cYear));
				$prev_month = getDate(mktime(0,0,0,$cMonth-1,$cDay,$cYear));
				 
				$next_year = getDate(mktime(0,0,0,$cMonth,$cDay,$cYear+1));
				$prev_year = getDate(mktime(0,0,0,$cMonth,$cDay,$cYear-1));
				
				$timestamp = mktime(0,0,0,$cMonth,1,$cYear);
				$maxday = date("t",$timestamp);          
				$thismonth = getdate ($timestamp);
				$startday = $thismonth['wday'];
				
				$cMonthouter = $cMonth;
				if($cMonth<10){
					$cMonthouter = "0".$cMonth;
				}
				$fromdate = esc_sql($cYear.'-'.$cMonthouter.'-'.$cDay);
				$todate = esc_sql($cYear.'-'.$cMonthouter.'-'.$maxday);
				
				$sqlmonth =  "select * from ".$table_prefix."sc_scbooking where  from_date>='".$fromdate."' and to_date<='".$todate."' and order_status != '0' ";
				$month_result = $wpdb->get_results($sqlmonth);
				
        
				function sc_searchForDay($day, $array) {
					 foreach ($array as $key => $val) {
							 if ($val['day'] === $day) {
									 //return $key;
									 return $val;
							 }
					 }
					 return null;
				}
        
				function sc_searchForDue($day, $array) {
					 foreach ($array as $key => $val) {
							 if ($val['dueDay'] === $day) {
									 //return $key;
									 return $val;
							 }
					 }
					 return null;
				}
        
				function sc_searchForBookingID($day, $array) {
					 foreach ($array as $key => $val) {
							 if ($val['day'] === $day) {
									 //return $key;
									 return $val['booking_id'];
							 }
					 }
					 return null;
				}
        
				function sc_searchForBookingRange($day, $array) {
					 foreach ($array as $key => $val) {
							 if ($val['day'] === $day) {
									 //return $key;
									 return $val['booking_range'];
							 }
					 }
					 return null;
				}
        
				function sc_COTTAGE($MAX,$room,$fromdate,$todate,$cYear,$cMonth){
						global $table_prefix,$wpdb,$export_data, $nl, $separator;
						/* PROCESSING BG COLOR */
						$processing_bg_color = sc_scbooking_get_opt_val_for_calendar('_processing_bg_color',CCB_PROCESSING_BG_COLOR); 
						/* BOOKED BG COLOR */
						$booked_bg_color = sc_scbooking_get_opt_val_for_calendar('_booked_bg_color',CCB_BOOKED_BG_COLOR); 
						if($cMonth <10){
							$cMonth = "0".$cMonth;
						}
						
						$fromdate_for_query = esc_sql($cYear.'-'.$cMonth.'-%');
						$todate_for_query = esc_sql($cYear.'-'.$cMonth.'-%');
						
						$fromdate_begin = esc_sql($cYear.'-'.$cMonth.'-01');
						$todate_end = esc_sql($cYear.'-'.$cMonth.'-'.$MAX);
						
						$sqlmonth =  "select * from ".$table_prefix."sc_scbooking where  (from_date like '".$fromdate_for_query."' or to_date like '".$todate_for_query."' or (from_date < '".$fromdate_begin."' and to_date > '".$todate_end."')) and room like '%".addslashes($room->post_title)."%' and order_status != '0' ";

						$month_room_booking_result = $wpdb->get_results($sqlmonth);
						
						$room_month_booked_days_2d = array();
						$room_month_booked_days = array();
						
						$room_month_booking_due_2d = array();
						$room_month_booking_due = array();
						
						$count = 0;						
						$count_due = 0;
            
						foreach($month_room_booking_result as $mrr){
								$fromdate_timestmp = strtotime("".$mrr->from_date."");
								$fromday = date("j",$fromdate_timestmp);
								$fromday = ($fromday - 1);		
								$todate_timestmp = strtotime($mrr->to_date);
								$today = date("j",$todate_timestmp);
								
								$fromdate_month = date("n",$fromdate_timestmp);
								$todate_month = date("n",$todate_timestmp);
								$bookingrange = 0;
								if($fromdate_month == $cMonth && $todate_month == $cMonth){
										$bookingrange = ($today - $fromday);
								}
								elseif($fromdate_month == $cMonth && $todate_month != $cMonth){
										$bookingrange = ($MAX - $fromday);
								}
								elseif($fromdate_month != $cMonth && $todate_month == $cMonth){
										$bookingrange = $today;
										$fromday = 0;
								}
								elseif($fromdate_month != $cMonth && $todate_month != $cMonth){
										$bookingrange = $MAX;	
										$fromday = 0;
								}
								
								for($j=0;$j<$bookingrange;$j++){
										$fromday++;
										$room_month_booked_days['day'] = $fromday;
										$room_month_booked_days['booking_id'] = $mrr->booking_id;
										$room_month_booked_days['booking_range'] = $bookingrange;
										//
										$room_month_booked_days['first_name'] = $mrr->first_name;
										$room_month_booked_days['last_name'] = $mrr->last_name;
										$room_month_booked_days['email'] = $mrr->email;
										$room_month_booked_days['phone'] = $mrr->phone;
										$room_month_booked_days['booking_by'] = $mrr->booking_by;
										$room_month_booked_days['price'] = $mrr->custom_price;
										$room_month_booked_days['payment_method'] = $mrr->payment_method;
										$room_month_booked_days['tracking_no'] = $mrr->tracking_no;
										//
										$room_month_booked_days_2d[$count] = 	$room_month_booked_days;
										$count++;	                    
																			
                    if($mrr->order_status == 1){
											//----------new-----------
											$room_month_booking_due['dueDay'] = $fromday;
											$room_month_booking_due['booking_id'] = $mrr->booking_id;
											//----------new-----------
											$room_month_booking_due_2d[$count_due] = $room_month_booking_due;
											$count_due++;
										}									
								}
						}
            
            
						$shtml = '';
						for ($i=1; $i<$MAX+1; $i++) {
							$day = sc_searchForDay($i,$room_month_booked_days_2d);
							$due = sc_searchForDue($i,$room_month_booking_due_2d);
							if ( ($day!=NULL || $day!="") && ($due!=NULL || $due !="")){
								$booking_id = sc_searchForBookingID($i,$room_month_booked_days_2d);
								//---------colspan-----------
								$colspan = sc_searchForBookingRange($i,$room_month_booked_days_2d);
								$val = sc_searchForDay($i,$room_month_booked_days_2d);
								$i = $i + ($colspan-1);								
                $shtml .= '<td style="color:black;background-color:#'.$processing_bg_color.';text-align:center;" colspan="'.$colspan.'"><a onclick="sc_open_edit_popup('.$booking_id.')" style="cursor:pointer;text-decoration:none;"><span class="tooltip">'.__("P","sc-scbooking").'</span></td>';                
							}else if(($day!=NULL || $day!="")){
								$colspan = sc_searchForBookingRange($i,$room_month_booked_days_2d);
								$val = sc_searchForDay($i,$room_month_booked_days_2d);
								$i = $i + ($colspan-1);
                
                $shtml .= '<td style="color:black;background-color:#'.$booked_bg_color.';text-align:center;" colspan="'.$colspan.'"><span class="tooltip">'.__("B","sc-scbooking").'</span></td>';
                
                
							}else{
								if($i<10){
									$daypass = '0'.$i;
								}
								else{
									$daypass = $i;
								}
								if($cMonth<10){
									$cMonthpass = "0".$cMonth;
								}
								else{
									$cMonthpass = $cMonth;
								}
								$current_cell_date = $cYear.'-'.$cMonth.'-'.$daypass;
								$current_date = date("Y-m-d");
								if($current_cell_date < $current_date){
									$shtml .= '<td style="background-color:#F4F4F4;text-align:center;"><img src="'.SCBOOKING_PLUGIN_URL.'/images/add.jpg"></img></td> ';
								}
								else{
									$shtml .= '<td style="background-color:#F4F4F4;text-align:center;"><a onclick="sc_openpopup('.$room->term_id.','.$room->ID.',\''.$current_cell_date.'\');"  id="opener" style="cursor:pointer;" ><img src="'.SCBOOKING_PLUGIN_URL.'/images/add.jpg"></img></a></td> ';
								}
							}
						}
						return $shtml;
				}
				//=============================
				$output .= '<div style=""><table id="calendar_front" cellspacing="0" cellpadding="0"><tr style="font-size:12px;background-color:#0099FF;color: #ffffff; font-family: arial;"><td width="100px"><div style="font-weight: bold;">'.__("ROOMS","sc-scbooking").'</div></td>';
				for ($i=1; $i<$maxday+1; $i++) {
					$time = mktime(0,0,0,$cMonth,$i,$cYear);
					$cc_date = date('m-d-Y', $time);
					$t=date('d-m-Y',$time);
					$cc_day = date("D",strtotime($t));
          $cc_day =substr($cc_day, 0, 1);          
          
					$output .= '<td valign="top" height="20px"><b>'. sprintf(__("%s","sc-scbooking"),$cc_day) . '<div style="font-size:12px">' . sprintf(__("%s","sc-scbooking"),$i)  .'</div></b></td>';
				}
				$output .= '</tr>';
				foreach($taxonomies as $taxo){
					$flag = 0;
					$term_id = esc_sql($taxo->term_id);
					$sql_rooms = "select * from ".$table_prefix."term_taxonomy tt inner join ".$table_prefix."term_relationships tr on tt.term_taxonomy_id = tr.term_taxonomy_id inner join ".$table_prefix."posts p on p.id = tr.object_id inner join ".$table_prefix."postmeta pm on pm.post_id = p.id where p.post_status = 'publish' and p.post_type='sc_custom_booking' and tt.term_id=".$term_id." and pm.meta_key = '_room_price' order by pm.post_id ASC";
					$rooms = $wpdb->get_results($sql_rooms);
					foreach($rooms as $room){
						if($flag == 0){
							 $output .= '<tr><td style="background-color:#E4E4E4;color:#26A4CE;border-bottom:1px solid #d2d2d2;"><div style="color:#464646; font-weight:bold; margin-left:0px; font-size:12px;">'.sprintf(__("%s","sc-scbooking"),$taxo->name).'</div><b><a style="text-decoration:none;font-size:12px;" class="room_listing_name" href="'.get_option('siteurl').'/?page_id='.ROOMDETAILS_PAGEID.'&roomid='.$room->post_id.'">'.sprintf(__("%s","sc-scbooking"),$room->post_title).'</a></b></td>' . sc_COTTAGE($maxday,$room,$fromdate,$todate,$cYear,$cMonth) . '</tr>';
						}else{
							$output .= '<tr><td style="background-color:#E4E4E4;color:#26A4CE;border-bottom:1px solid #d2d2d2;"><b><a style="text-decoration:none;font-size:12px;" class="room_listing_name" href="'.get_option('siteurl').'/?page_id='.ROOMDETAILS_PAGEID.'&roomid='.$room->post_id.'">'.sprintf(__("%s","sc-scbooking"),$room->post_title).'</a></b></td>' . sc_COTTAGE($maxday,$room,$fromdate,$todate,$cYear,$cMonth) . '</tr>';
						}
						$flag++;
					}
				}	
			 $output .= '</table></div>'; 		
		return $output;
	}
	add_shortcode('sc_sccalendar','sc_sccalendar_shortcode');
	?>