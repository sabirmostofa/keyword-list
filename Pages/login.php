<?php
if( get_option('kt-settings-var') )extract( get_option('kt-settings-var'));
$re = get_permalink($check_page) ;

 ?>
 <center><br/>
 Already have an account? Please login to complete your purchase.
 <br/>
<div style=""> <!-- Login form-->
		<div id="">
		<div class="title">
			<h3>Login to your Account</h3>
		</div>
			<form action="<?php echo site_url("wp-login.php") ?>" method="post">
			<input type="text" name="log" value="Username" id="user_login" class="input" />
			<input type="password" name="pwd" value="Password" id="user_pass" class="input"  />
				<input type='hidden' name='redirect_to' value="<?php echo $re ?>"/>
				<?php do_action('login_form'); ?>
				<input type="submit" value="Login" id="login" />
	

			</form>
		</div>
</div>
</center>
