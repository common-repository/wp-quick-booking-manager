	jQuery(document).ready(function(){
		jQuery('.entry-content').delegate(".clsdel","click",function(e){
        //alert('hhhh');
				e.preventDefault();	
				var indx = jQuery(this).attr('id');
				var thiselement = jQuery(this);
				var grand_total = jQuery('#grandtotal').html();
				//alert('G.T='+grand_total);
				var price = jQuery(this).parent().parent().children('#price').html();
				//alert('price='+price);
				grand_total = (grand_total - price);
				//var ajax_url = '<?php echo admin_url( 'admin-ajax.php' ); ?>';	
				//alert(indx);
				jQuery.ajax({
						type: "POST",
						url: scCartAjax.ajaxurl,
						data: {
							action: 'sc_cart_session_ajax_request',	
							indx: indx
						},
						success: function (data) {
								if(data.length>0){
									//console.log(thiselement.parent().html());
									
									thiselement.parent().parent().remove()
									jQuery('#grandtotal').html(grand_total);
									//console.log('data:'+data);
									
									//jQuery('#frmshoppingcart').submit();
								}
						},
						error : function(errorThrown){
								console.log(error);
						}	
				});
			}); 
});
