<?php
if (!defined('ABSPATH')) exit;
function scpd_admin_setting(){
	add_submenu_page('edit.php?post_type=sc_custom_booking', 'Products Settings', ''.__("Settings","sc-scbooking").'', 'edit_posts', 'scpd_settings', 'scpd_global_settings');
}

function scpd_cust_admin_tabs( $current = 'general' ) { 
	$tabs = array( 'general' => ''.__("General","sc-scbooking").''); 
  $links = array();
  echo '<div id="icon-themes" class="icon32"><br></div>';
  echo '<h2 class="nav-tab-wrapper">';
  foreach( $tabs as $tab => $name ){
    $class = ( $tab == $current ) ? ' nav-tab-active' : '';
    echo "<a class='nav-tab$class' href='admin.php?page=scpd_settings&tab=$tab'>".sprintf(__('%s','sc-scbooking'),$name)."</a>";
  }
  echo '</h2>';
}

function scpd_global_settings(){
  global $table_prefix,$wpdb;
  if(!get_option('scpd_base_currency')){
    update_option('scpd_base_currency', 136);        
  }  
  ?>
  <div class="wrap">
    <div>
      <div style="float:left; padding:6px 3px 0 0;" class="wp-menu-image dashicons-before dashicons-admin-settings"></div>
      <div style="float:left;"><h2><?php _e("Settings","sc-scbooking"); ?></h2></div>
    </div>
    <div style="clear:both;"></div>
    <?php if ( isset ( $_GET['tab'] ) ) scpd_cust_admin_tabs($_GET['tab']); else scpd_cust_admin_tabs('general'); ?>
    <div id="poststuff" >
			<form method="post" action="">
				<?php				
					if ( isset ( $_GET['tab'] ) ) $tab = $_GET['tab']; 
					else $tab = 'general'; 
					
          echo '<div class="scpd_settings">';
					switch ( $tab ){
						case 'general' :
              if(isset($_POST['scpd_general_settings'])){
                if($_POST['scpd_general_settings']){                  
                  update_option('scpd_base_country', sanitize_text_field($_POST['scpd_country']));
                  update_option('scpd_base_currency', sanitize_text_field($_POST['scpd_currency']));
                  update_option('scpd_admin_email', sanitize_email($_POST['scpd_admin_email']));
                  update_option('scpd_base_tax', sanitize_text_field($_POST['scpd_tax']));
                  update_option('scpd_design_help_text', sanitize_text_field($_POST['scpd_design_help_text']));
                }
             }
							$country_data = scpd_currency_table_query();
              ?>
              <h2><?php _e("General","sc-scbooking"); ?></h2>
              <table class="form-table">
                <tr>
                  <th scope="row"><?php _e("Country","sc-scbooking"); ?></th>
                  <td>
                    <select name="scpd_country">
                      <option value=""><?php _e("Select","sc-scbooking"); ?></option>
                      <?php
                      foreach($country_data as $country){
                        echo '<option value="'.$country->isocode.'" '.((get_option('scpd_base_country')==$country->isocode) ? 'selected="selected"':'').' >'.$country->country.'</option>';
                      }
                      ?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <th scope="row"><?php _e("Administrator email","sc-scbooking"); ?></th>
                  <td><input type="text" name="scpd_admin_email" class="regular-text" value="<?php printf(__("%s","sc-scbooking"), get_option('scpd_admin_email'));?>" /></td>
                </tr>
                <tr>
                  <th scope="row"><?php _e("Tax","sc-scbooking"); ?></th>
                  <td>
                    <input type="text" name="scpd_tax" class="regular-text" value="<?php printf(__("%s","sc-scbooking"), get_option('scpd_base_tax')) ;?>" /> %
                  </td>
                </tr>
                <tr>
                  <th scope="row"><?php _e("Currency","sc-scbooking"); ?></th>
                  <td>
                    <select name="scpd_currency" class="regular-text">
<!--                      <option value=""><?php //_e("Select","sc-scbooking"); ?></option>-->
                      <?php
                      foreach($country_data as $currency){
                        /*$currency_code=$currency->symbol_html;
                        if($country->symbol_html==''){
                          $currency_code=$currency->code;
                        }*/
                        echo '<option value="'.$currency->id.'" '.((get_option('scpd_base_currency')==$currency->id) ? 'selected="selected"':'').' >'.$currency->country.' ('.$currency->code.')</option>';
                      }
                      ?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <th scope="row"><?php _e("Design Panel Help Text","sc-scbooking"); ?></th>
                  <td><textarea  name="scpd_design_help_text" rows="5" cols="38"><?php printf(__("%s","sc-scbooking"), get_option('scpd_design_help_text'));?></textarea></td>
                </tr>
              </table>
              <input type="hidden" name="scpd_general_settings" value="yes" />
							<?php
						break;					
					}
          echo '</div>';
				
				?>
				<p class="submit" style="clear: both;">
					<input type="submit" name="Submit"  class="button-primary" value="<?php _e("Update Settings","sc-scbooking"); ?>" />
				</p>
			</form>
			
		</div>
  </div>
  <?php
}

add_action('admin_menu', 'scpd_admin_setting');

function scpd_currency_code($id){  
  global $table_prefix,$wpdb;
  $sql = "select * from ".$table_prefix."sc_currency_list where id=".$id."";  
  $currency=$wpdb->get_row($sql);
  $currency_code=$currency->code;
  if($country->symbol_html==''){
    $currency_code=$currency->symbol_html;
  }
  return $currency_code;
}
