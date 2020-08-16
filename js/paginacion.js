
apmcpaginacion ={
	context:this,
	total_paginas:0,
	intervalo:new Array(2),
	chars:apmcchars,
	pagina:0,
	paginar:function(){
	  var resto = this.chars.length % 10;
	  this.total_paginas =  resto ==0 ? this.chars.length / 10 : parseInt(this.chars.length / 10 + 1);
		try{document.getElementById('t_pagina').innerHTML = this.total_paginas}catch(error){}
	},
	setPagina:function(p){
	  if( apmccurrent_char.isset){
	    apmcchar_editor.desactivar(apmccurrent_char.row);
	  }
	  this.pagina = p
	  document.getElementById('n_pagina').value = p + 1;
	  this.setIntervalo();

	},
	setIntervalo:function(){
	  this.intervalo[0] = this.pagina * 10;
	  this.intervalo[1] = this.intervalo[0] + 9;

	   apmcchar_editor.ponerMultiple();
	},
	primera_p:function(){
	  if(this.pagina != 0){
	  this.setPagina(0);
	}
	},
	una_menos: function(){

	  var aux = this.pagina -1 <= -1 ? 0: this.pagina - 1;

		 this.setPagina(aux);
	},
	n_pagina:function(ele){
		var val = ele.value;

		if(!isNaN(val)){
			var aux = val -1;
			if(aux < 0)aux =0;
			if(aux>this.total_paginas-2)aux=this.total_paginas-1;
			this.setPagina(aux);
		}else{
			this.setPagina(this.pagina)
		}
	},
	una_mas:function(){
	  var aux = this.pagina + 1
	  //Arreglar Aqui
	  if(aux < this.total_paginas){
		 this.setPagina(aux);
	  }
	},
	ultima_p:function(){
		 if(this.pagina < this.total_paginas -1){
		   this.setPagina(this.total_paginas - 1);
	    }
	},
	buscar:function(ele){
    		var value = ele.value;
    		var code = value.charCodeAt(0) -32 ;
    		this.setPagina(parseInt(code/10));
    		var ele = document.getElementById('draw_'+code%10)
    		var top = ele.getBoundingClientRect().y;
    		window.scrollTo(0,top - 40)
    		apmcchar_editor.activar(ele)
	},
}
function apmciniciar_editor(){
		 apmcpaginacion.paginar();
		 apmcpaginacion.setPagina(0);
		//document.write( vas123wpm_paginacion.wpm_chars);
}
