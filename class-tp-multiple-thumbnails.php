<?php
/**
 * Plugin Name: Multiple thumbnails
 * Description: Allows multiple featured images, specified per post type.
 *
 * Plugin URI: https://github.com/trendwerk/multiple-thumbnails
 * 
 * Author: Trendwerk
 * Author URI: https://github.com/trendwerk
 * 
 * Version: 1.0.0
 */

/**
 * API
 */

/**
 * Get the ID of the post thumbnail
 * 
 * @param  id $post_id   
 * @param  string $thumbnail Thumbnail type
 * @return int|bool
 */
function tp_get_post_thumbnail_id( $post_id, $thumbnail ) {
	$thumbnail_ids = get_post_meta( $post_id, '_thumbnail_ids', true );

	if( isset( $thumbnail_ids[ $thumbnail ] ) )
		return $thumbnail_ids[ $thumbnail ];

	return false;
}

/**
 * Check if a thumbnail exists
 * 
 * @param  int $post_id
 * @param  string $thumbnail Thumbnail type
 * @return bool
 */
function tp_has_post_thumbnail( $post_id, $thumbnail ) {
	return (bool) tp_get_post_thumbnail_id( $post_id, $thumbnail );
}

/**
 * Get the post thumbnail HTML
 * 
 * @param  int $post_id
 * @param  string $thumbnail Thumbnail type
 * @param  string $size      
 * @param  string $attr
 * @return string Image output
 */
function tp_get_the_post_thumbnail( $post_id, $thumbnail, $size = 'thumbnail', $attr = '' ) {
	$thumbnail_id = tp_get_post_thumbnail_id( $post_id, $thumbnail );

	if( $thumbnail_id )
		return wp_get_attachment_image( $thumbnail_id, $size, false, $attr );

	return '';
}

function tp_the_post_thumbnail( $thumbnail, $size = 'thumbnail', $attr = '' ) {
	$post_id = get_the_ID();

	if( $post_id )
		echo tp_get_the_post_thumbnail( $post_id, $thumbnail, $size, $attr );
}

/**
 * Allow admins to select multiple featured images
 */
class TP_Multiple_Thumbnails {
	var $post_types = array();
	
	function __construct() {
		add_action( 'init', array( $this, 'init' ), 11 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		//AJAX
		add_action( 'wp_ajax_tp_multiple_thumbnails_set', array( $this, 'set_thumbnail' ) );
	}
	
	/**
	 * Initialize which post types want to be ordered
	 */
	function init() {
		$post_types = get_post_types();

		if( $post_types ) {
			foreach( $post_types as $post_type ) {
				if( ! post_type_supports( $post_type, 'thumbnails' ) )
					continue;
				
				$post_type = get_post_type_object( $post_type );
				if( ! isset( $post_type->thumbnails ) || ! is_array( $post_type->thumbnails ) )
					continue;

				$this->post_types[ $post_type->name ] = $post_type->thumbnails;
			}
		}
	}

	/**
	 * Add meta boxes
	 */
	function add_meta_boxes( $post_type ) {
		if( isset( $this->post_types[ $post_type ] ) && is_array( $this->post_types[ $post_type ] ) ) {
			foreach( $this->post_types[ $post_type ] as $thumbnail => $settings )
				add_meta_box( 'multiple-thumbnails-' . $thumbnail, $settings['label'], array( $this, 'meta_box' ), $post_type, 'side', 'low', $thumbnail );
		}
	}

	/**
	 * Display meta box
	 */
	function meta_box( $post, $meta_box ) {
		$thumbnail = $meta_box['args'];

		echo '<div class="tp-multiple-thumbnails-thumbnail" data-name="' . $this->post_types[ $post->post_type ][ $thumbnail ]['label'] . '" data-thumbnail="' . $thumbnail . '">';

		$thumbnail_ids = (array) get_post_meta( $post->ID, '_thumbnail_ids', true );
		
		if( ! isset( $thumbnail_ids[ $thumbnail ] ) )
			$thumbnail_ids[ $thumbnail ] = '';

		echo preg_replace( '/onclick="(.*?)"/is', '', _wp_post_thumbnail_html( $thumbnail_ids[ $thumbnail ], $post->ID ) );

		echo '</div>';
	}

	/**
	 * Enqueue scripts
	 */
	function enqueue_scripts() {
		wp_enqueue_script( 'tp-multiple-thumbnails', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_style( 'tp-multiple-thumbnails', plugins_url( 'assets/sass/admin.css', __FILE__ ) );
	}

	/**
	 * Set thumbnail
	 */
	function set_thumbnail() {
		$post_id = intval( $_POST['post_id'] );
		$thumbnail_id = intval( $_POST['thumbnail_id'] );
		$thumbnail = $_POST['thumbnail'];

		$thumbnail_ids = (array) get_post_meta( $post_id, '_thumbnail_ids', true );

		if( -1 == $thumbnail_id )
			unset( $thumbnail_ids[ $thumbnail ] );
		else
			$thumbnail_ids[ $thumbnail ] = $thumbnail_id;

		update_post_meta( $post_id, '_thumbnail_ids', $thumbnail_ids );

		wp_send_json( preg_replace( '/onclick="(.*?)"/is', '', _wp_post_thumbnail_html( $thumbnail_id, $post_id ) ) );
	}

} new TP_Multiple_Thumbnails;
