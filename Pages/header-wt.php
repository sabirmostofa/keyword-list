<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<title>
	<?php if ( is_tag() ) {
			echo 'Tag Archive for &quot;'.$tag.'&quot; | '; bloginfo( 'name' );
		} elseif ( is_archive() ) {
			wp_title(); echo ' Archive | '; bloginfo( 'name' );
		} elseif ( is_search() ) {
			echo 'Search for &quot;'.wp_specialchars($s).'&quot; | '; bloginfo( 'name' );
		} elseif ( is_home() ) {
			bloginfo( 'name' ); echo ' | '; bloginfo( 'description' );
		}  elseif ( is_404() ) {
			echo '404 Not Found | '; bloginfo( 'name' );
		} else {
			echo wp_title( ' | ', false, right ); bloginfo( 'name' );
		} ?>
	</title>
	<!-- While these meta keywords are not ideal and the meta description could be better, they are better than nothing -->
	<meta name="keywords" content="<?php bloginfo( 'name' ); echo ' , '; bloginfo( 'description' ); ?>" />
	<meta name="description" content="<?php bloginfo( 'description' ); ?>" />
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="index" title="<?php bloginfo( 'name' ); ?>" href="<?php echo get_option('home'); ?>/" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo( 'name' ); ?>" href="<?php bloginfo( 'rss2_url' ); ?>" />
	<link rel="alternate" type="application/atom+xml" title="<?php bloginfo( 'name' ); ?>" href="<?php bloginfo( 'atom_url' ); ?>" />
	<link rel="canonical" href="<?php echo curPageURL(); ?>" />
	<!--
		optional HTML 5 Shim
		Fixes the new HTML 5 elements (article, header, etc.) for IE8 and below
		http://code.google.com/p/html5shim/
	-->

	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo plugins_url().'/keywords-table/css/style_front.css' ?>" />
	<!--
	Pulls the latest version of jQuery from Google's CDN
	http://code.google.com/apis/libraries/-->
	
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js" type="text/javascript"></script>
	<script src="<?php echo plugins_url().'/keywords-table/js/script_front.js' ?>" type="text/javascript"></script>

	<script type="text/javascript">
	var ktSettings = {
	ajaxurl: "http://keywordsupplier.com/wp-admin/admin-ajax.php",
	pluginurl: "http://keywordsupplier.com/wp-content/plugins/wp-keywords-table/"
	
	}

	</script>
    <!-------wp_head------------------------------------------->
    
	
    
  
    



	
	  <!-------wp_head------------------------------------------->
</head>
<?
/* This code retrieves all our admin options. */
global $options;
foreach ($options as $value) {
	if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}
?>
<body <?php body_class(); ?> >
 <div id="main">
  	<!-- header -->
    <header>
    	<!-- .logo -->
    	<div class="logo">
      		<?php if (get_theme_mod_tm('logo_image', '')) { ?>
                                <a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
                                    <img src="<?php echo get_theme_mod_tm('logo_image', '') ?>" title="" alt="" />
                                </a>
                            <?php } else { ?>
                                <a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
                                    <img src="<?php bloginfo('stylesheet_directory'); ?>/images/logo.png" title="" alt="" />
                                </a>
                            <?php } ?>
      	</div>
    	<!-- /.logo -->
      

      	<?php if (!(current_user_can('level_0'))){ 
			
			extract( get_option('kt-settings-var') );
			?>

			        <form action="<?php echo get_option('home'); ?>/wp-login.php" id="login-form" method="post">

		        <fieldset>

                <a href="http://keywordsupplier.com/checkout/">Register</a> - 

        			User Login:&nbsp; <input type="text" value="username" name="log" id="log" onFocus="if(this.value=='username'){this.value=''}" onBlur="if(this.value==''){this.value='username'}">

		            <input type="password" name="pwd" id="pwd" />
		            <input type="hidden" name="redirect_to" value="<?php echo get_permalink($check_page) ?>" />

        		    <input type="submit" value="Go"  name="submit" />
        		    

		        </fieldset>    
		    </form>
		<?php } else { ?> <?php ?>
			<div id="if-logged-in">
				<div class="container">
					<p class="right">
                    <iframe src="http://www.facebook.com/plugins/like.php?app_id=260106047334703&amp;href=http%3A%2F%2Fkeywordsupplier.com&amp;send=false&amp;layout=button_count&amp;width=80&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:80px; height:21px;" allowTransparency="true"></iframe>
						<a href="<?php echo wp_logout_url(); ?>">Logout</a>
						<strong><a href="<?php bloginfo('url'); ?>/checkout/">Checkout</a></strong>
						<strong><a href="<?php bloginfo('url'); ?>/members/">My Account</a></strong>
					</p>
				</div>
			</div><!--#if-logged-in-->

		<?
			}
		?>
			<form id="ClockForm" action="">
		        <fieldset>
        		  <input type="text" id="clock" />
		        </fieldset>
		   </form>
      <nav>
	    <?php wp_nav_menu( array('menu' => 'Main Menu' )); ?>
      </nav>
      <!-- .phone number -->
      <div class="phone">
      
	<?php
		if ($tm_phone_number != "") { ?>
    	    	//<? echo $tm_phone_number; ?>

	<?php } 
	
	?>
    
      </div>
      <!-- /.phone number -->
      <!-- .twitter-link -->
      <div class="twitter-link">
      <?php
		if ($tm_twitter != "") { ?>
    	    	<a href="<? echo $tm_twitter; ?>" class="normaltip" title="Follow us on twitter">Follow</a>
	  <?php } 
	
	?>
      </div>
      <!-- /.twitter-link -->
    </header>
    
     <section id="content">
      
      <?php if ( is_home() || is_front_page() ): ?>
			
			<?php if ( is_active_sidebar( 'main-banner-widget-area' ) ) : ?>
				<?php dynamic_sidebar( 'main-banner-widget-area' ); ?>
			<?php endif; ?>
			
			
        <?php $content_block = new WP_Query(array('post_type'=>'subbanners', 'posts_per_page'=>4, 'order'=>'ASC'))?>

        <aside class="top">
	    	<ul class="banners">
			<?php while($content_block->have_posts()): $content_block->the_post(); ?>
				<li>	
					<?php the_content();?>
	    	    </li>
			<?php endwhile; ?>
          </ul>
      	</aside>  
		<?php endif; ?>
		<div class="content-box">
      	<div class="wrapper">
        
