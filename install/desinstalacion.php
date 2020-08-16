<?php
namespace apmc\install\desinstalacion;
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

function desinstalacion(){

	global $wpdb;
   	$nombre_tabla = "{$wpdb->prefix}apmc_marquees";

	$sql = "SELECT * FROM {$nombre_tabla}";
	$result = $wpdb->get_results( $sql,ARRAY_A);
	for($i=0;$i<count($result);$i++){
		$aux = $result[$i]["id"];//escapar!!!!!
		delete_option("apmc_marquee_{$aux}");

	}
	$sql = "DROP TABLE IF EXISTS {$nombre_tabla}";

	$wpdb->query($sql);
	$nombre_tabla = "{$wpdb->prefix}apmc_caracteres";
	$sql = "DROP TABLE IF EXISTS {$nombre_tabla}";

	$wpdb->query($sql);
	$nombre_tabla = "{$wpdb->prefix}apmc_marcadores";
	$sql = "DROP TABLE IF EXISTS {$nombre_tabla}";
	$wpdb->query($sql);
	 delete_option("apmc_version");
   delete_option("apmcmarqueesperpage");

}
?>
