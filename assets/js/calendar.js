jQuery( "#addbooking_backend_popup" ).dialog({
    autoOpen: false,
    height: 570,
    width: 540,
    modal: true,
    buttons: {
      "Add Booking": function() {
          if(sc_save_booking()){
            jQuery(this).dialog("close");
          }
          else{
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
jQuery(".tooltip").bt({
  contentSelector: "jQuery(this).attr('title')",
  shrinkToFit: true,
  padding: 10,
  fill:'#EAECF0',
  cornerRadius: 10,
  positions: ['right', 'left',  'bottom']
});
function sc_openpopup(cat_id,room_id,from_date){
  jQuery( "#addbooking_backend_popup" ).dialog( "open" );
  jQuery("#rooms_multiselect").multipleSelect('setSelects', room_id);
  sc_get_roomprice();
  jQuery("#dtpfromdate").val(from_date);
  jQuery("#dtptodate").val(from_date);
}
function sc_open_edit_popup(booking_id){
               jQuery( "#addbooking_backend_popup" ).dialog( "open" );
               sc_setbooking_info(booking_id);
            }
