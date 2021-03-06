<?php

/*
Affiliate Class
 */

class kt_affiliate{
    function __construct() {
        add_action('init', array($this,'track_aff'));
        add_action('user_register', array($this, 'save_aff') );
        add_action('kt_affdata_insert', array($this, 'affdata_insert') );
        add_action('admin_menu', array($this,'CreateMenu'),50);
        
    }
    
    function track_aff(){        
        if( stripos ( $_SERVER['REQUEST_URI'], '/affiliates/' ) !== false ) :
            $set_array = get_option('kt-settings-var'); 
           if( preg_match('?/affiliates/(.*)?', $_SERVER['REQUEST_URI'],  $matches)){
              $pos_user = trim($matches[1], '/');
           }else return;
           
           $current_user = wp_get_current_user();
           $current_user_login = '';
           if( 0 != $current_user -> ID)
             $current_user_login = $current_user -> user_login;
             
             if($current_user_login == $pos_user )$this ->redirect_to_keypage(); 
           
           $pos_user = get_user_by('login', $pos_user);
           
           if( !$pos_user ) $this ->redirect_to_keypage(); 
                   
            setcookie( 'kt-affiliate-user',$pos_user -> ID , time()+3600*24*30 , '/' );
            $this ->redirect_to_keypage();      
    endif;
    
    }
    
    function redirect_to_keypage(){
         $set_array = get_option('kt-settings-var');
         wp_redirect( get_permalink($set_array['key_page']));
         exit;
        
    }
    
    function save_aff($user_id){
        if(isset($_COOKIE['kt-affiliate-user'])){            
            $aff = $_COOKIE['kt-affiliate-user'];
            if(!get_user_meta($user_id, 'kt-affiliate'))
                add_user_meta($user_id, 'kt-affiliate', $aff );
            if($all_affs = get_user_meta($aff,'kt-aff-users', true)){
                if(!in_array($user_id, $all_affs)){
                        $all_affs[]=$user_id;
                  update_user_meta($aff,'kt-aff-users',$all_affs);      
                } 
                
            }else{
                add_user_meta($aff, 'kt-aff-users', array($user_id));
            }
                    
                    
        }        
    }
    
    function affdata_insert($price,$order_id){
        global $wpdb;
        $current_user = wp_get_current_user();
        $current_user_id = $current_user -> ID;
          $opts = get_option('kt-settings-var');
          $aff_percent = $opts['aff_percent'];
        if( $aff = $this -> has_aff($current_user_id)){
            $aff_income = $price *($aff_percent/100);
            $wpdb -> query("insert into wp_kt_affs(user_id,referred_user_id,order_id, aff_income) values('$aff','$current_user_id','$order_id', '$aff_income')");
            
        }
            else return;
        //$current_aff_income = get_user_meta($current_user_id, 'kt_aff_income');
      
        
    }
    
    function CreateMenu(){
        add_submenu_page('tools.php','KT Affiliates','KT Affiliates','activate_plugins','wpKtAffs',array($this,'KTAffs'));
        }
     
        function KTAffs(){
            require_once 'kt-affs.php';
        }
        
        function has_aff($user_id){          
            return get_user_meta($user_id, 'kt-affiliate',true);
        }
        
        function hash_call($nvpstr){
            
                extract( get_option('kt-settings-var') );

                $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$api_end);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);

	//turning off the server and peer verification(TrustManager Concept).
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_POST, 1);
        
        curl_setopt($ch,CURLOPT_POSTFIELDS, $nvpstr);
        $response = curl_exec($ch);
        global $wpKeywordsTable;
         return $wpKeywordsTable -> deformatNVP($response);
        }
        
    
}
?>
