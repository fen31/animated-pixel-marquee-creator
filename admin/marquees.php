<?php
namespace apmc\admin\marquees;
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
function tablelist_view(){
	require_once(dirname(__FILE__) . '/marquees_list.php');

	$tabla = new \apmc\admin\marqueeslist\marqueesList();
    //Fetch, prepare, sort, and filter our data...
    $tabla->prepare_items();

    ?>
    <div class="wrap">
        <style>

          a[href*="shortcode"]{
            display:none !important;
          }
          #shortcode::before{
            content:"shortcode";
            color:#0073aa;
            text-align: center;
            margin-left:10px;
          }



        </style>
        <div id="icon-users" class="icon32"><br/></div>

        <h1 style="margin-bottom:10px">Marquees</h1>

        <form id="filtro-marquees" method="get">

            <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']) ?>" />

            <?php
							$tabla->display(); ?>
        </form>

    </div>
<?php

}
?>
