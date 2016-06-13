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

	add_meta_box( $id = 'year_completed-meta', $title = 'Year Completed', $callback = 'year_completed', $screen = 'frontpage_cpt', $context = 'side', $priority = 'low', $callback_args = NULL );
	add_meta_box( $id = 'credits_meta', $title = 'Design & Build Credits', $callback = 'credits_meta', $screen = 'frontpage_cpt', $context = 'normal', $priority = 'low', $callback_args = NULL );
}

add_action( 'admin_init', 'admin_init' );

/**
 * Meta box callback.
 */
function year_completed() {

	global $post;
	$custom = get_post_custom( $post->ID );

	$year_completed = $custom['year_completed'][0];

	?>
	<label>Year:</label>
	<input name='year_completed' value='<?php echo $year_completed; ?>'/>
	<?php
}

/**
 * Meta box callback.
 */
function credits_meta() {

	global $post;
	$custom = get_post_custom( $post->ID );

	$designers = $custom['designers'][0];
	$developers = $custom['developers'][0];
	$producers = $custom['producers'][0];

	?>
	<p><label>Designed By:</label><br/>
		<?php wp_editor( $content = $designers, $editor_id = 'mettaabox_ID_stylee', $settings = array(
			'textarea_name' => 'designers',
			'media_buttons' => FALSE,
			'textarea_rows' => 5,

		) ); ?></p>
	<p><label>Built By:</label><br/>
		<textarea cols='50' rows='5' name='developers'><?php echo $developers; ?></textarea></p>
	<p><label>Produced By:</label><br/>
		<textarea cols='50' rows='5' name='producers'><?php echo $producers; ?></textarea></p>
	<?php
}

/**
 * Ensures meta box values to get saved correctly.
 */
function save_details() {

	global $post;

	update_post_meta( $post->ID, 'year_completed', $_POST['year_completed'] );
	update_post_meta( $post->ID, 'designers', $_POST['designers'] );
	update_post_meta( $post->ID, 'developers', $_POST['developers'] );
	update_post_meta( $post->ID, 'producers', $_POST['producers'] );
}

add_action( 'save_post', 'save_details' );
