<?php
namespace apmc\admin\marquee;
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

function agregar_view(){
require_once(dirname(__FILE__).'/marquee_controller.php');
	wp_enqueue_style('apmcmarquee-style',APMCPLUGIN_URL.'css/marquee-style.css');
    $marcadores = \apmc\admin\marqueecontroller\marcadores_data();


      wp_register_script('apmcpaginacion',APMCPLUGIN_URL.'js/paginacion.js',array(),NULL,true);
      wp_enqueue_script('apmcpaginacion');
      wp_localize_script('apmcpaginacion','apmcchars',$marcadores[1]);

      wp_register_script('apmcchareditor',APMCPLUGIN_URL.'js/chareditor2.js',array(),NULL,true);
      wp_enqueue_script('apmcchareditor');
      wp_localize_script('apmcchareditor','apmcindex',$marcadores[0]);


		$data = \apmc\admin\marqueecontroller\agregar_view_controller();
		$velocidad = array();
		if($data['velocidad']==1){$velocidad['r']="checked";$velocidad['v']="";$velocidad['a']="";}
		if($data['velocidad']==2){$velocidad['r']="";$velocidad['v']="checked";$velocidad['a']="";}
		if($data['velocidad']==3){$velocidad['r']="";$velocidad['v']="";$velocidad['a']="checked";}
		$html = '<head>

						</head>
						<body onload="apmciniciar_editor()">
							<div id="admin-marquee" class="admin-marquee">
									<h1 class="am-titulo" style="color:'.$data["type"].'
									<a class="button action" href="admin.php?page=slm_tablelist">All Marquees</a>

                  '.maybe_title($data).'

					 					<form id="ledmarquee" class="form am-form" action="'.esc_attr($data["action"]).'" method="post">
											<div class="am-bloque">
												<label for="Nombre"><span>Name</span>
													<input class="am-entrada" type="text" id="Nombre" maxlength="255" name="Nombre" value="'.esc_attr($data["Nombre"]).'" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" required>
                          <i style="font-size:60%;margin-left:5px;color:#653a3a">Max 255</i>
                        </label>
                        <div class="wpm_help"><i>A descriptive name. It does not need to be unique.</i></div>

											</div>
											<div class="am-bloque">

  												<label for="texto"><span>Text</span>
  													<textarea name="texto" class="am-textarea" maxlength="11000" id="apmctexto" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" required>'.esc_textarea($data["texto"]).'</textarea>
  												</label>
                          <div class="wpm_help"><i>Marquee content.<br><font color="#653a3a">
                          Excessively long text can <b>crash</b> the tab, the browser or the device as well as <b>increasing the loading time</b>
                          </font></i></div>
											</div>
                      <div class="am-bloque marcadores">
                        <div id="marcadoreshead">
                          <div id="maricono" class="boton">
                          </div>
                          <a style=";color:#153eab;display:inline;vertical-align:top">Bookmarks</a>

                        </div>
                        '.marcadores_view().'

                      </div>

											<div class="am-bloque">
												<label for="tilesize"><span>Tilesize</span>
													<input type="text" pattern="\d*" maxlength="2" name="tilesize" placeholder="2" class="am-entrada" id="tilesize" value="'.esc_attr($data["tilesize"]).'" required></input>
												</label>
                        <div class="wpm_help"><i>The size of the tiles of the Marquee. Defines the size of the Marquee. (2 or 3 is a good size).</i></div>
											</div>
                      <div class="am-bloque">
												<label for="color"><span>Color</span>
													<input type="color" name="color" id="color" value="'.esc_attr($data["color"] ).'" required></input>
												</label>
                        <div class="wpm_help"><i>The color of the Marquee. * At the moment it is not possible to define the background color.</i></div>
											</div>
											<div  style="margin-top:30px" class="am-bloque">
                      <label style="vertical-align:top;" ><span>Speed</span></label>
                      <div style="vertical-align:middle;display:inline-block">
												<label for="rojo" id="r" class="co"><span>1X</span>
                          <input type="radio" id="rojo" name="velocidad" value="1" required '.$velocidad['r'].'>
                        </label><br>
												<label for="verde" id="v" class="co"><span>2X</span>
                          <input type="radio" id="verde" name="velocidad" value="2" '.$velocidad['v'].'>
                        </label><br>
												<label for="azul" id="a" class="co"><span>3X</span>
                          <input type="radio" id="azul" name="velocidad" value="3" '.$velocidad['a'].'>
                        </label><br>
                     </div>
                     <div class="wpm_help"><i>Marquee scrolling speed.</i></div>
											</div>
                      <div class="am-bloque">
                        <input type="submit" class="am-submit" value="Create/Update">
                      </div>
	        					</form>
                '.maybe_shortcode($data).'
							</div>
						</body>

				';
			echo $html;


}
function botones($id){
	$botones = '<a class="button action" href="admin.php?page=slm_agregar">Add New</a>';
	$url = add_query_arg(['page'=>'slm_agregar','action'=>'borrar','id'=>$id,'nonce'=>wp_create_nonce('apmcmarquee_borrar')],'admin.php');
	$href = esc_url($url);
  $msg = "Sure you want to remove this Marquee?";
	$botones .='<a class="button action" onclick="return confirm('."'".$msg."'".')" style="margin-left:2px !important" href="'.esc_attr($href).'">Delete</a>';
return $botones;
}

function maybe_title($data){
  if(absint($data["id"])>0){
     return '  <div class="am-bloque special">
         <h1 title="The name of a Marquee that has already been created.">Nombre:<span style="width:auto !important;margin-left:5px;color: #153eab">'.esc_html($data["Nombre"]).'</span></h1>
         <h4 title="The id of a Marquee that has already been created.">id:<span style="margin-left:5px;color:#153eab">'.absint($data["id"]).'</span></h4>
       </div>';
  }
}
function maybe_shortcode($data){
  if(absint($data["id"])>0){
     return '<div class="shortcode">
           <div style="width:49%;display:inline-block">
             <h4 style="display:inline-block"> SHORTCODE:</h4>
             <input title="The shortode to show this marquee" id="shortcode" type="text" style=""  value="'.esc_attr($data["shortcode"]).'" readonly>
             <div style="color:white" class="wpm_help"><i>Copy and paste this shortcode on desired block.</i></div>
           </div>
             <div title="Click to quickly copy the shortcode." id="clip" onclick="apmccopiar_shortcode()">
             Click to copy!
             </div>
     </div>';
  }
}
function marcadores_view(){
  $html ='<div id="marcadores" name="marcadores">';
  $html .='<div style="text-align:center">
            <input id="n_pagina" style="width:30px" maxlength="2" readonly></input><b>/</b><b id="t_pagina">0</b>
            <button "Previous page" onclick="vas123wpm_paginacion.una_menos()" type="button">&lt</button>
            <button title="Next page" onclick="vas123wpm_paginacion.una_mas()" type="button">&gt</button>
            <i>Bookmarks added from the char editor.</i>
          </div>';


  for($i=0;$i<10;$i++){
  $html .='<div id="row_'.$i.'"class="charrow">
            <canvas title="Click here to add this marker." height="15" width="7" onmousedown="apmcchar_editor.activar(this)" id="draw_'.$i.'" class="canvasdraw">
            </canvas>
            <input title="This is the character visible during the edition." style="width:30px" maxlength="1" type="text"  id="cod_'.$i.'" readonly></input>
          </div>';

        }
  $html.='<h1 id="no-marcadores">No bookmarks</h1></div>';

        return $html;
}
?>
