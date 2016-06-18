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
	// Define the custom attachment for posts
	add_meta_box(
		'wp_custom_attachment',
		'Custom Attachment',
		'wp_custom_attachment',
		'frontpage_cpt',
		'side'
	);
}

add_action( 'admin_init', 'admin_init' );

/**
 * Meta box callback.
 */
function wp_custom_attachment() {

	wp_nonce_field( plugin_basename( __FILE__ ), 'wp_custom_attachment_nonce' );

	$html = '<p class="description">';
	$html .= 'Upload your PDF here.';
	$html .= '</p>';
	$html .= '<input type="file" id="wp_custom_attachment" name="wp_custom_attachment" value="" size="25" />';

	echo $html;

} // end wp_custom_attachment

/**
 * Save attachment.
 *
 * @param $id
 *
 * @return mixed
 */
function save_custom_meta_data( $id ) {

	/* --- security verification --- */
	if ( ! wp_verify_nonce( $_POST['wp_custom_attachment_nonce'], plugin_basename( __FILE__ ) ) ) {
		return $id;
	} // end if

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $id;
	} // end if

	if ( 'frontpage_cpt' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $id ) ) {
			return $id;
		} // end if
	} else {
		if ( ! current_user_can( 'edit_page', $id ) ) {
			return $id;
		} // end if
	} // end if
	/* - end security verification - */

	// Make sure the file array isn't empty
	if ( ! empty( $_FILES['wp_custom_attachment']['name'] ) ) {

		// Setup the array of supported file types. In this case, it's just PDF.
		$supported_types = array( 'application/pdf' );

		// Get the file type of the upload
		$arr_file_type = wp_check_filetype( basename( $_FILES['wp_custom_attachment']['name'] ) );
		$uploaded_type = $arr_file_type['type'];

		// Check if the type is supported. If not, throw an error.
		if ( in_array( $uploaded_type, $supported_types ) ) {

			// Use the WordPress API to upload the file
			$upload = wp_upload_bits( $_FILES['wp_custom_attachment']['name'], NULL, file_get_contents( $_FILES['wp_custom_attachment']['tmp_name'] ) );

			if ( isset( $upload['error'] ) && $upload['error'] != 0 ) {
				wp_die( 'There was an error uploading your file. The error is: ' . $upload['error'] );
			} else {
				add_post_meta( $id, 'wp_custom_attachment', $upload );
				update_post_meta( $id, 'wp_custom_attachment', $upload );
			} // end if/else

		} else {
			wp_die( "The file type that you've uploaded is not a PDF." );
		} // end if/else

	} // end if

} // end save_custom_meta_data
add_action( 'save_post', 'save_custom_meta_data' );

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
