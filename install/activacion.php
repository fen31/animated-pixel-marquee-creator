<?php
namespace apmc\install\activacion;
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
function activacion(){
  flush_rewrite_rules();
	global $wpdb;

	//TODOS LOS MARQUEES
	$charset_collate = $wpdb->get_charset_collate();
	$nombre_tabla = "{$wpdb->prefix}apmc_marquees";


	$sql = "CREATE TABLE IF NOT EXISTS {$nombre_tabla}(
	  id int NOT NULL AUTO_INCREMENT,
 	 Nombre tinytext NOT NULL,
	  PRIMARY KEY  (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	dbDelta($sql);

	/////MARCADORES
  $nombre_tabla = "{$wpdb->prefix}apmc_marcadores";
	$sql = "CREATE TABLE IF NOT EXISTS {$nombre_tabla}(
		Code smallint NOT NULL
	)$charset_collate;";
	dbDelta($sql);


	//AÑADIMOS LA VERSION DEL PLUGIN
	update_option("apmc_version", "1.0");

	//AÑADIMOS CARACTERES A LA BASE DE DATOS
	require_once(APMCPLUGIN_DIR.'/install/crearcaracteres.php');

	\apmc\install\crearcaracteres\crearcaracteres();
}

function desactivacion(){
//Vaciar caché y enlaces permanentes
  flush_rewrite_rules();
	remove_menu_page("slm_tablelist");
  remove_menu_page( "slm_options" );
}




?>
