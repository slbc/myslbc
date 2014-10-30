<?php

class Sermon_Post_Type {

	const NAME = 'sermon';

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
				'name' => 'Sermons',
				'singular_name' => 'Sermon',
				'add_new' => 'Add New',
				'add_new_item' => 'Add New Sermon',
				'edit_item' => 'Edit Sermon',
				'new_item' => 'New Sermon',
				'view_item' => 'View Sermon',
				'search_items' => 'Search Sermons',
				'not_found' => 'No Sermons found',
				'not_found_in_trash' => 'No Sermons found in Trash',
			),
			'public' => true,
			'has_archive' => true,
			'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
			'rewrite' => array(
				'slug' => 'sermons',
				'with_front' => false,
			)
		) );
	}

	public function add_meta_fields() {
		
	}

}
