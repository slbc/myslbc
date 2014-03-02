<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package scott-lake
 */
?>
<aside class="col-sm-4 col-lg-4" role="complementary" id="secondary">
	<?php do_action( 'before_sidebar' ); ?>
	<?php if ( !dynamic_sidebar( 'sidebar-1' ) ) : ?>

		<aside id="archives" class="widget">
			<h3><?php _e( 'Archives', 'scott_lake' ); ?></h3>
			<ul>
				<?php wp_get_archives( array( 'type' => 'monthly' ) ); ?>
			</ul>
		</aside>

	<?php endif; // end sidebar widget area ?>
</aside><!-- #secondary -->
