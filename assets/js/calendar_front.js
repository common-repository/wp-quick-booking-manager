jQuery(".tooltip").bt({
  contentSelector: "jQuery(this).attr('title')",
  shrinkToFit: true,
  padding: 10,
  fill:'#EAECF0',
  cornerRadius: 10,
  positions: ['right', 'left',  'bottom']
  });
//_020
jQuery( "#addbooking_dialog" ).dialog({ 
    autoOpen: false,
    height: 500,
    width: 400,
    modal: true,
    buttons: {
      "Add Booking": function() {
          if(sc_save_booking()){
            jQuery(this).dialog("close");
            //jQuery("#frmgetbooking").submit();
            //alert("if--");
          }
          else{
            //jQuery(this).dialog("close");
            //jQuery("#frmgetbooking").submit();
          }
      },
      Cancel: function() {
        jQuery( this ).dialog( "close" );
        sc_cleardata();
      }
    },
    close: function() {
      sc_cleardata();
    } 
});
function sc_openpopup(cat_id,room_id,from_date){
	//_020
	
  jQuery( "#addbooking_dialog" ).dialog( "open" );
  //jQuery("#rooms_multiselect").multiselect("select",room_id);
  //jQuery("#rooms_multiselect").multipleSelect("setSelects", room_id);
  sc_get_roomprice();
  jQuery("#dtpfromdate").val(from_date);
  //jQuery("#dtptodate").val(from_date);
  //--------
  var date2 = jQuery('#dtpfromdate').datepicker('getDate');
	date2.setDate(date2.getDate()+1);
	jQuery('#dtptodate').datepicker('setDate', date2); 
}

function sc_open_edit_popup(booking_id){
  jQuery( "#addbooking_dialog" ).dialog( "open" );
  sc_setbooking_info(booking_id);
}