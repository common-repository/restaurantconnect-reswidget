<?php

	if(!class_exists('WP_List_Table')){

	    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

	}



	class RestaurantDetails extends WP_List_Table 

	{

		var $strQuery;

		function __construct()

		{

			parent::__construct(array(

				'singular' => 'restaurant',

				'plural' => 'restaurants',

				'ajax' => true 

				));

		}

		/*function column_id($item)

		{

            $actions = array();

            $actions['edit'] = (sprintf("<a href='?page=%s&mode=%s&id=%s'>Edit</a>",$_REQUEST['page'],'edit',$item['id']));

            $actions['delete'] = (sprintf("<a href='?page=%s&action=%s&id=%s' 

                                        onclick=\"if ( confirm( '" . esc_js( sprintf( __( "You are about to Delete Restaurant \n 'Cancel' to stop, 'OK' to Delete." ),  $item['name'] ) ) . "' ) ) { return true;}return false;\">Delete</a>",$_REQUEST['page'],'delete',$item['id']));

		    return sprintf('%s %s', $item['id'],$this->row_actions($actions));

		}*/

        function column_shortcode($item)

        {

            $actions = array();

            $actions['edit'] = (sprintf("<a href='?page=%s&mode=%s&id=%s'>Edit</a>",$_REQUEST['page'],'edit',$item['id']));

            $actions['delete'] = (sprintf("<a href='?page=%s&action=%s&id=%s' 

                                        onclick=\"if ( confirm( '" . esc_js( sprintf( __( "You are about to Delete Restaurant \n 'Cancel' to stop, 'OK' to Delete." ),  $item['name'] ) ) . "' ) ) { return true;}return false;\">Delete</a>",$_REQUEST['page'],'delete',$item['id']));

            $str_shortcode = '[res id='.$item['id'].']';

            return sprintf('%s %s', '<input type="text" value="'.$str_shortcode.'" onfocus="this.select();" readonly style="width:100px;" />',$this->row_actions($actions));

        }



		function column_display_type($item)

		{

		   return sprintf('%s', stripslashes($item['display_type']));

		}



		function column_activated_pages($item)

		{

	        return sprintf('%s',  stripslashes($item['activated_pages']));

    	}

        



    	function column_cb($item)

    	{

    	    return sprintf(

    	        '<input type="checkbox" name="%1$s[]" value="%2$s" />',

    	        $this->_args['singular'],

    	         $item['id']

    	    );

    	}



    	function get_columns()

    	{

    	    $columns = array(

    	        'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text

                //'id' => 'id',

                'shortcode' => 'Shortcode',

    	        'display_type' => 'Display Type',

    	        'activated_pages' => 'Activated Pages',

    	     );

    	    return $columns;

    	}



    	function get_sortable_columns() 

    	{

    	    $sortable_columns = array(
                 'shortcode' => array('id', false),
             );
             return $sortable_columns;

    	}



    	function get_bulk_actions() 

    	{

    	   $actions = array(

    	        'delete'    => 'Delete',

    	    );

    	    return $actions;

    	}



		function prepare_items($searchvar= NULL) 

    	{       

    	    global $wpdb; //This is used only if making any database queries

    	    $per_page = 5;

    	    $columns = $this->get_columns();

    	    $hidden = array();

    	    $sortable = $this->get_sortable_columns();

    	    $strTbl = $wpdb->prefix."restaurants";               

    	    $this->_column_headers = array($columns, $hidden, $sortable);



           	if(!empty($searchvar)){

   	        	$this->strQuery = "SELECT id, display_type, activated_pages FROM $strTbl WHERE name LIKE '%".$searchvar."%' ORDER BY id DESC"; 

   	    	}

	   	    else

	   	    {

	   	        $this->strQuery = "SELECT id,display_type, activated_pages FROM $strTbl ORDER BY id DESC"; 

	   	    }	

            

    	    $data = $wpdb->get_results( $this->strQuery, ARRAY_A );



    	    function usort_reorder($a,$b)

    	    {

    	        $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'name'; //If no sort, default to rank

    	                   $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc'; //If no order, default to desc

    	        if(is_numeric($a[$orderby]))

    	        {

    	             $result = ($a[$orderby] > $b[$orderby]?-1:1); //Determine sort order

    	        }

    	        else

    	        {

    	            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order

    	        }

    	        return ($order==='desc') ? $result : -$result; //Send final sort direction to usort

    	    }

    	    usort($data, 'usort_reorder');

    	    $current_page = $this->get_pagenum();

    	    $total_items = count($data);

    	    

    	    $data = array_slice($data,(($current_page-1)*$per_page),$per_page);

    	          

    	    $this->items = $data;

    	  

    	    $this->set_pagination_args( array(

    	        'total_items' => $total_items,                  //WE have to calculate the total number of items

    	        'per_page'    => $per_page,                     //WE have to determine how many items to show on a page

    	        'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages

    	    ) );

    	}

	}



	function fnAddRestaurant()

	{

		global $wpdb;

	    $RestaurantListTable = new RestaurantDetails();

	    $this_file = "?page=".$_REQUEST['page'];

	    $strTbl = $wpdb->prefix."restaurants"; 

	   	switch($RestaurantListTable->current_action())

	    {

	        case "delete":

	            if((isset($_GET['action2']) && ($_GET['action2']=="-1")) || isset($_GET['action']) && ($_GET['action']=="-1"))

	            {

	                $del_id = $_GET['restaurant'];

	                $chkArray = is_array($del_id);

	                if($chkArray)

	                {

	                    foreach($del_id as $delId)

	                    {

	                        $del_data = fn_delete_Restaurant_data( $delId );

	                    }

	                } 

	            }

	            if(isset($_GET['id']) && $_GET['id'])

	            {

	                $del_id = $_GET['id'];

	                $del_data = fn_delete_Restaurant_data( $del_id );

	            }

	            if(isset($del_data)){ ?>

	                <div class='<?php echo $del_data['msgClass']; ?>'>

	                    <p><?php echo $del_data['msg']; ?></p>

	                </div>

	            <?php } 

	                     

	            $this_file = $this_file."&update=delete";

	            break;

	        default:

	        break;

	    } 

        if(isset($_GET['id']) && ($_GET['mode'] == 'edit'))

        {

            $intEditId= $_GET['id'];

           

            $arrRoleData = $wpdb->get_row("SELECT id, display_type, activated_pages FROM $strTbl WHERE id = $intEditId", ARRAY_A); 

            $checked_pages  = explode(", ",$arrRoleData['activated_pages']);

        }

        //check blank data & add record

        if(isset($_POST['keybutton']) && !empty($_POST['keybutton']))

        {

        	extract($_POST);

        	update_option( 'demokey',$key );

        }

        if (isset($_POST['addRestaurant']) && !empty($_POST['addRestaurant']))

        {

            extract($_POST);

            if($activated_pages > 1) :

            $activated_pages = implode(", ", $activated_pages);

            endif;

            $arrWhere = array();  

            if( ! empty($display_type) && ! empty($activated_pages)) :

                $arrData =  array( 

                    'display_type' => $display_type,

                    'activated_pages' => $activated_pages,

                );

                if( isset($_GET['id']) && ! empty($_GET['id']) ) :

                    $arrWhere = array("id" => $_POST['id']);

                    $arrMsg = fn_Insert_Update_Restaurant($arrData, $arrWhere);

                else :

                    $arrMsg = fn_Insert_Update_Restaurant($arrData);

                endif;

            else :

                $arrMsg = array('msg' => 'All fields are required.','msgClass' =>'updated');

            endif;

        }

        //Fetch, prepare, sort, and filter our data...

        $RestaurantListTable->prepare_items($_GET['s']);

        if ( ! empty($messages) ) {

            foreach ( $messages as $msg )

            echo $msg;

        }

        ?>



        <div class="wrap">

            <div class="icon32 icon32-posts-post" id="icon-approve">

                <br>

            </div>

            <h2></h2>

            <?php if(isset($arrMsg) && !empty($arrMsg)){ ?>

                <div class="<?php echo $arrMsg['msgClass']; ?>">

                <p><?php echo $arrMsg['msg']; ?></p>

            </div>

            <?php } ?>

            <div id="col-container" class="RoleContainer">

                <div id="col-right">

                    <div class="col-wrap">

                        <div class="form-wrap">

                            <form id="testimonials-filter" method="get">

                                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />

                                <p class="search-box">

                                    <label class="screen-reader-text" for="post-search-input">Search:</label>

                                    <input id="post-search-input" type="search" value="<?php echo $_GET['s']; ?>" name="s">

                                    <input id="search-submit" class="button" type="submit" value="Search " name="">

                                </p>

                                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" /> 

                                <?php $RestaurantListTable->display();  ?>

                            </form>

                        </div>

                    </div>

                </div>

                <div id="col-left">

                    <div class="col-wrap">

                        <div class="form-wrap">

                        <?php

                            $strLabel = (isset($intApproveId)) ? "Approve" : "Add" ;

                        ?>

                            <style type="text/css">

                                label.error {display: none !important;}

                                input.error {border: 1px solid red !important; }

                                .chkRequired{color: red;}

                            </style>

                             <h3>

                                ResWidget Configuration

                                <?php if(isset($intApproveId)) { ?>

                                <a href="?page=restaurant" class="add-new-h2">Add New</a> 

                               <?php } ?> 

                            </h3>

                            <form method="post" action="" name="form_add_Restaurant" id="form_add_Restaurant" autocomplete=off>

                                <div class="form-field">

                                    <label for="display_type"><strong>Display Type</strong><span class="chkRequired">*</span></label>

                                    <select name="display_type" style="width:70%">

                                        <?php

                                        $option_array = array("default"=>"Default","mini"=>"Mini");

                                        foreach ($option_array as $key => $value) 

                                        {

                                            ?>

                                            <option value="<?php echo $key; ?>" 

                                            <?php if(!empty($arrRoleData['display_type']) && $arrRoleData['display_type'] == "$key")  echo 'selected = "selected"'; ?>><?php echo $value; ?></option>

                                            <?php

                                        }  ?>

                                    	<!-- <option value="default" 

                                            <?php if (!empty($arrRoleData['display_type']) && $arrRoleData['display_type'] == 'default')  echo 'selected = "selected"'; ?>>    Default</option>

                                    	<option value="mini" 

                                            <?php if (!empty($arrRoleData['display_type']) && $arrRoleData['display_type'] == 'mini')  echo 'selected = "selected"'; ?>>Mini

                                        </option> --> 

                                    </select>

                                </div>

                                <div class="form-field">

                                    <label for="activated_pages"><strong>

                                    Activated Pages</strong><span class="chkRequired">*</span></label>

                                    <?php $category_val = array("p1"=>"Reserve (p1)","p2"=>"News (p2)","p3"=>"Promos (p3)","p4"=>"Group Dining (p4)");

                                    	foreach ($category_val as $key => $value) { ?>

                                    		<input type="checkbox" name="activated_pages[]" value="<?php echo $key; ?>" <?php if(isset($checked_pages) && in_array("$key",$checked_pages)) { ?> checked="checked" <?php } ?>><?php echo "&nbsp;".$value."<br>"; ?>

                                    <?php	}

                                    ?>

                                </div>

                                <p class="submit">

                                    <?php $strBtn = (empty($_GET['id'])) ? 'Submit' : 'Update'; ?>

                                    <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">

                                    <input type="submit" value="<?php echo $strBtn; ?>" class="button" id="addRestaurant" name="addRestaurant">

                                    <a href="?page=resto-configuration" class="cancel">Cancel</a>

                                </p>

                            </form> 

                        </div>

                    </div>

                </div>   <!-- /col-left -->

            </div><!-- /col-container -->

        </div>

	    <?php

	}

	

	function fn_Insert_Update_Restaurant( $arrData, $arrWhere= array() ) 

    {

        global $wpdb;

        $strTbl = $wpdb->prefix.'restaurants';

        if(count($arrWhere)==0)

        {

            $wpdb->insert($strTbl,$arrData);

            if(!empty($wpdb->insert_id))

            {

                $arrMsg = array('msg' => 'Configuration saved successfully.','msgClass' =>'updated');

                return $arrMsg;

            }

            else

            {

                $arrMsg = array('msg' => 'Something went wrong','msgClass' =>'updated');

                return $arrMsg;

            }

        }

        else

        {

            $boolUpdate = $wpdb->update($strTbl,$arrData,$arrWhere); 

            if($boolUpdate)

            {

                $arrMsg = array('msg' => 'Configuration updated successfully.','msgClass' =>'updated');

                return $arrMsg;

            }       

        }

    }

	function fn_delete_Restaurant_data($intId)

    {

        global $wpdb;

        $strTbl = $wpdb->prefix."restaurants";

        $chkArray = is_array($intId);

        if($chkArray)

        {

            foreach($intId as $del_id)

            {

                $wpdb->delete( 

                    $strTbl,

                    array( 'id' => $del_id ), 

                    array( 

                        '%s'

                    ), 

                    array( '%d' ) 

                );

            }

        }

        else

        {

            $wpdb->delete( 

                $strTbl,

                array( 'id' => $intId ), 

                array( 

                    '%s'

                ),

                array( '%d' ) 

            );

        }

        $arrMsg = array('msg' => 'Restaurant(s) Deleted.','msgClass' =>'updated');

        return $arrMsg;

    }

    