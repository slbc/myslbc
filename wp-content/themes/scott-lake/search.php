<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package scott-lake
 */
get_header();
?>

<section>
	<div class="container">
		<div class="row">
			<div class="col-sm-8 col-lg-8" role="main" id="primary">

				<?php if ( have_posts() ) : ?>

					<header class="page-header">
						<h1 class="page-title"><?php printf( __( 'Search Results for: %s', 'scott_lake' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
					</header><!-- .page-header -->

					<?php /* Start the Loop */ ?>
					<?php while ( have_posts() ) : the_post(); ?>

						<?php get_template_part( 'content', 'search' ); ?>

					<?php endwhile; ?>

					<?php scott_lake_content_nav( 'nav-below' ); ?>

				<?php else : ?>

					<?php get_template_part( 'content', 'none' ); ?>

				<?php endif; ?>

			</div>
			<?php get_sidebar(); ?>
		</div>
	</div>
</section>

<?php
get_footer();