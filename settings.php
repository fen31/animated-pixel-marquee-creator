<?php
namespace apmc\settings;
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
function main_settings(){



  add_settings_section('apmcmain_ajustes','Main settings',"apmc\settings\main_ajustes",'Ajustes');
//,array('label_for'=>'Maquees por página','class'=>"")
  add_settings_field(
    'apmcmarqueesperpage','Marquees per page','apmc\settings\marqueesperpage','Ajustes','apmcmain_ajustes');
    //'wpm_mpp'
  register_setting('Ajustes','apmcmarqueesperpage','absint');

  }



function main_ajustes(){
  echo "<div style='border-bottom:1px solid blue'></div>";
}

function marqueesperpage(){
  $setting = get_option('apmcmarqueesperpage');
  ?>
<input type="number"  min="5" max="99" style="width:60px" name="apmcmarqueesperpage" value="<?php echo !empty( $setting ) ? esc_attr( $setting) : '5'; ?>">
<?php
}

function settings_html(){
  if ( ! current_user_can( 'manage_options' ) ) {
    return;
  }
  if ( isset( $_GET['settings-updated'] ) ) {
 // add settings saved message with the class of "updated"
    //add_settings_error( 'wporg_messages', 'wporg_message', __( 'Settings Saved', 'wporg' ), 'updated' );
  }
  settings_errors( 'wporg_messages' );
  ?>
 <div class="wrap">
 <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
 <form action="options.php" method="post">
 <?php
 // output security fields for the registered setting "wporg"
 settings_fields( 'Ajustes' );
 // output setting sections and their fields
 // (sections are registered for "wporg", each field is registered to a specific section)
 do_settings_sections( 'Ajustes' );
 // output save settings button
 submit_button( 'Save Settings' );
 ?>
 </form>
 </div>
 <?php
}


 ?>
