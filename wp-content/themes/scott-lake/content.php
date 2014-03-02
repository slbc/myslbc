<?php
/**
 * @package scott-lake
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	if ( has_post_thumbnail() ) {
		echo '<div class="post-thumbnail">';
		the_post_thumbnail();
		echo '</div>';
	}
	?>
	<header class="entry-header">
		<h1 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h1>

		<?php if ( 'post' == get_post_type() ) : ?>
			<div class="entry-meta">
				<?php scott_lake_posted_on(); ?>
			</div><!-- .entry-meta -->
		<?php endif; ?>

	</header><!-- .entry-header -->

	<?php if ( is_search() ) : // Only display Excerpts for Search ?>
		<div class="entry-summary">
			<?php the_excerpt(); ?>
		</div><!-- .entry-summary -->
	<?php else : ?>
		<div class="entry-content">
			<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'scott_lake' ) ); ?>
			<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'scott_lake' ),
				'after' => '</div>',
			) );
			?>
		</div><!-- .entry-content -->
	<?php endif; ?>

	<footer class="entry-meta">
		<?php if ( 'post' == get_post_type() ) : // Hide category and tag text for pages on Search ?>
			<?php
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( __( ', ', 'scott_lake' ) );
			if ( $categories_list && scott_lake_categorized_blog() ) :
				?>
				<p class="cat-links">
					<?php printf( __( '<strong>Categories</strong>: %1$s', 'scott_lake' ), $categories_list ); ?>
				</p>
			<?php endif; // End if categories ?>

			<?php
			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list( '', __( ', ', 'scott_lake' ) );
			if ( $tags_list ) :
				?>
				<p class="tags-links">
					<?php printf( __( '<strong>Tags</strong>: %1$s', 'scott_lake' ), $tags_list ); ?>
				</p>
			<?php endif; // End if $tags_list ?>
		<?php endif; // End if 'post' == get_post_type() ?>

		<?php if ( !post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
			<p class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'scott_lake' ), __( '1 Comment', 'scott_lake' ), __( '% Comments', 'scott_lake' ) ); ?></p>
		<?php endif; ?>

		<?php edit_post_link( __( 'Edit', 'scott_lake' ), '<p class="edit-link">', '</p>' ); ?>
	</footer><!-- .entry-meta -->
</article><!-- #post-## -->
