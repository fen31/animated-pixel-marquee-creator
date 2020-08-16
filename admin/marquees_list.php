<?php
namespace apmc\admin\marqueeslist;
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

require_once( dirname(__FILE__).'/class-wp-list-table.php' );

class marqueesList extends \apmc\admin\classwplisttable\WP_List_Table {

    /** ************************************************************************

     * @var array
     **************************************************************************/




    /** ************************************************************************
     ***************************************************************************/
    function __construct(){
        global $status, $page;

        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'marquee',
            'plural'    => 'marquees',
            'ajax'      => false
        ) );

    }

    function column_Nombre($item){

		$id = absint($item['id']);

        $query1 = add_query_arg([
		'page'=>'slm_agregar',
		'action'=>'editar',
		'id'=>$id,]);

		$query2 = add_query_arg([
		'page'=> 'slm_tablelist',
		'action'=>'borrar',
		'marquee'=>$id,]);
      $msg = "Are you sure you want to delete this Marquee?";
        $actions = array(

            'Editar'=> '<a href="'.$query1.'">Edit</a>',
            'Borrar'=> '<a href="'.$query2.'" onclick="return confirm('."'".$msg."'".')">Delete</a>',
        );

        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ esc_html($item['Nombre']),
            /*$2%s*/ $id,
            /*$3%s*/ $this->row_actions($actions)
        );
    }

	function column_id($item){
		return absint($item['id']);

	}
    /** ************************************************************************
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
             $this->_args['singular'],
             absint($item['id'])
        );
    }

    function column_shortcode($item){
      $id= $item['id'];
      return "[marquee id=$id link=]";

    }


    /** ************************************************************************
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'Nombre'     => 'Name',
            'id'    => 'id',
            'shortcode' => 'shortcode',
        );
        return $columns;
    }


    /** ************************************************************************
     * @return array
     **************************************************************************/
    function get_sortable_columns() {
        $sortable_columns = array(
            'Nombre'     => array('Nombre',false),     //true means it's already sorted
            'id'    => array('id',true),
            'shortcode' => array('shortcode',false),
        );
        return $sortable_columns;
    }


    /** ************************************************************************
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_bulk_actions() {
        $actions = array(
            'borrar'    => 'Delete'
        );
        return $actions;
    }


    /** ************************************************************************
     * @see $this->prepare_items()
     **************************************************************************/
    function process_bulk_action() {
        global $wpdb;

        //Detect when a bulk action is being triggered...
        if('borrar'===$this->current_action() ) {


          $ids    = isset( $_GET['marquee'] ) && !empty($_GET['marquee']) ? $_GET['marquee'] : false;

          if ( ! is_array( $ids ) )$ids = array( $ids );

    				foreach($ids as $id) {
              if(!is_numeric($id))return ;
              $wpdb->delete( $wpdb->prefix.'apmc_marquees',[ 'id' => $id],[ '%d' ]);
    					delete_option("apmc_marquee_$id");
    				}

		    }

    }
    /** ************************************************************************
     * @global WPDB $wpdb
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     **************************************************************************/
    function prepare_items() {
      global $wpdb;

      $columns = $this->get_columns();
       $hidden = array();
       $sortable = $this->get_sortable_columns();

       $this->_column_headers = array($columns, $hidden, $sortable);

       $this->process_bulk_action();

       $nombre_tabla = "{$wpdb->prefix}apmc_marquees";
       $query = "SELECT * FROM {$nombre_tabla}";


         $orderby = !empty($_GET["orderby"]) && !is_numeric($_GET["orderby"]) ? sanitize_sql_orderby($_GET["orderby"]) : 'id';
         $order = !empty($_GET["order"]) && !is_numeric($_GET["order"]) ? sanitize_sql_orderby($_GET["order"]) : "asc";

         if(!empty($orderby) & !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; }


      $totalitems = $wpdb->query($query);

      $wpm_mpp = get_option('apmcmarqueesperpage');

      $perpage = !empty($wpm_mpp) ? $wpm_mpp : 5;


      $totalpages = ceil($totalitems/$perpage);
        $paged = !empty($_GET["paged"]) ? $_GET["paged"] : "";
      if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }//paged is validated here 

      if(!empty($paged) && !empty($perpage)){ $offset=($paged-1)*$perpage; $query.=' LIMIT '.(int)$offset.','.(int)$perpage; }
       $this->set_pagination_args( array(
       "total_items" => $totalitems,
       "total_pages" => $totalpages,
       "per_page" => $perpage,
    ) );


 /* -- Fetch the items -- */
    $this->items = $wpdb->get_results($query,ARRAY_A);


    }
	function extra_tablenav( $which ) {
		if ( $which == "top" ){
			echo '<a class="button action" href="admin.php?page=slm_agregar">Add New</a>';
     	}
	}


}
?>
