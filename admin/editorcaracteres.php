<?php
namespace apmc\admin\editorcaracteres;
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
function caracteres_view(){
	  wp_enqueue_style('apmceditor-style',APMCPLUGIN_URL.'css/editor-style.css',array(),false,'all');

  global $wpdb;

		$nombre_tabla = "{$wpdb->prefix}apmc_caracteres";
		$result = $wpdb->get_results("SELECT Data FROM {$nombre_tabla}",ARRAY_A);
    $chars = json_decode($result[0]['Data']);

    $nombre_tabla = "{$wpdb->prefix}apmc_marcadores";
    $marcadores = $wpdb->get_results("SELECT Code FROM {$nombre_tabla}",OBJECT_K);


		wp_register_script('apmcchar',APMCPLUGIN_URL.'js/char.js',array(),NULL,true);
		wp_enqueue_script('apmcchar');
    wp_localize_script('apmcchar','apmcajax_marcadores',['ajaxurl'=>admin_url('admin-ajax.php'),'nonce'=>wp_create_nonce('apmcajax_marcadores')]);
    wp_localize_script('apmcchar','apmcmarcadores',$marcadores);

    wp_register_script('apmcchareditor',APMCPLUGIN_URL.'js/chareditor.js',array(),NULL,true);
    wp_enqueue_script('apmcchareditor');
    wp_localize_script('apmcchareditor','apmcajax_guardar',['ajaxurl'=>admin_url('admin-ajax.php'),'nonce'=>wp_create_nonce('apmcajax_guardar')]);

		wp_register_script('apmcpaginacion',APMCPLUGIN_URL.'js/paginacion.js',array(),NULL,true);
    wp_enqueue_script('apmcpaginacion');
    wp_localize_script('apmcpaginacion','apmcchars',$chars);


?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>stylesheet</title>

</head>
<body onload="apmciniciar_editor()">


	<div id="tools" class="tools">


		<div class="charedit">
					<div class="rango">
						<div class="titulos">
							Search
						</div>
						<input title="Search one character"style="margin-top:5px" title="search" onchange="apmcpaginacion.buscar(this)" class="entrada" type="text" size="1" maxlength="1" placeholder="-">
					</div>
		      <div class="rango">
      			<div class="titulos">
                    Pagination
            </div>
    				<div class="paginacion">
    					<div title="go to first page" onclick="apmcpaginacion.primera_p()" class="boton extrem1"></div>
    					<div  title="go to one less page" onclick="apmcpaginacion.una_menos()" class="boton step1"></div>
    					<div class="boton">
    						<input id="n_pagina"  pattern="\d*" title="go to a page number" onchange="apmcpaginacion.n_pagina(this)"class="entrada" value="1" type="text" size="1" maxlength="2">
    					</div>
    					<div title="go to one more page" onclick="apmcpaginacion.una_mas()"class="boton step"></div>
    					<div title="go to last page" onclick="apmcpaginacion.ultima_p()" class="boton extrem"></div>
    				</div>
          </div>
		</div>
		<div class="charedit">
      <div class="rango">
        <div class="titulos">
          Tools
        </div>
        <div id="g_" title="Save all changes" onclick="apmcchar_editor.guardar()" class="boton4 guardar"></div>
        <div id="d_" title="Undo the activated  character " onclick="apmccurrent_char.deshacer()" class="boton4 deshacer"></div>
        <div id="r_'" title="Redo the activated  character" onclick="apmccurrent_char.rehacer()" class="boton4 rehacer"></div>
        <div id="b_" title="Clear the activated  character" onclick="apmccurrent_char.vaciarTodo()"class="boton4 borrar"></div>
        <div id="ll" title="Fill the activated  character" onclick="apmccurrent_char.llenarTodo()" class="boton4 llenar"></div>
        <div id="c_" title="Copy the activated  character" onclick="apmcchar_editor.copiar()" class="boton4 copiar"></div>
        <div id="p_" title="Paste the activated  character" onclick="apmcchar_editor.pegar()" class="boton4 pegar"></div>
      </div>
		</div>


	</div>
	<main>
<?php
  for($i = 0;$i < 10 ; $i++){
    $id=$i;

			$html = '<div id="row_'.$id.'"  class="charrow">
				<div class="bloque1">
						<h1 id="letra_'.$id.'" title="Character" id="letra" class="letra">A</h1>
						<h4 id="code_'.$id.'" title="Character Code" id="code" class="code">122</h4>
						<div class="charedit">
								<div title="Add/Remove this character to your bookmarks" id="m_'.$id.'" title="Maker" onclick="apmccurrent_char.marcador(this)" class="boton4 marcador"></div>
                <div title="Reset character to last saved state" id="res_'.$id.'" title="Restore" onclick="apmccurrent_char.restablecer(this)" class="boton4 restablecer"></div>

						</div>
				</div>

				<div title="Click to draw activate and draw" id="draw_'.$id.'" onclick="apmcchar_editor.activar(this)" class="chardraw" ></div>

			</div>';
			echo $html;
    };




?>
	</main>

</body>

</html>

<?php
}
?>
