<?php

class Ministry_Post_Type {

	const NAME = 'ministry';

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
				'name' => 'Ministries',
				'singular_name' => 'Ministry',
				'add_new' => 'Add New',
				'add_new_item' => 'Add New Ministry',
				'edit_item' => 'Edit Ministry',
				'new_item' => 'New Ministry',
				'view_item' => 'View Ministry',
				'search_items' => 'Search Ministries',
				'not_found' => 'No Ministries found',
				'not_found_in_trash' => 'No Ministries found in Trash',
			),
			'public' => true,
			'has_archive' => true,
			'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
			'rewrite' => array(
				'slug' => 'ministries',
				'with_front' => false,
			)
		) );
	}

	public function add_meta_fields() {
		
	}

}
