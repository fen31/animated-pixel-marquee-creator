<?php
namespace apmc\admin\marqueecontroller;
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
function agregar_view_controller(){
global $wpdb;

$id = isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id']) ? absint($_REQUEST['id']) : 0;
$Nombre= "";
$texto ="";
$tilesize = "";
$velocidad = "1";
$color = '#FF0000';
$options = array();
$type ="";
function inside(){}

if(isset($_GET['action']) && !empty($_GET['action'])){
  if($_GET['action'] =='ce' && isset($_GET['nonce']) && wp_verify_nonce($_GET['nonce'],'apmcmarquee_ce')){
    // CE: CREAR O ACTUALIZER MARQUEE
    $total = 0;
      $Nombre = isset($_POST['Nombre']) ? sanitize_text_field($_POST['Nombre']) : "";
      $texto = isset($_POST['texto']) ? sanitize_textarea_field($_POST['texto']) : "";

    if(!empty($Nombre) && strlen($Nombre)<=255){
      $options['Nombre'] = $Nombre;
      $total++;
    }else{echo "<font color='red'>invalid Name</font>";};
    if(!empty($texto) && strlen($texto) < 11000){
      $options['texto'] = $texto;
      $total++;
    }else{echo "<font color='red'>invalid Text</font>";};
    if(isset($_POST['tilesize']) && !empty($_POST['tilesize']) && is_numeric($_POST['tilesize']) && strlen($_POST['tilesize'])<3){
      $tilesize = absint($_POST['tilesize']);
      $options['tilesize'] = absint($tilesize);
      $total++;
    }else{echo "<font color='red'>invalid Tilesize</font>";};
    if(isset($_POST['velocidad']) && !empty($_POST['velocidad']) && is_numeric($_POST['velocidad']) && strlen($_POST['velocidad'])<2){
      $velocidad = absint(($_POST['velocidad']));
      $options['velocidad'] = $velocidad;
      $total++;
    }else{echo "<font color='red'>invalid Speed</font>";};
    if(isset($_POST['color']) && !empty($_POST['color']) ){
            //If it is not a hexcolor, it is # 000000
          $color =  $_POST['color']=="#000000" ? "#FF0000" : sanitize_hex_color(($_POST['color'])) ;

          $options['color'] = $color;
          $total++;
    }


    if($total  == 5){

      if($id>0){
        $wpdb->update("{$wpdb->prefix}apmc_marquees",array("Nombre"=>$Nombre),array('id'=>$id));
        $botones = \apmc\admin\marquee\botones($id);
        $type='ORANGE">This Marquee has been edited</h1>'.$botones;
      }else{
        $wpdb->insert("{$wpdb->prefix}apmc_marquees",array("Nombre"=>$Nombre));
        $id =$wpdb->insert_id;
        $botones = \apmc\admin\marquee\botones($id);
        $type='green">A new Marquee has been added</h1>'.$botones;
      }
      update_option("apmc_marquee_$id",$options);
    }else{
      $type='blue">Create a new Marquee</h1>';
      $id=0;
    }
    ///////////////////////////////////////////////
  }else if($_GET['action'] =='borrar' && isset($_GET['nonce']) && wp_verify_nonce($_GET['nonce'],'apmcmarquee_borrar')){
    //borrar:BORRAR MARQUEE
    $wpdb->delete( "{$wpdb->prefix}apmc_marquees",[ 'id' => $id],[ '%d' ] );
    delete_option("apmc_marquee_$id");
    $type='blue">Create a new Marquee</h1>';
    $id=0;
    //////////////////////////
  }else if($_GET['action'] =='editar'){
    //editar:EDITAR MARQUEE
    $options = get_option('apmc_marquee_'.$id);
    $Nombre = $options['Nombre'];
    $texto = $options['texto'];
    $tilesize = $options['tilesize'];
    $velocidad = $options['velocidad'];
    $color = $options['color'];
    $botones = \apmc\admin\marquee\botones($id);
    $type='orange">Edit</h1>'.$botones;
    /////////////////////////////////////////
  }
}else{

    $type='blue">Create a new Marquee</h1>';
    $id=0;
}


$formaction = add_query_arg([
  'page'=>'slm_agregar',
  'action'=>'ce',
  'nonce' =>wp_create_nonce('apmcmarquee_ce'),
  'id' => $id,
  ]);
$action = $formaction;
$shortcode ="[marquee id=$id link=]";

$data = array(
  'type'=>$type,
  'Nombre'=>$Nombre,
  'id'=>$id,
  'action'=>$action,
  'texto'=>$texto,
  'tilesize'=>$tilesize,
  'velocidad'=>$velocidad,
  'color'=>$color,
  'shortcode'=>$shortcode,

);
return $data;
}
function marcadores_data(){
  global $wpdb;
  //obtenemos los chars
  $nombre_tabla = "{$wpdb->prefix}apmc_caracteres;";
  $sql = "SELECT * FROM {$nombre_tabla}";
  $pre_caracteres = $wpdb->get_results($sql,ARRAY_A);
  $caracteres = json_decode($pre_caracteres[0]['Data']);
  //obtenemos $_tabla_marcadores
  $nombre_tabla = "{$wpdb->prefix}apmc_marcadores";
  $sql = "SELECT * FROM {$nombre_tabla}";
  $pre_marcadores = $wpdb->get_results($sql,ARRAY_A);

  $marcadores = array();
  $indexs = array();
  $datas = array();
  foreach($pre_marcadores as $marcador){
    $index = $marcador['Code']-32;
    $data = $caracteres[$index];
    array_push($datas,$data);
    array_push($indexs,$index);
  }
  $marcadores[0] = $indexs;
  $marcadores[1] = $datas;

  return $marcadores;

  //con resultados devuelve un array con cada una de las ReflectionClass

  //devuelve un array con 0 resultados si la tabla esta vacia o no encuentra nada o si la tabla no existe

}
?>
