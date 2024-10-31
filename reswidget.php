<?php
/*
Plugin Name: RestaurantConnect ResWidget
Plugin URI: http://software.restaurantconnect.com
Description: Mobile friendly restaurant reservation booking widget from RestaurantConnect.
Author: RestaurantConnect, Inc
Version: 1.0
Author URI: http://software.restaurantconnect.com
*/

class RestaurantInfo
{
	function __construct()
	{
		define('BASE_API_URL', 'https://secure.restaurantconnect.com/reserve/v1/index_mobile.php');
		add_action("admin_menu", array($this,"fnrestaurantData"));
 		register_activation_hook( __FILE__,array($this,'fn_addrestaurant_table') );
 		add_shortcode( 'res',array($this, 'fn_restaurant_shordcode') );
 		add_filter( 'widget_text', 'do_shortcode' );
 		add_action( 'wp_enqueue_scripts',array($this, 'wpb_adding_scripts') );  
	}

	/** function wpb_adding_scripts to add javascript for iframe  
	**/
	function wpb_adding_scripts() {
		wp_register_script('my_amazing_script', plugins_url('/js/restaurant_widget.js', __FILE__), array('jquery'),'1.1', true);
		wp_enqueue_script('my_amazing_script');
	}

	/** function fn_restaurant_shordcode to retrive id and create iframe url 
	**/
	function fn_restaurant_shordcode($atts){
		extract(shortcode_atts(array('id' => ''), $atts));
		if($id <=0)
			return "There is an error in record";

		global $wpdb;
		$strTbl = $wpdb->prefix."restaurants";
		$arr_pages = array();
		//get active pages from record
		$str_pages = $wpdb->get_row($wpdb->prepare("SELECT activated_pages,display_type FROM $strTbl WHERE id= %d",$id));
		$arr_pages = explode(",",$str_pages->activated_pages);
		//get url from option table 
		$restaurant_api_key = get_option("restaurant_api_key");
		for ($i=1; $i < 5 ; $i++) { 
			$key = "p".$i;
			$retVal = (in_array($key,$arr_pages)) ? "1" : "0" ;
			$arr_url_param[$key] = $key."=".$retVal;  
		}
		$str_url_param = implode("&",$arr_url_param);
		$str_iframe_src = BASE_API_URL."?p=".$restaurant_api_key."&d=".$str_pages->display_type."&".$str_url_param; 
		$str_iframe = '<iframe id="restaurantconnect" src="'.$str_iframe_src.'"  scrolling="no" style="width:100%"></iframe>';
		$str_src = ($this->isValidateToken($restaurant_api_key)) ? $str_iframe : "Invalid API key." ;
		return $str_src;
	}
	/*This function is created for admin menu pages*/
	function fnrestaurantData(){
		require_once("add-restaurant.php");
		  $icon = plugins_url('restaurantconnect-reswidget')."/img/icon2.png";
		     add_menu_page("ResWidget", "ResWidget", "manage_options", "resto-api-key", array($this,"fnApiKey"),$icon);
		     add_submenu_page("resto-api-key","Add API Key", "Add API Key", "manage_options", "resto-api-key", array($this,"fnApiKey"));
		     add_submenu_page( "resto-api-key", "ResWidget Configuration", "ResWidget Configuration", "manage_options", "resto-configuration", "fnAddRestaurant");
	}
	/*This function is used for creating table restaurant*/
	function fn_addrestaurant_table(){
		global $wpdb;
		$table_name = $wpdb->prefix .'restaurants';
		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
		    id int(11)  NOT NULL AUTO_INCREMENT,
		    display_type varchar(100) NOT NULL,
		    activated_pages varchar(100) NOT NULL,
		    PRIMARY KEY  (id)
		    );";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}
	/*This function is used for checking the token is valid or not*/
	function isValidateToken($token)
	{
		return true;
	}
	/*This function is used adding and updating API key*/
	function fnApiKey()
	{
		if(isset($_POST['keybutton']) && !empty($_POST['key']))
		{
			$data = $this->isValidateToken($token);
			   $arrMsg = (!empty($data)) ? update_option("restaurant_api_key",$_POST['key']) : array('msg' => 'Invalid API key.','msgClass' =>'updated');
			   $arrMsg = array('msg' => 'API key updated successfully.','msgClass' =>'updated'); 
			  }
			  $strKey = get_option( "restaurant_api_key" );

		?>
		<div class="wrap">
		<style type="text/css">
			.chkRequired{
				color: red;
			}
		</style>
		<h1>API Key</h1>
		<div class="<?php echo $arrMsg['msgClass']; ?>"><?php if(isset($arrMsg) && !empty($arrMsg)) echo $arrMsg['msg']; ?></div>
		 <form method="post" action="?page=resto-api-key" name="key-form" id="key-form" autocomplete=off>
			<div class="form-field">
		        <label for="key"><strong>Enter API Key</strong>
		        <span class="chkRequired" >*</span> </label>

		        <input class="required input" type="text" name="key" value="<?php echo $strKey;  ?>"  maxlength="50" style="width:34%;padding: 5px 0px;" required/>
		        
		        <button name="keybutton" type="submit" value="Update" class="
		        button-primary">
		        	Update
		        </button>
		    </div>
		</form>
    	</div>	
<?php	}
}
$RestaurantInfo = new RestaurantInfo();
