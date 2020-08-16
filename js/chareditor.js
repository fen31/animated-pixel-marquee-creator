apmcchar_editor = {
	poner: function(e){
		var aux = (e- apmcpaginacion.pagina *10).toString();
		var wrap = document.getElementById("draw_" + aux);
		document.getElementById('letra_' + aux).innerHTML = String.fromCharCode(e+32);
		document.getElementById('code_' + aux).innerHTML = e + 32;
		if(apmcmarcadores.hasOwnProperty(e+32)){

			document.getElementById("m_"+aux).setAttribute('class','boton4 marcador2');
		}else{
			document.getElementById("m_"+aux).setAttribute('class','boton4 marcador');
		}
		//desactivado
		wrap.innerHTML = "";

    		var canvas = document.createElement("canvas");
    		canvas.height = 340;
    		canvas.width = 140;
    		wrap.appendChild(canvas);
				wrap.parentElement.style.display="inline-block";
    		var ctx = canvas.getContext("2d");
				if( apmcpaginacion.chars[e] != undefined){
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
				wrap.parentElement.style.display='none';
	},
	ponerMultiple:function(){
		for(var e =  apmcpaginacion.intervalo[0];e<= apmcpaginacion.intervalo[0] + 9;e++){
      		this.poner(e);
  		}
	},
	pixels:function(ctx,x,y,color){
    		ctx.fillStyle = color;
    		ctx.fillRect(x*20,y*20,20,20);

	},
	pixels2:function(a,x,y,color){
  		var pixel = "<div id='"+a;
  		pixel += "'style='position:absolute;border:1px solid black;display:inline-block;";
  		pixel += "left:"+(x*20)+"px;top:"+(y*20)+"px;";
  		pixel += "width:20px;height:20px;background-color:"+color;
  		pixel += "'></div>";
		return pixel;
	},
	activar:function(ele){
		if( apmccurrent_char.isset){
    			 apmcchar_editor.desactivar( apmccurrent_char.row);
  		}
		var row = parseInt(ele.id[5]);
		var index =row +  apmcpaginacion.intervalo[0];
		var data =  apmcpaginacion.chars[index];
		ele.innerHTML="";
		for(var x = 0;x<7;x++){
		    for(var y = 0 ;y<15;y++){
      			var a = (15 * x) + y;
      			var color = data[a] == 0 ? 'black' :'white' ;
				ele.innerHTML +=  apmcchar_editor.pixels2(a,x,y,color);
    			}
  		}
    		 apmccurrent_char.setChar(row,index,data, apmcchar_editor.context);
    		 apmccurrent_char.activar();
    		ele.onclick=  apmccurrent_char.draw.bind(null, apmccurrent_char);
	},
	desactivar:function(row){
		var cc =  apmccurrent_char;
		var index = cc.code-32
			 apmcpaginacion.chars[index] = cc.estado[cc.estado_actual].join("");
    		this.poner(index);
  		cc.desactivar();
  		cc.reset();
  		var row = document.getElementById('draw_'+row);
  		row.onclick = this.activar.bind(null,row);
	},
	copiar:function(){
  		if( apmccurrent_char.isset){
    		this.copydata =  apmccurrent_char.estado[apmccurrent_char.estado_actual];
 		}
	},
	pegar:function(){
  		if(this.copydata!=undefined){
    			 apmccurrent_char.nuevo_estado(this.copydata);
    			 apmccurrent_char._auxdrawstate();
  		}
	},
	guardar:function(e){
		if(apmccurrent_char.isset){
		  apmcchar_editor.desactivar( apmccurrent_char.row);
		}
		jQuery.ajax({
			url:apmcajax_guardar.ajaxurl,
			type:'post',
			data:{
				action:'apmcguardar',
				nonce:apmcajax_guardar.nonce,
				Data: apmcpaginacion.chars,
			},
			beforeSend:function(){
				     jQuery("#wpcontent").css("cursor", "progress");
						 jQuery(".tools").css("cursor", "progress");
						 jQuery(".boton4,boton").css("cursor", "progress");

			},
			success:function(res){
						if(res =="0")alert("Invalid Chars Data");
				  	jQuery("#wpcontent").css("cursor", "default");
						 jQuery(".tools").css("cursor", "default");
						 jQuery(".boton4,boton").css("cursor", "pointer");
			}
		});

	}


}
