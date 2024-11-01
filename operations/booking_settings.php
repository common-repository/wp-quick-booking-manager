<script type="text/javascript">
</script>
<style type="text/css">
 .stuffbox{
 	width:45%;
	min-height:187px;
	clear:both;
	padding:10px;
 }
</style>
<?php
if (!defined('ABSPATH')) exit;
define('CCB_PROCESSING_BG_COLOR','7FCA27') ;
define('CCB_BOOKED_BG_COLOR','138219') ;
$options = array (
								 '_processing_bg_color',
								 '_booked_bg_color'
								 );
if(isset($_REQUEST['reset'])){
	if($_REQUEST['reset']){
		foreach($options as $opt){
			delete_option ($opt);
			$_POST[$opt]='';
			add_option( $opt, sanitize_text_field($_POST[$opt]) );	
		}
	}
}								 
if ( count($_POST) > 0 && isset($_POST['savesettings']) ){
	foreach($options as $opt ){
			delete_option ( $opt, sanitize_text_field($_POST[$opt]) );
			add_option ( $opt, sanitize_text_field($_POST[$opt]) );
	}	
}

/* PROCESSING BG COLOR */
$processing_bg_color = sc_scbooking_get_opt_val('_processing_bg_color',CCB_PROCESSING_BG_COLOR); 
/* BOOKED BG COLOR */
$booked_bg_color = sc_scbooking_get_opt_val('_booked_bg_color',CCB_BOOKED_BG_COLOR); 

?>

<div>
  <!--<div id="icon-link-manager" class="icon32"></div>-->
  <div>
    <div style="float:left; padding-top:9px;" class="wp-menu-image dashicons-before dashicons-admin-generic"></div>
    
  </div>
  <div id="namediv" class="stuffbox">
		<h3 class="top_bar" style="padding:8px;"><?php _e("Booking Settings","sc-scbooking"); ?></h3>
    	
      <form id="frmbookingsettings" action="" method="post">
      	<table>
        	<tr>
          	<td><?php _e("Processing Background:","sc-scbooking"); ?> </td>
            <td><input class="color" type="text" name="_processing_bg_color" id="_processing_bg_color" value="<?php echo $processing_bg_color;?>" /></td>
          </tr>
          <tr>
          	<td><?php _e("Booked Background:","sc-scbooking"); ?> </td>
            <td><input class="color" type="text" name="_booked_bg_color" id="_booked_bg_color" value="<?php echo $booked_bg_color;?>" /></td>
          </tr>
          <tr>
          	<td></td>
            <td></td>
          </tr>
        </table>
        <div style="float:left;margin-top:13px;padding-left:4px;">
        	<input type="submit" name="savesettings" class="button-primary" style="min-width:100px;height:29px" value="<?php _e("Save Changes","sc-scbooking"); ?>" />
        </div>
      </form>
      </div>
      <div style="float:left;margin-top:-60px;margin-left:120px;">
      <form method="post" action="" >
      	<input type="submit" name="reset" class="button-primary" style="min-width:108px;height:29px;" value="<?php _e("Default Settings","sc-scbooking"); ?>" />
      </form>
      </div>
	</div>
  
<div style="clear:both;"></div>