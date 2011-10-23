<?php   

/*
Plugin Name: WP-Keywords-Table
Plugin URI: http://sabirul-mostofa.blogspot.com
Description: Keywords management From CSV and a lot more
Version: 1.0
Author: Sabirul Mostofa
Author URI: http://sabirul-mostofa.blogspot.com
*/

include 'kt-affiliate.php';
$wpKeywordsTable = new wpKeywordsTable();
$wpKTAff = new kt_affiliate();

if(isset($wpKeywordsTable)) {
	add_action('admin_menu', array($wpKeywordsTable,'CreateMenu'),50);	
}
   
class wpKeywordsTable{
	
	function __construct(){

		add_action('admin_enqueue_scripts' , array($this,'admin_scripts'));
		add_action('wp_enqueue_scripts' , array($this,'front_scripts'));	
		add_action('wp_print_styles' , array($this,'front_css'));	
		add_action( 'wp_ajax_myajax-submit', array($this,'ajax_handle' ));
		add_action( 'wp_ajax_ajax_remove', array($this,'ajax_remove' ));
		add_action( 'wp_ajax_ajax_remove_multiple', array($this,'ajax_remove_multiple' ));
		add_action( 'wp_ajax_show_next', array($this,'ajax_next_page_show'));
		add_action( 'wp_ajax_ajax_getId', array($this,'ajax_process_insert'));
		add_action( 'wp_ajax_wt_table_show', array($this,'ajax_display_table'));
		add_action( 'the_content', array($this,'content_generate'));
		add_action( 'wp', array($this,'session_manipulate'));
		add_action( 'init', array($this,'update_user'));
		add_action( 'user_register', array($this,'after_register'));
		
		
		register_activation_hook(__FILE__, array($this, 'create_table'));
		
		}
		
	function admin_scripts(){
		if( stripos( $_SERVER['REQUEST_URI'], 'wpKeywordsTable' ) !==false || stripos($_SERVER['REQUEST_URI'], 'wpKtAffs') !== false ){					
			wp_enqueue_script('jquery');
			wp_enqueue_script('kt_autocomplete_script',plugins_url('/' , __FILE__). 'js/jquery.autocomplete-min.js');	
			wp_enqueue_script('kt_admin_script',plugins_url('/' , __FILE__).'js/script_admin.js');
			wp_register_style('kt_admin_css', plugins_url('/' , __FILE__).'css/style_admin.css', false, '1.0.0');
			wp_enqueue_style('kt_admin_css');
	
		}	
	}
	
	function front_scripts(){					
		wp_enqueue_script('jquery');					
			if(!(is_admin())){
				//adding  fancy
				/*
					if( $opts = get_option('kt-settings-var') ){
						global $post;
						if( $post -> ID == $opts['check_page'] ){
							wp_enqueue_script('kt_mh_script', plugins_url('/' , __FILE__).'js/jquery.mousewheel-3.0.4.pack.js');
							wp_enqueue_script('kt_fancy_script', plugins_url('/' , __FILE__).'js/jquery.fancybox-1.3.4.pack.js');
					
						}
					}
					* */
				
				wp_enqueue_script('kt_front_script', plugins_url('/' , __FILE__).'js/script_front.js');
				wp_localize_script('kt_front_script', 'ktSettings',
						array(
						'ajaxurl'=>admin_url('admin-ajax.php'),
						'pluginurl' => plugins_url('/' , __FILE__),
						'upload_dir'=>wp_upload_dir()
						));	

			}	
	}
	
	function front_css(){
		if(!(is_admin())):
		wp_enqueue_style('kt_front_css', plugins_url('/' , __FILE__).'css/style_front.css');
		endif;
	}
		
			
		

	function CreateMenu(){
		add_submenu_page('tools.php','Keywords Table','Keywords Table','activate_plugins','wpKeywordsTable',array($this,'OptionsPage'));
		add_submenu_page('tools.php','KT Settings','KT Settings','activate_plugins','wpKeywordsOptions',array($this,'KTSettings'));
		add_submenu_page('tools.php','KT Orders','KT Orders','activate_plugins','wpKeywordsOrders',array($this,'KTOrders'));
	}
	
	function content_generate($content){
		global $post;
		$opts = get_option('kt-settings-var');
		switch($post -> ID){
			case $opts['check_page']:
				return $this -> checkout_page();
			break;
			case $opts['mem_page']:
				return $this -> member_page();
			break;		
		}
		return $content;	
	}
	
	function checkout_page(){
		include 'Pages/checkout.php';
		}
	function member_page(){
		include 'Pages/member.php';
		}
		
		function update_user(){
			if(isset($_POST['change-pass'])){
				extract( get_option('kt-settings-var') );
				$check_page = get_permalink($check_page);
		
				if( $_POST['pass1'] != $_POST['pass2']){
					$check_page .='?pass_reset=1&fail=1';
					header("Location: {$check_page}");
					}
		
				if( $current = wp_get_current_user())
					$current = $current -> ID;
				if( !(isset($_GET['pass_reset']) ))
				@wp_update_user(  array ('ID' => $current, 'user_pass' =>  $_POST['pass1'] ) ) ;
			
				$check_page .= '?pass_reset=1';
				header("Location: {$check_page}");
			}
			
			}
		
	//initializing session vars
	function session_manipulate(){
		global $post;
                                    if(!is_object($post))return;
		$opts = get_option('kt-settings-var');
		

		
		if(isset($_COOKIE['cItems'])){
			$vars = $this -> process_cookie( $_COOKIE['cItems'] );
			extract($vars);
		}
		if( $post -> ID == $opts['mem_page'] && isset($_GET['export']) ){
			if( !is_user_logged_in() )exit;
			$this -> export_csv( $_GET['export']);
			exit;
		}
		
		if($post -> ID == $opts['check_page']):
			if(isset($_POST['paypal-hidden-submit'])):
				$response = $this -> hash_call( $itemP, 'SetExpressCheckout',false,$itemN );
				$token = $response['TOKEN'];
				$re_url=$opts['api_url'].urlencode( $token );
				//var_dump($re_url);
				//var_dump($response);
				//exit;
				@session_start();
				$_SESSION['TOKEN'] = $token ;				
				$_SESSION['price'] = $itemP;				
				$_SESSION['response'] = $response ;				
				header("Location: {$re_url}");
				exit;
				
				
			elseif( isset($_GET['PayerID']) ):
				@session_start();
				$response = $this -> hash_call( $_SESSION['price'], 'DoExpressCheckoutPayment', $_GET['PayerID'], $itemN );		
				if($response['ACK'] == 'Success'){                                                                                        
                                                                                              
					$this -> populate_table($_COOKIE['cItems']);
					$order_id = $this -> populate_orders($vars);
                                                                                        do_action('kt_affdate_insert', $_SESSION['price'], $order_id);  
					setcookie( 'cItems', '', time()-100,'/' );
					setcookie( 'keyExtPrice', '', time()-100,'/' );
				}
					
				$_SESSION['final'] = $response;
			else:
				@session_start();
			
			endif;
			
			
		endif;
		
	}
	
	function populate_table($data){
		global $wpdb;
	extract( $this -> process_cookie($data) );
	if( $id = wp_get_current_user())
		$id = $id -> ID;
	$keys= explode(',' , $itemS);
	foreach ( $keys as $key){
		$wpdb -> query("insert into wp_kt_members (member_id, key_id) values('$id','$key') "); 
		$count = $wpdb -> get_var("select count from wp_keywords_list where id='$key'");
		$count++;
		$count = $wpdb -> query("update wp_keywords_list set count='$count' where id='$key'");
		
		}	
	}
	
		function populate_orders($data){
		global $wpdb;
		extract( $data );
		if( $id = wp_get_current_user())
			$id = $id -> ID;
			$itemS = trim($itemS, ',');
                                  $key_ext = ( isset($_COOKIE['keyExtPrice']))? 1:0;
		$wpdb -> query("insert into wp_kt_orders (member_id, key_ids,price,key_ext) values('$id','$itemS', '$itemP', '$key_ext') "); 
                                    $order_id = $wpdb -> get_var("SELECT id from wp_kt_orders order by id desc limit 1");
                                    return $order_id;
		}
	
	function after_register($i){
		$opts = get_option('kt-settings-var');
		$loc = get_permalink ( $opts['check_page'] );
		session_start();
		$_SESSION['wt-current-user'] = $i;
		}
	
	

		// function for autocomplete
	function ajax_handle(){
		global $wpdb;
		$val = $_GET['query'] ;
		$res_array =  array();

		$results=$wpdb -> get_results("SELECT keyword FROM wp_keywords_list where 1=1",'ARRAY_N');
		foreach($results as $res){
			if(stripos($res[0], $val) !== false)$res_array[] = $res[0];
		}
		$suggs ='['. join(',', array_map(create_function('$a','return "\'".$a."\'";'), $res_array) ) . ']';		
		echo "{
		 query:'{$val}',
		 suggestions:{$suggs},
		 data:'',
		}";
		exit;
	}
		

			
	function ajax_remove(){
		global $wpdb;
		$key= mysql_real_escape_string( $_REQUEST['key']);								
		echo $test = $wpdb -> query("delete from wp_keywords_list where keyword='$key'");				
		exit;				
	}
				
				
	function ajax_remove_multiple(){
		global $wpdb;
		$keys= $_REQUEST['keys'];
		$keys = explode(',', $keys);
		foreach	($keys as $key){
			$key = mysql_real_escape_string($key);							
			$wpdb -> query("delete from wp_keywords_list where keyword='$key'");
		}				
		exit;

	}
				

		
		
	function create_table(){	
		$sql = "CREATE TABLE IF NOT EXISTS `wp_keywords_list` (
		`id` int unsigned NOT NULL AUTO_INCREMENT, 
		`keyword` varchar(250)  NOT NULL,
		`global_searches_month` int unsigned NOT NULL,
		`local_searches_month` int unsigned NOT NULL,
		`cpc` float(5,2) NOT NULL,
		`comp_pages` int unsigned  NOT NULL,
		`real_comp_pages` int unsigned  NOT NULL,
		`cp_strength` tinyint(1)  NOT NULL,
		`average_pr` float(5,2)  NOT NULL,
		`pr_strength` tinyint(1)  NOT NULL,
		`competition` tinyint(1) NOT NULL,
		`commercial` tinyint(1)  NOT NULL,
		`ads_count` smallint unsigned  NOT NULL,
		`keyword_rating` varchar(15)  NOT NULL,
		`category` varchar(15)  NOT NULL,
		`price` float(5,2) NOT NULL,
		`com` varchar(255)  NOT NULL,
		`net` varchar(255)  NOT NULL,
		`org` varchar(255)  NOT NULL,
		`published` tinyint(1)  NOT NULL default 1,
		`added` timestamp not null default current_timestamp,
		`count` int unsigned not null default 0,
		PRIMARY KEY (`id`),
		key `keyword`(`keyword`),
		key `price`(`price`),
		key `category`(`category`)   
		)";
		global $wpdb;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		
		$sql1 =  "CREATE TABLE IF NOT EXISTS `wp_kt_members` (
		`id` int unsigned NOT NULL AUTO_INCREMENT,
		`member_id` int unsigned  NOT NULL , 
		`key_id` int unsigned  NOT NULL,
		PRIMARY KEY (`id`),
		key `key`(`key_id`),
		key `member`(`member_id`)		
		)";
		
		$sql2 =  "CREATE TABLE IF NOT EXISTS `wp_kt_orders` (
		`id` int unsigned NOT NULL AUTO_INCREMENT,
		`member_id` int unsigned  NOT NULL , 
		`key_ids` varchar(255) NOT NULL,
		`price` float(5,2) NOT NULL,		
		`date` timestamp not null default current_timestamp,
                                    `key_ext` tinyint(1)  NOT NULL default 0,
		PRIMARY KEY (`id`),
		key `key`(`member_id`)		
		)";
		$sql3 =  "CREATE TABLE IF NOT EXISTS `wp_kt_affs` (
		`user_id` int unsigned NOT NULL,
		`referred_user_id` int unsigned  NOT NULL , 
		`order_id` int unsigned  NOT NULL default 0 , 
		`aff_income` float(8,2)  NOT NULL default 0 ,		
		PRIMARY KEY (`order_id`),			
		key `aff_key`(`referred_user_id`)		
		)";
		dbDelta($sql);
		dbDelta($sql1);
		dbDelta($sql2);
		dbDelta($sql3);

	}	
		
		
/*
 * Options Page
 * 
 * */		
	function OptionsPage( ){
		require_once 'kt-options.php';
	}//endof options page
	
	// Settings Page	
	function KTSettings(){
		require_once 'kt-settings.php';
	}
	
	function KTOrders(){
		require_once 'kt-orders.php';
		}
	
	
	//Processing data

	function process_array($data){
		
	 $data[6] = $this -> data_check($data[6]);
	 $data[8] = $this -> data_check($data[8]);
	 $data[9] = $this -> data_check($data[9]);
	 
	 if(stripos($data[10], 'yes') !== false) $data[10] = 1;
	 else $data[10] = 0;
		
      $data[14] = str_replace('$','',$data[14]);
      return $data;	
	}
	
	function data_check($ar_elem){
		$val_name = array( 'Easiest', 'Easy', 'Moderate', 'Hard', 'Too Hard');		
		foreach($val_name as $key => $value):
		if( stripos(trim($ar_elem), $value ) === 0 ){
			return $key;
		}			
		endforeach;
		return $ar_elem;
		
		}
		
	function reverse_process_array($data,$check_page = false){
		$edit = true;
		if( $this -> not_show($data[0]) ){
			$tmp = str_split( $data[0] ) ;
			$data[0] = $tmp[0]. implode('', array_map ( create_function('$a', 'if(preg_match("/\s/",$a))return $a;return "*";'), array_slice( $tmp, 1)) );
		     $edit = false;
		}
		$data[6] = $this -> reverse_check($data[6]);
		$data[8] = $this -> reverse_check($data[8]);
		$data[9] = $this -> reverse_check($data[9]);
		
		if($data[10] == 1)
			$data[10] = 'YES';
		else 
			$data[10] = 'N0';
		
		$data[14] = '$' . $data[14];
		if( !is_admin() ){
			$str = ($check_page)? '<a href="" class="remove-cart">Remove</a>' :'<a href="" class="add-to-cart">Add To Cart</a>';
			$str = ($edit) ? 'You Bought' : $str;
			$data = array_merge( array_slice($data,0,1), array($str) ,array_slice($data, 1, 18 ));
		}
		return $data;
	}
			
	function reverse_check($ar_elem){
		$val_name = array( 'Easiest', 'Easy', 'Moderate', 'Hard', 'Too Hard');	
		
		foreach($val_name as $key => $value):
		if( trim($ar_elem) == $key ){
		return $value;
		}			
		endforeach;
		return $ar_elem;	
		
		}
		
	function  not_show($keyword){
		global $wpdb;
		
		if(is_admin())return false;
		if(  $id = wp_get_current_user() ){
			if(is_int($keyword))
							if($this -> exists_in_table_double($id -> ID ,$key))
								return false;
							else return true;
			else	
				$key = $wpdb -> get_var("select id from wp_keywords_list where keyword='$keyword'");
				if($this -> exists_in_table_double($id -> ID ,$key))
					return false;		
		}
		return true;
				
	}
	
	function process_cookie($cookie){
		if(!isset($cookie)) return array();
		$res =explode('-', $cookie);
		
		$final = array(
			'itemN' => $res[0],
			'itemP' => $res[1],
			'itemS' => urldecode( $res[2] )
		);
		return $final;		
		
		}
       
       
     
   
   
   
   //Crude functions
	function exists_in_table($keyword){
		global $wpdb;
		//$wpdb = new wpdb( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST );
		$result = $wpdb->get_results( "SELECT keyword FROM wp_keywords_list where  keyword='$keyword'" );
		if(empty($result))
			return false;
		else 
			return true;
	}
	
		function exists_in_table_double($member,$key){
		global $wpdb;
		//$wpdb = new wpdb( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST );
		$result = $wpdb->get_results( "SELECT id FROM wp_kt_members where  member_id='$member' and key_id='$key'" );
		if(empty($result))
			return false;
		else 
			return true;
	}
	
	function export_csv($id){
		global $wpdb;
		$csv_array=array('Keyword', 'Status', 'Global Monthly Searches', 'Local Monthly Searches', 'CPC', 'Search Results', 'Actual Results', 'Average PR', 'Competition', 'Ads Displayed', 'Keyword Rating', 'Category','Price','.COM','.NET','.ORG'); 
		$result = $wpdb->get_results( "SELECT * FROM wp_kt_orders where  id='$id'", 'ARRAY_A' );
		extract($result[0]);
		$keys = explode(',' ,$key_ids);
		$str = implode(',' ,$csv_array)."\r\n";
		foreach($keys as $key){
				$result = $wpdb->get_results( "SELECT * FROM wp_keywords_list where  id='$key'", 'ARRAY_N' );
				$result = array_slice($result[0], 1, 18);
				 $result = $this ->reverse_process_array($result);
		unset($result[11]);
        unset($result[9]);
		unset($result[7]);
				$str .= implode(',',$result);
				$str .= "\r\n";
			}
		header('Content-type: text/csv');
		header("Content-disposition: attachment;filename=transaction-{$id}.csv");
		echo $str;
	
		}
		
		function ajax_display_table(){
			$id = $_REQUEST['class'];
			include 'simple-table.php';
			exit;
			}
	
	//Paypal Funcs
	function hash_call($price = '',$method =false, $payerId= false,$amount = null)
	{
		extract( get_option('kt-settings-var') );
	//declaring of global variables
	//global $API_Endpoint,$version,$API_UserName,$API_Password,$API_Signature,$nvp_Header, $subject, $AUTH_token,$AUTH_signature,$AUTH_timestamp;
	// form header string
	//$nvpheader=nvpHeader();
	//setting the curl parameters.
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$api_end);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);

	//turning off the server and peer verification(TrustManager Concept).
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_POST, 1);
	$check_page = urlencode( get_permalink($check_page));
	$token = urlencode( $_SESSION['TOKEN']);
	//var_dump($token);
	//exit;
	$amount = isset($amount)? $amount : 4;
	$setArray= array(
	'CANCELURL' => $check_page,
	'RETURNURL' => $check_page,
	'PAYMENTACTION' => 'Sale'	
	);
	
	$doArray = array(
			'PAYERID' => $payerId,
			'TOKEN' => $_SESSION['TOKEN'],
			'PAYMENTACTION' => 'Sale'
	);
	$common = array( 
	'USER' => $api_user,
	'PWD' => $api_pas,
	'SIGNATURE' => $api_sig,
	'VERSION' => '65.1',
	'METHOD' => $method,
	'L_NAME0' =>'Keywords',
	'L_AMT0' => $price,
	'L_QTY0' => 1,
	'AMT' => $price
	);
	switch($method):
	case 'SetExpressCheckout':
	$nvpreq = array_merge($common, $setArray);
	break;
	case 'DoExpressCheckoutPayment':
		$nvpreq = array_merge($common, $doArray);
	break;
	
	endswitch;
	
	
	
	$str = '';
	foreach ($nvpreq as $key=> $value)
		$str .= "&{$key}={$value}";
	$str = trim($str, '&');

	
	//setting the nvpreq as POST FIELD to curl
	curl_setopt($ch,CURLOPT_POSTFIELDS, $str);

	//getting response from server
	$response = curl_exec($ch);

	//convrting NVPResponse to an Associative Array
	$nvpResArray=$response;
	//$nvpReqArray=deformatNVP($nvpreq);




	return  $this -> deformatNVP( $nvpResArray );
	}
	
	function deformatNVP($nvpstr)
	{

	$intial=0;
 	$nvpArray = array();


	while(strlen($nvpstr)){
		//postion of Key
		$keypos= strpos($nvpstr,'=');
		//position of value
		$valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);

		/*getting the Key and Value values and storing in a Associative Array*/
		$keyval=substr($nvpstr,$intial,$keypos);
		$valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
		//decoding the respose
		$nvpArray[urldecode($keyval)] =urldecode( $valval);
		$nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
     }
	return $nvpArray;
	}	  


}


?>
