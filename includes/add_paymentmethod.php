<?php
if (!defined('ABSPATH')) exit;
?>
<script type="text/javascript">
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
  jQuery(document).ready(function(){
      var calltype = sc_getUrlVars()["calltype"];
			if(calltype){
				if(calltype == 'editpaymentmethod'){
          <?php 
          if(isset($_REQUEST['id'])){
            $id = esc_sql($_REQUEST['id']);	
            global $table_prefix,$wpdb;
            $sql = "select * from ".$table_prefix."sc_scbooking_paymentmethods where id=".$id;
            $result = $wpdb->get_results($sql);
          ?>
          var paymentmethod = <?php echo json_encode($result[0]);?>;
          jQuery('#hdnpaymentmethodid').val(paymentmethod['id']);
          jQuery('#paymentmethod_name').val(paymentmethod['payment_method']);
          <?php }?>        
        }
      }
      jQuery('#frmpaymentmethod').on('submit',function(e){
	  		 e.preventDefault();
				 sc_save_paymentmethod();
			});
  });
  function sc_save_paymentmethod(){
    var hdnpaymentmethodid = jQuery('#hdnpaymentmethodid').val(); 
    var payment_method = jQuery('#paymentmethod_name').val();
    if(payment_method==""){
      alert('Please input Payment Method name.');
      return;
    }
    jQuery.ajax({
        type: "POST",
        url: '<?php echo admin_url( 'admin-ajax.php' );?>',
        data: {
          action:'sc_save_paymentmethod',
          hdnpaymentmethodid: hdnpaymentmethodid,paymentmethod:payment_method 
        },
        success: function (data) {
            if(data.length>0){
			  if(hdnpaymentmethodid!=''){
			    alert('<?php _e("Updated successfully","sc-scbooking"); ?>');
			  }else{
			    alert('<?php _e("added successfully","sc-scbooking"); ?>');
			  }
              window.location.href = "<?php echo get_option('siteurl'); ?>/wp-admin/edit.php?post_type=sc_custom_booking&page=manage-payment-method-menu";
              //jQuery('#paymentmethod_name').val('');
            }
        },
        error : function(s , i , error){
            console.log(error);
        }
    });
  }
</script>  
<div id="addbooking_backend" class="wrapper" style="clear:both;">
  <div class="wrap" style="float:left; width:95%;">
    <div>      
      <div style="float:left;padding-left:3px;"><h2><?php _e("Booking Manager","sc-scbooking"); ?></h2></div>
    </div>
    <div class="main_div" style="clear:both;">
       <div class="metabox-holder" style="width:69%; float:left;">
        <div id="namediv" class="stuffbox" style="width:95%;">
            <h3 class="top_bar">
			   <?php
			      if(isset($_REQUEST['id'])){
				    _e("Update Payment Method","sc-scbooking"); 
				  }else{
				    _e("Add Payment Method","sc-scbooking"); 
				  }
			   ?>            
            </h3>
            <form id="frmpaymentmethod" action="" method="post" novalidate="novalidate">
              <table style="margin:10px;width:95%;">
                <tr>
                  <td><?php _e("Payment Method","sc-scbooking"); ?></td>
                  <td><input type="text" name="paymentmethod_name" id="paymentmethod_name" value="" /><span style="color:red;">*</span></td>
                </tr>
                <tr><td colspan="2"></td></tr>
                <tr>
                    <td></td>
                  <td>
                    <input type="submit" id="btnaddpaymentmethod" name="btnaddpaymentmethod" class="button button-primary" value="<?php _e("Save","sc-scbooking"); ?>" style="width:100px;cursor: pointer;"/>
                    <input type="hidden" id="hdnpaymentmethodid" name="hdnpaymentmethodid" value="" style="width:150px;"/>
                  </td>
                </tr>
              </table>
            </form>
        </div>
       </div>
    </div>
   </div>
  </div>