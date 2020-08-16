<?php
    /**
* Plugin Name: Animated Pixel Marquee Creator
* Plugin URI: https://github.com/fen31/animated-pixel-marquee-creator.git
* Description: Animated Pixel Marquee Creator generates animated pixel panel simulating the electronic
signs that we can see on buses, metro, airports and advertisement. It also
contains a char editor to modify or create your own font, logos, symbols or
emblems.

* Version: 1.0.0
* Author: Fernando Espinosa González

*/

namespace apmc;
if ( !defined( 'ABSPATH' ) ) {
    exit;
}



define( 'APMCPLUGIN_DIR', dirname(__FILE__));
define('APMCPLUGIN_URL',plugin_dir_url ( __FILE__ ));
require_once(APMCPLUGIN_DIR.'/install/activacion.php');
require_once(APMCPLUGIN_DIR.'/install/desinstalacion.php');

register_activation_hook( __FILE__, 'apmc\install\activacion\activacion');
register_deactivation_hook( __FILE__, 'apmc\install\activacion\desactivacion');
register_uninstall_hook(__FILE__, 'apmc\install\desinstalacion\desinstalacion');

require_once(APMCPLUGIN_DIR.'/menu.php');
require_once(APMCPLUGIN_DIR.'/settings.php');
add_action('admin_menu', 'apmc\menu\add_menu');
add_action('admin_init', 'apmc\settings\main_settings');

require_once(APMCPLUGIN_DIR. '/shortcodes/shortcodes.php');
add_shortcode('marquee','apmc\shortcodes\shortcodes\marquee_shortcode');


require_once(APMCPLUGIN_DIR.'/ajax.php');

add_action('wp_ajax_apmcguardar','apmc\ajax\guardar');
add_action('wp_ajax_apmcmarcadores','apmc\ajax\marcar');
add_action('wp_ajax_apmcrestablecer','apmc\ajax\restablecer');


?>
