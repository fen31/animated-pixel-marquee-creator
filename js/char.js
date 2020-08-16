
apmccurrent_char = {
  context:null,
  isset:false,
  _count:1,
  row:-1,
  code: 0,
  char:0,
  Data:[],
  estado:[],
  estado_actual:0,
  setChar:function(row,index,data,context){
    this.context = context;
    this.isset = true;
    this._count = 1;
    this.row = row;
    this.code = index + 32;
    this.char = String.fromCharCode(this.code);
    this.data = data.split("");
    this.estado = [];
    this.estado_actual = -1;
    this.nuevo_estado(this.data);
  },
  activar:function(){
    var row = document.getElementById("row_"+this.row);
      row.style.backgroundColor = "#0f7870";
      row.style.border = "2px solid #023e79";
    var restablecer = document.getElementById('res_'+this.row);
    restablecer.style.backgroundColor="#1a95d1";

  },
  desactivar:function(){
    var row = document.getElementById("row_"+this.row);
    row.style.backgroundColor = "#153eab";
    row.style.borderWidth = "4px 6px 4px 0px";
    row.style.borderStyle = "ridge";
    row.style.borderColor = "#020612";
    var draw =   document.getElementById("draw_"+this.row);
    var restablecer = document.getElementById('res_'+this.row);
    restablecer.style.backgroundColor="gray";

  },
  draw:function(current_char,event){
    var ele = event.target;
    var celda = ele.id;
    if(celda.length<4){
      var current_data = current_char.estado[current_char.estado_actual].slice();

    //console.log(current_data[celda])
        if(current_data[celda]=="1"){
            current_data[celda] = "0";
            ele.style.backgroundColor = "black";

          }else{
            current_data[celda] = "1";
            ele.style.backgroundColor = "white";

          }
          current_char._count -=1;
          if(current_char._count==0){
            current_char.nuevo_estado(current_data)
            current_char._count = 3
          }else{
            current_char.estado[current_char.estado_actual]=current_data;
          }
        }

  },
  nuevo_estado:function(data){
    this.estado_actual++

    this.estado[this.estado_actual] = data;

    if(this.estado.length>10){
      this.estado_actual--
      this.estado.shift();
    }else{
      var index = this.estado_actual + 1;
      this.estado.splice(index,this.estado.length);
      }

  },
  deshacer:function(){

      if(this.estado_actual -1 >-1){
        this.estado_actual = this.estado_actual -1 ;

        this._auxdrawstate();
      }
    },
    rehacer:function(){

      if(this.estado_actual + 1 < this.estado.length){
        this.estado_actual = this.estado_actual + 1;
        this._auxdrawstate();
      }

    },
    _auxdrawstate:function(){
      var current_data = this.estado[this.estado_actual];
        var row = document.getElementById('draw_'+this.row)
      for(var i = 0;i<105;i++){
        var color = current_data[i] == 0 ? "black" : "white";

        row.childNodes[i].style.backgroundColor = color;
      }
    },
    reset:function(){
      this.context=null;
      this.isset=false;
      this._count=1;
      this.row=-1;
      this.code= 0;
      this.char=0;
      this.data=[];
      this.estado=[];
      this.estado_actual=0;
    },
    llenarTodo:function(){
      if(this.isset){

        var data = new Array(15*7);
        data.fill(0);
        this.nuevo_estado(data);
        this._auxdrawstate();
      }
    },
    vaciarTodo:function(){
      if(this.isset){

        var data = new Array(15*7);
        data.fill(1);
        this.nuevo_estado(data);
        this._auxdrawstate();
      }
    },
    marcador:function(ele){
        var code = parseInt(ele.id[2])+  apmcpaginacion.intervalo[0]+32
          jQuery.ajax({
            url:apmcajax_marcadores.ajaxurl,
            type:'post',
            data:{
              action:'apmcmarcadores',
              nonce:apmcajax_marcadores.nonce,
              Code:code,
            },
            beforeSend:function(){

              jQuery("#wpcontent").css("cursor", "progress");
              jQuery(".tools").css("cursor", "progress");
              jQuery(".boton4,boton").css("cursor", "progress");
            },
            success:function(res){
              if(res=='1'){
                			document.getElementById("m_"+ele.id[2]).setAttribute('class','boton4 marcador2');
                      apmcmarcadores[code] = {Code:code.toString()}
              }else if(res=='0'){
                    			document.getElementById("m_"+ele.id[2]).setAttribute('class','boton4 marcador');
                          delete apmcmarcadores[code];
              }else if(res=='2'){
                alert('Invalid Code')
              }
              jQuery("#wpcontent").css("cursor", "default");
               jQuery(".tools").css("cursor", "default");
               jQuery(".boton4,boton").css("cursor", "pointer");
            }
          });

    },
    restablecer:function(ele){
      var self = this;
      if(ele.id[4]==this.row){
        jQuery.ajax({
          url:apmcajax_marcadores.ajaxurl,
          type:'post',
          data:{
            action:'apmcrestablecer',
            Code:this.code,
          },
          beforeSend:function(){
            jQuery("#wpcontent").css("cursor", "progress");
            jQuery(".tools").css("cursor", "progress");
            jQuery(".boton4,boton").css("cursor", "progress");
          },
          success:function(res){
            if(res =="2")alert("Invalid Code");
            var data = JSON.parse(res).data;
            self.nuevo_estado(data.split(""));
            self._auxdrawstate();
            jQuery("#wpcontent").css("cursor", "default");
            jQuery(".tools").css("cursor", "default");
            jQuery(".boton4,boton").css("cursor", "pointer");
          }
        });
      }
    },
}
