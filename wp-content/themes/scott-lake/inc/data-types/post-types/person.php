<?php

class Person_Post_Type {

	const NAME = 'person';

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
				'name' => 'People',
				'singular_name' => 'Person',
				'add_new' => 'Add New',
				'add_new_item' => 'Add New Person',
				'edit_item' => 'Edit Person',
				'new_item' => 'New Person',
				'view_item' => 'View Person',
				'search_items' => 'Search People',
				'not_found' => 'No People found',
				'not_found_in_trash' => 'No People found in Trash',
			),
			'public' => true,
			'has_archive' => true,
			'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
			'rewrite' => array(
				'slug' => 'people',
				'with_front' => false,
			)
		) );
	}

	public function add_meta_fields() {
		
	}

}
