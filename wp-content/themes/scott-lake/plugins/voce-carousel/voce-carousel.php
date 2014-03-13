<?php

class Voce_Carousel {

	const POST_TYPE = 'carousel';
	const MAX_CAROUSEL_ITEMS = 5; //may change to setting later

	public static function init() {
		if ( !class_exists('Voce_Settings_API') )
			return _doing_it_wrong( __CLASS__, 'Voce Settings API is required for the this plugin to work', null );

		if ( !class_exists('Post_Selection_Box') )
			return _doing_it_wrong( __CLASS__, 'Post Selection UI is required for the this plugin to work', null );

		self::register_post_type();
		self::add_setting();

		wp_register_script( 'manage-carousel', get_template_directory_uri() . '/plugins/voce-carousel/manage-carousel.js', array( 'jquery', 'wp-ajax-response' ), false, true );
		wp_register_script( 'edit-carousel', get_template_directory_uri() . '/plugins/voce-carousel/edit-carousel.js', array( 'jquery', 'wp-ajax-response' ), false, true );

		add_action( 'transition_post_status', array( __CLASS__, 'update_carousel_item_permalink' ), 10, 3 );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_carousel_meta_boxes' ), 10, 2 );
		add_action( 'save_post', array( __CLASS__, 'save_post' ) );
		add_action( 'wp_ajax_push_to_carousel', array( __CLASS__, 'ajax_push_to_carousel' ) );
		add_action( 'wp_ajax_quick_create_carousel_item', array( __CLASS__, 'ajax_quick_create_carousel_item' ) );

		add_filter( 'post-selection-ui-row-title', array( __CLASS__, 'psu_row_add_image' ), 10, 4 );
		add_filter( 'get_sample_permalink_html', array( __CLASS__, 'carousel_preview_button' ), 10, 4 );
	}

	public static function register_post_type() {
		register_post_type( self::POST_TYPE, array(
			'labels' => array(
				'name' => __( 'Carousel Items', 'manifest' ),
				'singular_name' => __( 'Carousel Item', 'manifest' ),
				'add_new' => __( 'Add New', 'manifest' ),
				'add_new_item' => __( 'Add New Carousel Item', 'manifest' ),
				'edit_item' => __( 'Edit Carousel Item', 'manifest' ),
				'new_item' => __( 'New Carousel Item', 'manifest' ),
				'all_items' => __( 'All Carousel Items', 'manifest' ),
				'view_item' => __( 'View Carousel Item', 'manifest' ),
				'search_items' => __( 'Search Carousel Items', 'manifest' ),
				'not_found' => __( 'No carousel Items found', 'manifest' ),
				'not_found_in_trash' => __( 'No Carousel Items found in Trash', 'manifest' ),
				'menu_name' => __( 'Carousel Items', 'manifest' )
			),
			'query_var' => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'hierarchical' => false,
			'public' => false,
			'show_ui' => true,
			'rewrite' => false,
			'has_archive' => true,
			'supports' => array( 'title', 'thumbnail', 'url', 'excerpt' ),
			'map_meta_cap' => true
		) );
	}

	public static function add_setting() {
		$carousel_items = 'carousel_items';
		add_metadata_group( $carousel_items, 'Carousel Items' );
		add_metadata_field( $carousel_items, 'carousel_item_ids', '', 'psu', array(
			'post_type' => self::POST_TYPE,
			'limit'     => 10
		) );
		add_post_type_support( 'page', $carousel_items );
	}

	public static function carousel_preview_button($return, $id, $new_title, $slug){
		$post = get_post($id);

		if($post->post_type != 'carousel')
			return $return;

		$permalink = home_url();
		$return = '';
		if ( 'publish' == $post->post_status ) {
			$view_post = get_post_type_object($post->post_type)->labels->view_item;
			$return .= "<span id='view-post-btn'><a href='$permalink' class='button' target='_blank'>$view_post on Homepage</a></span>\n";
		}
		return $return;
	}

	public static function psu_row_add_image($title, $post_id, $name, $args){
		if( $name == 'carousel_items[carousel_item_ids]' && has_post_thumbnail($post_id) ){
			$image_id = get_post_thumbnail_id($post_id);
			$src = wp_get_attachment_image_src($image_id, 'thumb-108');
			return sprintf('<p><a href="%s" target="_blank"><img src="%s" /></a>  %s</p>', get_edit_post_link($image_id, false), $src[0], $title);
		}
		return $title;
	}

	public static function save_post($post_id) {
		//clickthru url meta saving
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) )
			return $post_id;

		if( get_option( 'page_on_front' ) === $post_id )
			wp_cache_delete( 'carousel_query' );

		if( get_post_type( $post_id ) != self::POST_TYPE )
			return $post_id;

		if ( isset( $_REQUEST['carousel_data_nonce'] ) && wp_verify_nonce( $_REQUEST['carousel_data_nonce'], 'update_carousel_data' ) ) {
			$clickthru_url = esc_url_raw( $_REQUEST['clickthru_url'] );
			if ( empty( $clickthru_url ) || $clickthru_url == 'http://' ) {
				delete_post_meta( $post_id, 'clickthru_url' );
			} else {
				update_post_meta( $post_id, 'clickthru_url', $clickthru_url );
			}

			$clickthru_text = sanitize_text_field( $_REQUEST['clickthru_text'] );
			update_post_meta( $post_id, 'clickthru_text', $clickthru_text );
		}

		wp_cache_delete( 'carousel_query' );
	}

	//if post or story has carousel item created from it while in draft status, update the permalink after the item is published.
	public static function update_carousel_item_permalink($new_status, $old_status, $post){
		if($new_status == 'publish'){
			$carousel_item_id = get_post_meta($post->ID, 'carousel_item_created', true);
			if($carousel_item_id){
				if($carousel_item = get_post($carousel_item_id)){
					$clickthru_url = get_post_meta($carousel_item_id, 'clickthru_url', true);
					if($clickthru_url == $carousel_item->guid){
						update_post_meta($carousel_item_id, 'clickthru_url', get_permalink($post->ID));
					}
				}
			}
		}
	}

	//metabox display handling
	public static function add_carousel_meta_boxes($post_type, $post) {
		if( get_option('page_on_front') != $post->ID ){
			remove_meta_box( 'carousel_items', 'page', 'normal' );
		}
		if ( $post_type == self::POST_TYPE ) {
			//add clickthru url
			add_meta_box( 'carousel_data', 'Item Settings', array( __CLASS__, 'clickthru_url_metabox' ), $post_type );

			//add metabox to quickly add to selected list
			if($post->post_status == 'publish'){
				if ( current_user_can( 'manage_options' ) ) {
					wp_enqueue_script( 'manage-carousel' );
					add_meta_box( 'add_to_carousel', 'Add to Carousel', array( __CLASS__, 'add_to_carousel_metabox' ), null, 'side', 'low' );
				}
			}

		} elseif ( post_type_supports( $post_type, 'create_carousel_item' ) ) {
			add_meta_box('create_carousel_item', 'Create Carousel Item', array(__CLASS__, 'create_carousel_item_metabox'), null, 'side');
		}
	}

	public static function clickthru_url_metabox( $post ) {
		wp_enqueue_script('edit-carousel');
		$clickthru_url  = get_post_meta( $post->ID, 'clickthru_url', true ) ?: 'http://';
		$clickthru_text = get_post_meta( $post->ID, 'clickthru_text', true ) ?: 'Read More';
		?>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">Clickthru Text</th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><span>Clickthru Text</span></legend>
							<p class="description">The text displayed on the clickthru button.</p>
							<div id="clickthru_selection">
								<input type="text" id="clickthru_text" class="widefat" name="clickthru_text" value="<?php echo esc_attr( $clickthru_text ) ?>" class="widefat"/>
							</div>
						</fieldset>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Clickthru URL</th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><span>Clickthru URL</span></legend>
							<p class="description">Enter the location the user should be routed to when clicking the carousel item.</p>
							<div id="clickthru_selection">
								<input type="text" id="clickthru_url" class="widefat" name="clickthru_url" value="<?php echo esc_attr( $clickthru_url ) ?>" class="widefat"/>
								<p class="description">Or link to existing content</p>
								<?php
								echo post_selection_ui('clickthru_url_search', array(
									'post_type'   => apply_filters( 'voce_carousel_clickthru_post_types', array( 'post', 'page' ) ),
									'post_status' => array('publish'),
									'limit'       => 0,
									'selected'    => array(),
									'labels'      => array('name' => 'URL Target', 'singular_name' => 'URL Target'),
									'sortable'    => false,
									));
								?>
							</div>
						</fieldset>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
		wp_nonce_field( 'update_carousel_data', 'carousel_data_nonce' );
	}

	public static function add_to_carousel_metabox( $post ) {
		echo self::get_add_to_carousel_link( $post->ID );
	}

	public static function get_carousel_ids() {
		return array_map( 'intval', get_post_meta(get_option('page_on_front'), 'carousel_item_ids', get_option( 'page_on_front' ) ) );
	}

	public static function get_carousel_query( $query_args = array() ){
		$defaults = array(
			'post_type' => self::POST_TYPE,
			'posts_per_page' => self::MAX_CAROUSEL_ITEMS,
			'post_status' => 'publish',
			'post__in' => self::get_carousel_ids(),
			'orderby' => 'post__in',
			'no_found_rows' => true
		);

		$query_args = wp_parse_args( $query_args, $defaults );

		$query = wp_cache_get( 'carousel_query' );

		if( $query === false ){
			$query = new WP_Query( $query_args );

			if( is_wp_error( $query ) )
				return false;

			wp_cache_set( 'carousel_query', $query );
		}

		return $query;
	}

	public static function set_carousel_ids( $carousel_ids = array( ) ) {
		$carousel_ids = array_slice( $carousel_ids, 0, self::MAX_CAROUSEL_ITEMS );
		$carousel_ids = array_map( 'intval', $carousel_ids );

		update_post_meta( get_option( 'page_on_front' ), "carousel_item_ids", $carousel_ids );
	}

	private static function get_add_to_carousel_link( $post_id ) {
		$carousel_ids = self::get_carousel_ids();

		$current_position = array_search( $post_id, $carousel_ids );

		$html = '';
		if ( false !== $current_position ) {
			$html .= sprintf( '<p>Current Carousel Position: %1$d of %2$d</p>', $current_position + 1, count($carousel_ids) );
		}

		if ( 0 !== $current_position ) {
			$nonce_url = wp_nonce_url( admin_url( sprintf( 'admin-ajax.php?action=push_to_carousel&post_id=%1$d', $post_id ) ), sprintf( 'push_to_carousel_%1$d', $post_id ) );
			$html .= sprintf( '<a href="%1$s" id="push-to-carousel">Push to front of Carousel</a>', $nonce_url );
		}

		return $html;
	}

	public static function ajax_push_to_carousel() {
		if ( !isset( $_GET['_wpnonce'] ) || !isset( $_GET['post_id'] ) || !current_user_can( 'manage_options' ) ) {
			return;
		}

		$post_id = intval( $_GET['post_id'] );

		if ( !wp_verify_nonce( $_GET['_wpnonce'], 'push_to_carousel_' . $post_id ) ) {
			return;
		}

		$carousel_ids = self::get_carousel_ids();

		//take the one we want to push to the front out of the current list if it's already in there
		if ( false !== ($current_position = array_search( $post_id, $carousel_ids )) ) {
			array_splice( $carousel_ids, $current_position, 1 );
		}

		array_unshift( $carousel_ids, $post_id );

		self::set_carousel_ids( $carousel_ids );

		$response = array( 'success' => true, 'html' => self::get_add_to_carousel_link( $post_id ) );
		die( json_encode( $response ) );
	}

	public static function ajax_quick_create_carousel_item(){
		check_ajax_referer('quick_create_carousel_item');

		if(!isset($_REQUEST['post_id']))
			wp_die(-1);

		$post_id = (int) $_REQUEST['post_id'];
		$original_post = get_post( $post_id, ARRAY_A );
		$post = array(
			'post_type'   => self::POST_TYPE,
			'post_status' => 'publish',
			'post_title'  => $original_post['post_title'],
		);

		if( $new_id = wp_insert_post( $post ) ){
			//set original post as having already created a carousel item from so the link to create carousel item doesn't display anymore
			update_post_meta( $post_id, 'carousel_item_created', $new_id );
			$thumb_id = get_post_thumbnail_id( $post_id );
			if($thumb_id)
				update_post_meta( $new_id, '_thumbnail_id', $thumb_id );

			update_post_meta( $new_id, 'clickthru_url', get_permalink( $post_id ) );

			$response = new WP_Ajax_Response(array(
				'what'   => 'carousel_item',
				'action' => 'quick_create_carousel_item',
				'id'     => 1,
				'data'   => get_edit_post_link($new_id, false)
			));
			$response->send();
		}
	}

	public static function create_carousel_item_metabox( $post ){
		wp_enqueue_script( 'manage-carousel' );
		$carousel_item_id = get_post_meta($post->ID, 'carousel_item_created', true);
		if( $carousel_item_id && ( $carousel_item = get_post_status($carousel_item_id) ) ) {
			printf( '<p><strong>Carousel Item: </strong><a href="%s">%s</a></p>', get_edit_post_link( $carousel_item_id ), get_the_title( $carousel_item_id ) );
		} else {
			printf( '<p><a href="#" id="quick_create_carousel_item" data-nonce="%s">Create Carousel Item from %s</a></p>', wp_create_nonce( 'quick_create_carousel_item' ), get_post_type_object($post->post_type)->labels->singular_name );
		}
	}
}

add_action( 'init', array( 'Voce_Carousel', 'init' ) );