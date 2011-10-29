<?php
global $wpKeywordsTable, $wpdb;
extract( get_option('kt-settings-var') );
if( isset($_COOKIE['cItems']) )
	extract( $wpKeywordsTable -> process_cookie($_COOKIE['cItems']) );

	$res =explode('-', $_COOKIE['cItems']);
	$all_res =trim ($res[2],',' ) ;	
	$all_res = $wpdb->get_results("SELECT * from wp_keywords_list where id in({$all_res})", 'ARRAY_N');


$table_heads = array('Keyword', 'Remove','Global Monthly Searches', 'Local Monthly Searches', 'CPC', 'Search Results', 'Actual Results', 'Average PR', 'Competition', 'Ads Displayed', 'Keyword Rating', 'Category','Price','.COM','.NET','.ORG'); 

//header('http://google.com');

//var_dump($wpKeywordsTable -> hash_call(20) );
//var_dump($_SESSION['final']);


//str = ($_SERVER['SERVER_NAME'] == 'localhost')? 'wordpress/' : '';
//$signup_url = 'http://' . $_SERVER['SERVER_NAME'].'/'.$str. 'wp-signup.php?redirect_to='.urlencode(get_permalink( $check_page ));
if( isset( $_GET['pass_reset']) && isset( $_GET['fail']) )
		echo "<div class='message'>Your password does not match, try again to reset you password</div>";
	elseif( isset( $_GET['pass_reset']) )
		echo "<div class='message'>Your password has been reset successfully.</div>";
		

if( !is_user_logged_in() && !isset($_SESSION['wt-current-user']) ){	
		
	?>
	<center><div class='message'>You must create an account quickly to continue with your checkout!</div></center>
	<br/> 
	<?php
	include 'register.php';
	include 'login.php';
	
	return;
	}
elseif( !is_user_logged_in() && isset($_SESSION['wt-current-user'])){
	?>
	<div class='message'>Please Check your Email for Password. You can change the password after you login.</div>
	<?php
	include 'login.php';
	return;
	}
	
?>


<script type="text/javascript" language="JavaScript">
<!--
function checkCheckBoxes(theForm) {
	if (
	theForm.CHECKBOX_1.checked == false) 
	{
		alert ('You Have No Agreed To Our Terms!');
		return false;
	} else { 	
		return true;
	}
}
//-->
</script> 





<?php
if( isset( $_SESSION['final']['ACK'] ) && $_SESSION['final']['ACK'] == 'Success'  ):
?>
<div class='message'>Your Purchase has been Completed Successfully. Check Your Member's Page To See Your Keywords!</div>
<a href="<?php echo get_permalink($key_page) ?>">Buy More</a>
<?php
session_unset();
else:

?>
<div style="width:600px;margin: 20px auto;font-size:16px; text-align:center; ">
<form action='' method='post' onsubmit='return checkCheckBoxes(this);' >
<textarea name="textarea" id="textarea" cols="70" rows="8"> <?php include('http://keywordsupplier.com/Agreement.txt') ?> </textarea>
<br>
	<p><input type="CHECKBOX" name="CHECKBOX_1" value="I Agree"><strong> I Agree</strong></p>

Total Amount: <span id='totNumber' style="color:green;font-size:20px"> <?php echo $itemN ?></span>
<br/>
Total Price:  <span id='totAm' style="color:green;font-size:20px"> $<?php echo $itemP ?> </span>
<br/>
<br/>

<input type='checkbox' id='add-key-ext' name='add-key-ext'/>  Add The LSI Keyword Extension Option
<br/>
Enter Coupon Code(Optional):
<input type="text" name="kt-coupon"/>
<br/>
<input type='hidden' name='paypal-hidden-submit' value='pay' />
<input type='image' src='https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif' value='Buy Now' name='paypal-buy-final'/>
</form>
</div>
</center>
<h3 style="font-size:20px;">Checkout Or edit your Order:</h3>
<?php endif; ?>
<!-- TABLE-->
<div style='font-size:15px;margin:20px auto'>You can check your previous orders or change your password on your <a href="<?php echo get_permalink($mem_page);  ?>" > Member's Page</div>
<table id='kt-main' class='widefat'>
	<thead>
		<tr>
			<?php 
			foreach($table_heads as $key => $head){			
					echo "<th>{$head}</th>" ;
			
			}
			?>
		</tr>
	</thead>
	<tbody>
	
	<?php
foreach( $all_res as $result){
		echo '<tr>';
		$class = 'm' . $result[0];		
		$result = array_slice($result, 1, 18);
		$result = $wpKeywordsTable -> reverse_process_array($result, 'checkout');
		unset($result[11]);
        unset($result[9]);
		unset($result[7]);
		foreach ($result as $res)echo "<td class=\"{$class}\">{$res}</td>";
		echo '</tr>';
		
	}
	?>

</tbody>

	<tfoot>
		<tr>
			<?php 
			foreach($table_heads as $key => $head){
					echo "<th>{$head}</th>" ;
			}			
			?>
		</tr>
	</tfoot>

</table>




