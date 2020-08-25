const APMCSPACEBETWEENTEXT = "            ";//At the moment the only way to change the space between repetitive text.

function apmcgetAncho(){
  return document.getElementById('apmcwrap').clientWidth;
}

window.addEventListener('resize',function(){
   apmcmarquee.load();
  apmcmarqueeStart();
});

apmcmarquee = {
  tilesize:0,
  tls:"",
  ancho:0,
  alto:0,
  columnas:7,
  filas:15,
  canvas:document.getElementById('apmccanvas'),
  rgb:[],
  texto:'',
  marqueecolumnas:0,
  textoL: '',
  textoLS: 0,
  marqueesL:0,
  tilenorm:0,
  setColor:function(hex){
      var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
      var color =  result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
      } : null;

        this.rgb[0] = color.r/256;
        this.rgb[1] = color.g/256;
        this.rgb[2] = color.b/256;
  },
  coordPosition:function(){

    var cP = [];
    for(var x = 0;x<this.textoL*this.marqueesL;x++){

      for(var y = 0;y<this.filas;y++){
        cP.push(((x/this.ancho)*this.tilesize)*2.0-1.0);
        cP.push((((y/this.alto)*this.tilesize)*2.0-1.0)*-1);
      }
    }
    return new Float32Array(cP);
  },
  color:function(){

    var c = []
    var aux = "1111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111"

    for(var leng = 0;leng<this.marqueesL;leng++){

      for(var chars = 0; chars<this.texto.length;chars++){

        var caracter =  apmcdata.chars[this.texto.charCodeAt(chars)-32] ? apmcdata.chars[this.texto.charCodeAt(chars)-32] : aux;

        for(var x = 0;x<this.columnas;x++){
          for(var y = 0;y<this.filas;y++){

            var a = (this.filas * x) + y;
            c.push(caracter[a]!=0?0:1);
          }
        }
      }
    }
    return new Float32Array(c);
  },
  gl:null,
  load:function(){
    this.tilesize = apmcdata.opciones['tilesize'];
    this.setColor(apmcdata.opciones['color']);
    this.velocidad = apmcdata.opciones['velocidad'];
    this.ancho = apmcgetAncho();
    this.texto=APMCSPACEBETWEENTEXT.substring(0,parseInt((17/100)*(this.ancho/this.tilesize)))+ apmcdata.opciones['texto'];
    this.tls= this.tilesize+".0";
    this.alto= this.tilesize*15;
    this.marqueecolumnas=Math.floor(this.ancho/this.tilesize);
    this.textoL=this.texto.length * 7;
    this.textoLS= this.textoL*this.tilesize;
    this.marqueesL=Math.ceil(this.marqueecolumnas/this.textoL)+1;//fill the marquee until it covers, even if it overflows. We add one more to make sure it can be repeated during the animation.
    this.tilenorm=this.tilesize/this.ancho;//It is necessary to normalize it to WebGl values

    this.gl = this.canvas.getContext("webgl",{ alpha: false,depth:false,stencil:false,antialias:false,premultipliedAlpha:false,preserveDrawingBuffer:true}) || this.canvas.getContext("experimental-webgl");

    this.canvas.width=this.ancho;
    this.canvas.height=this.alto;
    document.getElementById('apmcwrap').style.height = this.alto +'px';
  }
}

 apmcmarquee.load();


var fsSource = `
precision lowp float;

#define PROCESSING_COLOR_SHADER
varying vec4 v_texCoord;


void main() {
   gl_FragColor = v_texCoord;
}`;
var vsSource =
   ` attribute vec2 a_position;
attribute float a_texCoord;
varying vec4 v_texCoord;
uniform float momento;
void main() {

      gl_PointSize =${ apmcmarquee.tls};
       gl_Position = vec4(a_position - vec2(momento,0), 0, 1);
       v_texCoord = a_texCoord * vec4(${ apmcmarquee.rgb[0]},${ apmcmarquee.rgb[1]},${ apmcmarquee.rgb[2]},1.0);
}`;


function apmcmarqueeStart(){
  const gl = apmcmarquee.gl;
  const texto = apmcmarquee.texto;
  const tilenorm = apmcmarquee.tilenorm;
  const requestAnimationFrame = window.requestAnimationFrame || window.mozRequestAnimationFrame ||
                              window.webkitRequestAnimationFrame || window.msRequestAnimationFrame;



    function createShader(gl,type,source){
      var shader = gl.createShader(type);
      gl.shaderSource(shader,source);
      gl.compileShader(shader);
      var success = gl.getShaderParameter(shader,gl.COMPILE_STATUS);
      if(success){
        return shader;
      }
      console.log(gl.getShaderInfoLog(shader));
      gl.deleteShader(shader);
    }
    function createProgram(gl,vertexShader,fragmentShader){
      var program = gl.createProgram();
      gl.attachShader(program,vertexShader);
      gl.attachShader(program,fragmentShader);
      gl.linkProgram(program);
      var success = gl.getProgramParameter(program,gl.LINK_STATUS);
      if(success)return program
      console.log(gl.getProgramInfoLog(program));
      gl.deleteProgram();
    }




      // setup GLSL program
      var vertexShader = createShader(gl,gl.VERTEX_SHADER,vsSource);
      var fragmentShader = createShader(gl,gl.FRAGMENT_SHADER,fsSource);
      //PASO DOS: CREAR Y UN PROGRAMA, UNIRLE LOS SHADERS ANTERIORES Y LINKARLO AL CONTEXTO
      var program = createProgram(gl,vertexShader,fragmentShader);
        gl.useProgram(program);
      // Localizamos las variables de entrada
      var positionLocation = gl.getAttribLocation(program, "a_position");
      var texcoordLocation = gl.getAttribLocation(program, "a_texCoord");
      var momentoLocation = gl.getUniformLocation(program,'momento');

      // Puffer para las posiciones

      function bufferCoor(){
        var positionBuffer = gl.createBuffer();
        var cP =  apmcmarquee.coordPosition();


        gl.bindBuffer(gl.ARRAY_BUFFER, positionBuffer);


        gl.bufferData(gl.ARRAY_BUFFER,cP,gl.STATIC_DRAW);

        return positionBuffer;
      }
      //El color de los vértices
      function buffercolor(){
        var texcoordBuffer = gl.createBuffer();
        var c =  apmcmarquee.color()
        gl.bindBuffer(gl.ARRAY_BUFFER, texcoordBuffer);
        gl.bufferData(gl.ARRAY_BUFFER, c, gl.STATIC_DRAW);
        return {buffer:texcoordBuffer,length:c.length};
      }

      var positionBuffer = bufferCoor();
      var pretextcoordBuffer = buffercolor();
      var texcoordBuffer = pretextcoordBuffer.buffer;
      const precount = pretextcoordBuffer.length;

      gl.clear(gl.COLOR_BUFFER_BIT);

      // Tell it to use our program (pair of shaders)



      // Turn on the position attribute
      gl.enableVertexAttribArray(positionLocation);
      // Bind the position buffer.
      gl.bindBuffer(gl.ARRAY_BUFFER, positionBuffer);

      // Tell the position attribute how to get data out of positionBuffer (ARRAY_BUFFER)

      gl.vertexAttribPointer(
          positionLocation,2,gl.FLOAT, false,0,0);


      // Turn on the texcoord attribute
      gl.enableVertexAttribArray(texcoordLocation);

      gl.bindBuffer(gl.ARRAY_BUFFER, texcoordBuffer);

      gl.vertexAttribPointer(
          texcoordLocation,1,gl.FLOAT,false,0,0);



      var momento = (texto.length * 7)*2 //This is the distance, in tile units, for moving the text. We multiply by two, because it was necessary during the normalization of vertices in ampc.coordPosition ;

      gl.viewport(0, 0, gl.canvas.width, gl.canvas.height);


    gl.deleteProgram(program)
  apmc_draw(gl,momento,momentoLocation,tilenorm,precount,apmcmarquee.velocidad);

}

function apmc_draw(gl,momento,momentoLocation,tilenorm,precount,velocidad){

  var mom = 0;
//
  eval(`function _draw(){
    mom = mom%${momento};
    gl.uniform1f(momentoLocation,mom*${tilenorm});

    gl.drawArrays(gl.POINTS,0,${precount});

    mom += ${velocidad};
  requestAnimationFrame(_draw)
}`);
requestAnimationFrame(_draw);
}
 apmcmarqueeStart();

