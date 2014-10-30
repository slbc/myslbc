<?php

$post_types = array(
	'Post',
	'Sermon',
	'Program',
	'Ministry',
	'Person'
);

foreach ( $post_types as $post_type ) {
	$filename = strtolower( sanitize_title_with_dashes( $post_type ) );
	$class = str_replace( '-', '_', sanitize_title_with_dashes( $post_type ) ) . '_Post_Type';
	if ( file_exists( __DIR__ . "/{$filename}.php" ) ) {
		include(__DIR__ . "/{$filename}.php");
	} elseif ( file_exists( __DIR__ . "/{$filename}/{$filename}.php" ) ) {
		include(__DIR__ . "/{$filename}/{$filename}.php");
	} else {
		continue;
	}

	$handler = new $class();
	$handler->setup();
}