function apmcgetAncho(){
  return document.getElementById('apmcwrap').clientWidth;
}

window.addEventListener('resize',function(){
   apmc.load( apmcdata.opciones['tilesize'], apmcdata.opciones['texto'], apmcdata.opciones['color'], apmcdata.opciones['velocidad'],apmcgetAncho());
  apmcmarqueeStart( apmc.gl, apmc.texto, apmc.tilenorm);
});

apmc = {
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
  load:function(tilesize,texto,color,velocidad,ancho){
    this.tilesize = tilesize;
    this.setColor(color);
    this.velocidad = velocidad;
    this.ancho = ancho;
    this.texto="                  ".substring(0,parseInt((17/100)*(this.ancho/this.tilesize)))+texto;
    this.tls= this.tilesize+".0";
    this.alto= this.tilesize*15;
    this.marqueecolumnas=Math.floor(this.ancho/this.tilesize);
    this.textoL=this.texto.length * 7;
    this.textoLS= this.textoL*this.tilesize;
    this.marqueesL=Math.ceil(this.marqueecolumnas/this.textoL)+1;
    this.tilenorm=this.tilesize/this.ancho;

    this.gl = this.canvas.getContext("webgl",{ alpha: false,depth:false,stencil:false,antialias:false,premultipliedAlpha:false,preserveDrawingBuffer:true}) || this.canvas.getContext("experimental-webgl");

    this.canvas.width=this.ancho;
    this.canvas.height=this.alto;
    document.getElementById('apmcwrap').style.height = this.alto +'px';
  }
}

 apmc.load( apmcdata.opciones['tilesize'], apmcdata.opciones['texto'], apmcdata.opciones['color'], apmcdata.opciones['velocidad'],apmcgetAncho());


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

      gl_PointSize =${ apmc.tls};
       gl_Position = vec4(a_position-vec2(momento,0), 0, 1);
       v_texCoord = a_texCoord * vec4(${ apmc.rgb[0]},${ apmc.rgb[1]},${ apmc.rgb[2]},1.0);
}`;


function apmcmarqueeStart(gl,texto,tilenorm){

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

      // Get A WebGL context
      /** @type {HTMLCanvasElement} */


      gl.viewport(0, 0, gl.canvas.width, gl.canvas.height);

      // setup GLSL program
      var vertexShader = createShader(gl,gl.VERTEX_SHADER,vsSource);
      var fragmentShader = createShader(gl,gl.FRAGMENT_SHADER,fsSource);
      //PASO DOS: CREAR Y UN PROGRAMA, UNIRLE LOS SHADERS ANTERIORES Y LINKARLO AL CONTEXTO
      var program = createProgram(gl,vertexShader,fragmentShader);

      // Localizamos las variables de entrada
      var positionLocation = gl.getAttribLocation(program, "a_position");
      var texcoordLocation = gl.getAttribLocation(program, "a_texCoord");
      var momentoLocation = gl.getUniformLocation(program,'momento');

      // Puffer para las posiciones
      var precount;
      function bufferCoor(){
        var positionBuffer = gl.createBuffer();
        var cP =  apmc.coordPosition();
        // Bind it to ARRAY_BUFFER (think of it as ARRAY_BUFFER = positionBuffer)

        gl.bindBuffer(gl.ARRAY_BUFFER, positionBuffer);
        // Set a rectangle the same size as the image.

        gl.bufferData(gl.ARRAY_BUFFER,cP,gl.STATIC_DRAW);
        precount = cP.length/2;

        return positionBuffer;
      }
      // provide texture coordinates for the rectangle.
      function buffercolor(){
        var texcoordBuffer = gl.createBuffer();
        var c =  apmc.color()
        gl.bindBuffer(gl.ARRAY_BUFFER, texcoordBuffer);
        gl.bufferData(gl.ARRAY_BUFFER, c, gl.STATIC_DRAW);

        return texcoordBuffer;
      }

      var positionBuffer = bufferCoor();
      var texcoordBuffer = buffercolor();


      gl.clear(gl.COLOR_BUFFER_BIT);

      // Tell it to use our program (pair of shaders)
      gl.useProgram(program);


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


      //gl.deleteBuffer(texCoordBuffer)
      var momento = (texto.length * 7)*2 ;

    var mom = 0;
    var of = 0.0;
    function draw(){
        mom = mom%momento
        gl.uniform1f(momentoLocation,mom*tilenorm);
        gl.drawArrays(gl.POINTS, 0, precount);
        mom +=  apmc.velocidad;
        requestAnimationFrame(draw);
    }
    requestAnimationFrame(draw);
}

 apmcmarqueeStart(apmc.gl, apmc.texto, apmc.tilenorm);
