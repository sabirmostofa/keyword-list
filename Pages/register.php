<?php
if( get_option('kt-settings-var') )extract( get_option('kt-settings-var'));
$re = get_permalink($check_page) ;
?>
<center>
Don't Have an account?
	Creating an account takes only 10 secs. 

<div style=""> <!-- Registration -->
		<div id="">
		<div class="title">
			<h2>Register Your Account</h2>
			<span>You must create an account to complete your checkout.</span>
		</div><br>
			<form action="<?php echo site_url('wp-login.php?action=register', 'login_post') ?>" method="post">
		Username:	<input type="text" name="user_login" value="" id="user_login" class="input" /><br>
		E-mail :		<input type="text" name="user_email" value="" id="user_email" class="input"  /><br>
				<?php do_action('register_form'); ?>
				<input type='hidden' name='redirect_to' value="<?php echo $re ?>"/>
				<input style = 'margin-left:20px;' type="submit" value="Register" id="register" />
			<hr />
			

			</form>
		</div>
</div><!-- /Registration -->
</center>
