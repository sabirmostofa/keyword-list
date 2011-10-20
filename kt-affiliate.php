<?php

/*
Affiliate Class
 */

class kt_affiliate{
    function __construct() {
        add_action('init', array($this,'track_aff'));
        add_action('user_register', array($this, 'save_aff') );
        add_action('kt_affdata_insert', array($this, 'affdata_insert') );
        
    }
    
    function track_aff(){        
        if( stripos ( $_SERVER['REQUEST_URI'], '/affiliates/' ) !== false ) :
            $set_array = get_option('kt-settings-var'); 
           if( preg_match('?/affiliates/(.*)?', $_SERVER['REQUEST_URI'],  $matches)){
              $pos_user = trim($matches[1], '/');
           }else return;
           
           $current_user = wp_get_current_user();
           
           if( 0 != $current_user -> ID){
               $current_user_login = $current_user -> user_login;
               if($current_user_login == $pos_user )wp_redirect( get_permalink($set_array['key_page']));
           }
           $pos_user = get_user_by('login', $pos_user);
           
           if( !$pos_user ) wp_redirect( get_permalink($set_array['key_page']));
                   
            setcookie( 'kt-affiliate-user',$pos_user -> ID , time()+3600*24*30 , '/' );
            wp_redirect( get_permalink($set_array['key_page']));
            exit;        
    endif;
    
    }
    
    function save_aff($user_id){
        if(isset($_COOKIE['kt-affiliate-user'])){            
            $aff = $_COOKIE['kt-affiliate-user'];
            add_user_meta($user_id, 'kt-affiliate', $aff );
        }        
    }
    
    function affdata_insert($price){
        $current_user = wp_get_current_user();
        $current_user_id = $current_user -> ID;
        $current_aff_income = get_user_meta($current_user_id, 'kt_aff_income');
        $aff_percent = '';
        
    }
    
}
?>
