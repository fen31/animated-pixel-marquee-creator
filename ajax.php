<?php
namespace apmc\ajax;
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
function guardar(){
  if(wp_verify_nonce($_POST['nonce'],'apmcajax_guardar')){
        if(validateData($_POST['Data'])){
        $chars= sanitize_text_field(json_encode($_POST['Data']));
        global $wpdb;
        $wpdb->update("{$wpdb->prefix}apmc_caracteres",array('Data'=>$chars),array('id'=>1));
      }else{
        echo "0";
      }
  }
    die();

}
function marcar(){

  if(wp_verify_nonce($_POST['nonce'],'apmcajax_marcadores')){
    if(validateCode()){
      $Code = absint($_POST['Code']);
          global $wpdb;
          $nombre_tabla = "{$wpdb->prefix}apmc_marcadores";
          $sql = "SELECT * FROM {$nombre_tabla} WHERE Code = {$Code}";
          $results = $wpdb->get_results($sql,ARRAY_A);
          if(!count($results)>0){
            $wpdb->insert("{$wpdb->prefix}apmc_marcadores",array('Code'=>$Code));
            echo "1";
          }else{
            $wpdb->delete("{$wpdb->prefix}apmc_marcadores",array('Code'=>$Code));
            echo "0";
          }
      }else{
        echo "2";
      }
    }
    die();
}
function restablecer(){
  if(validateCode($_POST['Code'])){
    $Code = absint($_POST['Code']);
    global $wpdb;
    $nombre_tabla = "{$wpdb->prefix}apmc_caracteres";
		$result = $wpdb->get_results("SELECT Data FROM {$nombre_tabla}",ARRAY_A);
    $chars = json_decode($result[0]['Data']);

    $res = $chars[$Code - 32];

    echo json_encode(array('data'=>$res));
  }else{echo "2";}
    die();

}
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
function validateCode(){
  $aux = false;
  if(isset($_POST['Code']) && !empty($_POST['Code']) && is_numeric($_POST['Code'])){
    $aux=true;
    if(absint($_POST['Code'])>255 || absint($_POST['Code'])<32 )$aux = false;
  }
  return $aux;

}
?>
