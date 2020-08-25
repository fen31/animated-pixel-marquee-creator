<?php
namespace apmc\install\crearcaracteres;
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
function crearcaracteres(){

	require_once(APMCPLUGIN_DIR.'/install/caracteresiniciales.php');
	global $wpdb;


	$charset_collate = $wpdb->get_charset_collate();
	$nombre_tabla = "{$wpdb->prefix}apmc_caracteres";


	$sql = "CREATE TABLE IF NOT EXISTS {$nombre_tabla}(
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
 	 Data text NOT NULL,
	  PRIMARY KEY  (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	dbDelta($sql);
/*
  function validateData($Data){
    $aux = false;
    if(isset($Data) && !empty($Data) && is_array($Data)){
        $aux = true;
        foreach($Data as $char){
          if(is_numeric($char) && strlen($char)==105){

          }else{ $aux = false;}

        }
    }
    return $aux;
  }
*/
  $sql = "SELECT id FROM {$nombre_tabla}";
  $is_there = $wpdb->get_results($sql,ARRAY_A);
  if(!count($is_there) > 0){
    if(\apmc\ajax\validateData(\apmc\install\caracteresiniciales\caracteresiniciales())){
		    $chars = sanitize_text_field(json_encode(\apmc\install\caracteresiniciales\caracteresiniciales()));
    }else{die("File 'caracteresiniciales.php' is corrupted");}
		$wpdb->insert(
		$nombre_tabla,array(
			'Data'=>$chars,
		));
  }

}
?>
