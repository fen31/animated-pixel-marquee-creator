<?php
namespace apmc\shortcodes\shortcodes;
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
	function marquee_shortcode($atts){
		$atributos = shortcode_atts(array('id'=>0,'link'=>""),$atts);

		$id = $atributos['id'];

    		$opciones = get_option("apmc_marquee_$id");
      if(!empty($opciones)){
        global $wpdb;
        $nombre_tabla = "{$wpdb->prefix}apmc_caracteres";
    		$result = $wpdb->get_results("SELECT Data FROM {$nombre_tabla}",ARRAY_A);
        $chars = json_decode($result[0]['Data']);
        wp_enqueue_style('apmcshortcode-style',APMCPLUGIN_URL.'/css/shortcode-style');
    		wp_register_script('apmcmarquee',APMCPLUGIN_URL.'/js/marquee.js',array(),NULL,true);
        wp_enqueue_script('apmcmarquee');
        wp_localize_script('apmcmarquee','apmcdata',['opciones'=>$opciones,'chars'=>$chars]);
    }
    if(!empty($atributos["link"])){
      $link = 'javascript:location.href="'.$atributos["link"].'"';
    }

	 	ob_start();
?>

<div  id="apmcwrap" class="apmcwrap" onclick='<?php echo $link ?>'>
  <canvas id="apmccanvas" width="0" height="0" class="apmccanvas"></canvas>
</div>

<?php
		return ob_get_clean();


	}



?>
