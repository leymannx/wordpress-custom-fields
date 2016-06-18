<?php

/*
Plugin Name: Front Page Custom Fields
Plugin URI: https://github.com/leymannx/wordpress-custom-fields.git
Description: This WordPress plugin some custom fields to a custom post type named "Front Page". Additionally it sets an WYSIWYG editor for some of these fields. Multi-language support enabled.
Version: 1.0
Author: Norman Kämper-Leymann
Author URI: http://berlin-coding.de
Text Domain: frontpage-fields
Domain Path: /lang
*/

/**
 * Adds custom fields (meta boxes) to Front Page CPT.
 */
function admin_init() {

	//	add_meta_box( $id = '', $title = '', $callback = '', $screen = '', $context = '' );
	add_meta_box( $id = 'quote_author_meta', $title = 'Quote Author', $callback = 'quote_author', $screen = 'frontpage_cpt', $context = 'side' );
	add_meta_box( $id = 'box_left_meta', $title = 'Left Box', $callback = 'left_box', $screen = 'frontpage_cpt', $context = 'normal' );
	add_meta_box( $id = 'box_right_meta', $title = 'Right Box', $callback = 'right_box', $screen = 'frontpage_cpt', $context = 'normal' );
	// Meta boxes for pages.
	add_meta_box( $id = 'page_sidebar-right', $title = 'Right Sidebar', $callback = 'page_right_sidebar', $screen = 'page', $context = 'normal' );
}

add_action( 'admin_init', 'admin_init' );

/**
 * Meta box callback.
 */
function quote_author() {

	global $post;
	$custom = get_post_custom( $post->ID );

	$quote_author = $custom['quote_author'][0];

	?>
	<textarea cols="30" rows="1" name="quote_author"><?php echo $quote_author; ?></textarea>
	<?php
}

/**
 * Meta box callback.
 */
function left_box() {

	global $post;
	$custom = get_post_custom( $post->ID );

	$box_title = $custom['left_box_title'][0];
	$box_content = $custom['left_box_content'][0];

	?>
	<p><label>Box Title</label><br/>
		<textarea cols="50" rows="1" name="left_box_title"><?php echo $box_title; ?></textarea></p>
	<p><label>Box Content</label><br/>
		<?php wp_editor( $content = $box_content, $editor_id = 'left_box_content', $settings = array(
			'textarea_name' => 'left_box_content',
			'media_buttons' => FALSE,
			'textarea_rows' => 20,

		) ); ?>
	</p>
	<?php
}

/**
 * Meta box callback.
 */
function right_box() {

	global $post;
	$custom = get_post_custom( $post->ID );

	$box_title = $custom['right_box_title'][0];
	$box_content = $custom['right_box_content'][0];

	?>
	<p><label>Box Title</label><br/>
		<textarea cols="50" rows="1" name="right_box_title"><?php echo $box_title; ?></textarea></p>
	<p><label>Box Content</label><br/>
		<?php wp_editor( $content = $box_content, $editor_id = 'right_box_content', $settings = array(
			'textarea_name' => 'right_box_content',
			'media_buttons' => FALSE,
			'textarea_rows' => 20,

		) ); ?>
	</p>
	<?php
}

/**
 * Meta box callback.
 */
function page_right_sidebar() {

	global $post;
	$custom = get_post_custom( $post->ID );

	$sidebar_content = $custom['sidebar'][0];

	wp_editor( $content = $sidebar_content, $editor_id = 'page_right_sidebar', $settings = array(
		'textarea_name' => 'sidebar',
		'media_buttons' => FALSE,
		'textarea_rows' => 20,

	) );

}

/**
 * Ensures meta box values to get saved correctly.
 */
function save_details() {

	global $post;

	update_post_meta( $post->ID, 'quote_author', $_POST['quote_author'] );
	update_post_meta( $post->ID, 'left_box_title', $_POST['left_box_title'] );
	update_post_meta( $post->ID, 'left_box_content', $_POST['left_box_content'] );
	update_post_meta( $post->ID, 'right_box_title', $_POST['right_box_title'] );
	update_post_meta( $post->ID, 'right_box_content', $_POST['right_box_content'] );
	update_post_meta( $post->ID, 'sidebar', $_POST['sidebar'] );
}

add_action( 'save_post', 'save_details' );
