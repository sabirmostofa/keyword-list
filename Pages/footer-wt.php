<?
/* This code retrieves all our admin options. */
global $options;
foreach ($options as $value) {
	if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}
?>
</div>
      </div>
      <!-- /.content-box -->
	</section>
    <!-- aside -->
    <aside class="bottom">
      <div class="inside">
        <?php if ( ! dynamic_sidebar( 'Footer' ) ) : ?>
		<?php endif ?>
      </div>
    </aside>
  </div>
  <footer>
    <div class="container">
      <div class="wrapper">
        <div class="fright">
         <span><?php bloginfo('name'); ?></span> &copy; <?php echo date("Y") ?> . All Rights Reserved.
        </div>
        <nav>
        <?php
            
			change_menu_class('footer');
			
			?>
         <?php wp_nav_menu( array('menu' => 'Footer Menu' )); ?>
        </nav>
      </div>
    </div>
  </footer>
  <!-- trigger --->
<?php  if ($tm_dev_pages_disable != "false") { ?>
  <div id="advanced">
  	<span class="trigger"></span>
 <?php
            
			function dev_wp_nav_menu_args( $args = '' )
			{
				$args['menu_class'] = '';
				return $args;
			} 

			add_filter( 'wp_nav_menu_args', 'dev_wp_nav_menu_args' );
			
			?>
         <?php wp_nav_menu( array('menu' => 'Dev pages menu' )); ?>


  <?php } ?>


<?php //wp_footer(); ?>
</body>
</html>
