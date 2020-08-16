apmcchar_editor = {
    index:apmcindex,
  	poner: function(e){
  		var aux = (e- apmcpaginacion.pagina *10).toString();
  		var canvas = document.getElementById("draw_" + aux);
      canvas.parentElement.style.display= 'inline-block';

      canvas.setAttribute('data-code',this.index[e]);
      document.getElementById('cod_'+aux).value = String.fromCharCode(parseInt(this.index[e])+32);
      		var ctx = canvas.getContext("2d");
  				if( apmcpaginacion.chars[e] != undefined){
            document.getElementById('no-marcadores').style.display = "none";
      //////////////////////////
      		for(var x = 0;x<7;x++){
        		for(var y = 0 ;y<15;y++){
          			var a = (15 * x) + y;
          			var color =  apmcpaginacion.chars[e][a] == 0 ? 'black' :'white' ;

  							this.pixels(ctx,x,y,color);
        		}
      		}
  				return
    			}
  				canvas.parentElement.style.display='none';
  	},
  	ponerMultiple:function(){
  		for(var e =  apmcpaginacion.intervalo[0];e<= apmcpaginacion.intervalo[0] + 9;e++){
        		this.poner(e);
    		}
  	},
  	pixels:function(ctx,x,y,color){

      		ctx.fillStyle = color;
      		ctx.fillRect(x,y,1,1);


  	},
  	activar:function(ele){
        var code = ele.dataset.code;
        var texto = document.getElementById('apmctexto');
        var textos = texto.value;
        var index = texto.selectionStart;
        var aux = textos.substring(0,index) + String.fromCharCode(parseInt(code)+32)+textos.substring(index )

        texto.value= aux;
        texto.selectionStart = index+1;
  	},
  	desactivar:function(row){

  	},
  }

 apmccurrent_char = {
  isset:false,
}
function apmccopiar_shortcode(){
  var shortcode = document.getElementById('shortcode');
  shortcode.select();
    shortcode.setSelectionRange(0, 99999);
     document.execCommand("copy");
}
function apmcconfirmacion_borrar(){
  return confirm("¿Seguro que desea borrar este Marquee?")

}
