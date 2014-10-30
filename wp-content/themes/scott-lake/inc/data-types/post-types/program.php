<?php

class Program_Post_Type {

	const NAME = 'program';

	public function setup() {
		add_action( 'init', array( $this, '_on_init' ) );
	}

	public function _on_init() {
		$this->register_post_type();
		$this->add_meta_fields();
	}

	public function register_post_type() {
		register_post_type( self::NAME, array(
			'labels' => array(
				'name' => 'Programs',
				'singular_name' => 'Program',
				'add_new' => 'Add New',
				'add_new_item' => 'Add New Program',
				'edit_item' => 'Edit Program',
				'new_item' => 'New Program',
				'view_item' => 'View Program',
				'search_items' => 'Search Programs',
				'not_found' => 'No Programs found',
				'not_found_in_trash' => 'No Programs found in Trash',
			),
			'public' => true,
			'has_archive' => true,
			'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
			'rewrite' => array(
				'slug' => 'programs',
				'with_front' => false,
			)
		) );
	}

	public function add_meta_fields() {
		
	}

}
