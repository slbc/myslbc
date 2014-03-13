<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?php wp_title( '|', true, 'right' ); ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="icon" type="image/x-icon" href="<?php bloginfo( 'stylesheet_directory' ); ?>/img/favicon.ico">

		<!--[if (gte IE 6)&(lte IE 8)]>
				<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/libs/selectivizr-min.js"></script>
			<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/libs/rem.min.js"></script>
		<![endif]-->
		<?php wp_head(); ?>
	</head>

	<body <?php body_class(); ?>>
		<?php do_action( 'before' ); ?>
		<header role="banner">
			<?php if(has_nav_menu('secondary')) : ?>
				<div class="navbar navbar-inverse" role="navigation">
					<div class="container">
						<form role="search" method="get" class="search-form" action="<?php echo site_url(); ?>">
							<label>
								<span class="screen-reader-text">Search for:</span>
								<input type="search" class="search-field" placeholder="Search …" value="" name="s" title="Search for:">
							</label>
							<input type="submit" class="search-submit" value="Search">
						</form>
						<?php
							wp_nav_menu( array(
								'theme_location' => 'secondary',
								'container' => '',
								'menu_class' => 'nav navbar-nav navbar-right',
								'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
								'depth' => 0,
							) );
							?>
						</div>
				</div>
			<?php endif; ?>
			
			<div class="navbar navbar-main" data-spy="affix" data-offset-top="55">
				<div class="navbar navbar-inner">
					<div class="container">
						<div class="navbar-header">
							<button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse">
								<span class="sr-only">Toggle navigation</span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</button>
							<a href="<?php echo site_url(); ?>" class="navbar-brand"><?php bloginfo( 'name' ); ?></a>
						</div>
						<?php if(has_nav_menu('primary')) : ?>
							<nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">
								<?php
								wp_nav_menu( array(
									'theme_location' => 'primary',
									'container' => '',
									'menu_class' => 'nav navbar-nav',
									'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
									'depth' => 0,
								) );
								?>
							</nav>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</header>
