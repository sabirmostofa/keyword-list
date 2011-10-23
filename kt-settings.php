<?php
global $wpdb;

if(isset($_POST['main-submit'])):
	$_POST = array_map( create_function('$a', 'return trim($a);'), $_POST);
	extract($_POST);	
	update_option( 'kt-settings-var', array( 
	'key_page' => $key_page,
	'check_page' => $check_page,
	'mem_page' => $mem_page,
	'api_user' => $api_user,
	'api_pas' => $api_pas,
	'api_sig' => $api_sig,
	'api_end' => $api_end, 
	'api_url' => $api_url,
                  'aff_percent' => $aff_percent,
                   'aff_mail_sub' => $aff_mail_sub,
                   'aff_mail_body' => $aff_mail_body
	 ) );
	 if( ($table =$_POST['truncate']) != 'None' ){
		$wpdb->query("truncate table {$table}");
		echo "<div style=\"margin:10px 0\" class=\"updated\">Table \"{$table}\" has been Truncated </div>";
	}
	 if( ($table =$_POST['delete-tab']) != 'None' ){
		$wpdb->query("drop table {$table}");
		echo "<div style=\"margin:10px 0\" class=\"updated\">Table \"{$table}\" has been Deleted</div>";
	}

endif;
 

?>
<div class='wrap' style="margin-top:20px">
<?php 
//var_dump(get_option('kt-settings-var'));
if(isset($_POST['main-submit']))
	echo '<div style="margin:10px 0" class="updated">Settings has been Updated</div>';
?>
<form action ='' method='post'>
 Keywords Page:
<?php 
//var_dump( get_option('kt-settings-var'));
if( get_option('kt-settings-var') )extract( get_option('kt-settings-var'));
$aff_mail_sub = isset($aff_mail_sub)? $aff_mail_sub : 'Payment Received from Keywordsupplier';
$aff_mail_body = isset($aff_mail_body)? $aff_mail_body : 'You have received payment of $x from keywordsupplier.com as affiliate earning ';
wp_dropdown_pages( array( 'name' => 'key_page', 'selected' => $key_page )); 

 ?>
 <br/>
 <br/>
 
  Checkout Page :
<?php wp_dropdown_pages( array( 'name' => 'check_page','selected' => $check_page )); 
 $pages = get_all_page_ids() ;
 ?>
  <br/>
 <br/>
   Member Page :
<?php wp_dropdown_pages( array( 'name' => 'mem_page','selected' => $mem_page )); 
 $pages = get_all_page_ids() ;
 ?>
  <br/>
 <br/>
 <h4>Paypal Settings</h4>
 API_USERNAME
  <br/>
 <input style="width:40%" type='text' name='api_user' value="<?php echo $api_user ?>"/>
 <br/>
 <br/>
    API_PASS
   <br/>
 <input style="width:40%" type='password' name='api_pas' value="<?php echo $api_pas ?>"/>
 <br/>
 <br/>
  API_SIGNATURE
   <br/>
 <input style="width:40%" type='text' name='api_sig' value="<?php echo $api_sig ?>"/>
 <br/>
 <br/>
  API_ENDPOINT
   <br/>
 <input style="width:40%" type='text' name='api_end' value="<?php echo $api_end ?>"/>
 <br/>
 <br/>

  Paypal URL
   <br/>
 <input style="width:40%" type='text' name='api_url' value="<?php echo $api_url ?>"/>
 <br/>
 <br/>
 
 
  <h4>Affiliate Settings</h4>
     Affiliate Percentage(Only the number)
 <input style="width:40%" type='text' name='aff_percent' value="<?php echo $aff_percent ?>"/>
 <br/>
 <br/>
         Mail Subject:<br/>
        <input style="width:70%" type="text" name="aff_mail_sub" value="<?php echo $aff_mail_sub ?>"/>
        <br/>
        Mail Body:<br/>
        <textarea name="aff_mail_body" rows="8" cols="80">
        <?php echo trim($aff_mail_body) ?>
        </textarea>
  <h4>Additional Tools</h4>
 Truncate Any Table(Use this feature to wipe All data from a table):
 <select name='truncate'>
 <option selected="selected">None</option>
 <option>wp_keywords_list</option>
 <option>wp_kt_orders</option>
 <option>wp_kt_members</option>
 </select>
 <br/>
 <br/>
  Delete Any Table(Don't use this feature if you are not sure):
 <select name='delete-tab'>
 <option selected="selected">None</option>
 <option>wp_keywords_list</option>
 <option>wp_kt_orders</option>
 <option>wp_kt_members</option>
 </select>
 <br/>
 <br/>
 <input class='button-primary' type='submit' name="main-submit" value='Submit' >
 </form>
</div>
