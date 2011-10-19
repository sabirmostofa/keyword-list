<?php 
/*
Template Name: wt-checkout
*/
if( get_option('kt-settings-var') )extract( get_option('kt-settings-var'));

get_header('wt'); ?>
<div class="col col_16">
          	<div class="indent-right">
	<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
				<?php the_content(); ?>
	<?php endwhile; ?>
    </div>
</div>
<!-- cart -->
<div id='cart-notify'>
	</div>
<div id="cart-wrapper">
	
	<div id="cart-1">
		<div class="ntr"><h2 style="display:inline">Shopping Cart</h2>
		</div>
		<a id='min-cart' style="" href='' >--</a>
	</div>
	<div id='cart-content'>
		<span style="font-weight:bold">Total Item(s): </span> <span id="totItem">0</span><br/>
		<span style="font-weight:bold">Total Price : </span><span id= "totPrice">0.00</span><br/>
		<a href='<?php echo get_permalink( $check_page)  ?>'>Checkout</a>
		<a style ="margin-left:20px" href='' id="cart-remove-all">Reset Cart</a>
	</div>
</div>

<?php get_footer('wt'); ?>
