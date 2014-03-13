<?php

$carousel_query = Voce_Carousel::get_carousel_query();

if( !empty( $carousel_query ) && $carousel_query->have_posts() ): ?>
	<div id="page-carousel" class="carousel slide" data-ride="carousel">
		<!-- Indicators -->
		<ol class="carousel-indicators">
			<?php foreach ( $carousel_query->posts as $i => $post ) : ?>
				<li data-target="#page-carousel" data-slide-to="<?php echo esc_attr($i); ?>" <?php echo ( $i === 0 ) ? 'class="active"' : ''; ?>></li>
			<?php endforeach; ?>
		</ol>

		<div class="carousel-inner">
			<?php while ( $carousel_query->have_posts() ) : $carousel_query->the_post();
					$thumb_id  = get_post_thumbnail_id();
					$image_src = wp_get_attachment_image_src( $thumb_id, 'carousel-slide' );
					$image_url = $image_src ? $image_src[0] : '';
					$clickthru = get_post_meta( get_the_ID(), 'clickthru_url', true ) ?: false;
					$link_text = get_post_meta( get_the_ID(), 'clickthru_text', true ) ?: 'Read More';
				?>
				<div class="item<?php echo !$carousel_query->current_post ? ' active' : ''; ?>" style="background-image: url(<?php echo esc_url($image_url); ?>);">
					<div class="container">
						<div class="carousel-caption">
							<?php if ( $clickthru ) : ?>
								<h2><a href="<?php echo esc_url( $clickthru ); ?>"><?php the_title(); ?></a></h2>
							<?php else: ?>
								<h2><?php the_title(); ?></h2>
							<?php endif; ?>
							<p><?php the_excerpt(); ?></p>
							<?php if ( $clickthru ) : ?>
								<p class="text-center"><a href="<?php echo esc_url( $clickthru ); ?>" class="btn btn-carousel"><?php echo esc_html( $link_text ); ?></a></p>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endwhile; wp_reset_postdata(); ?>
		</div> <!-- end carousel-inner -->
	</div> <!-- end carousel -->
<?php endif;