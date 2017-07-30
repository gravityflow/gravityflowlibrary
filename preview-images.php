<?php
/*
Plugin Name: Preview Images in Gravity Flow
Description: Displays images uploaded in the File Upload field.
Version: 1.0
Author: Steven Henty
Author URI: http://www.stevenhenty.com
License: GPL-3.0+

Copyright 2017 Steven Henty
*/

function sh_preview_image( $value, $field, $entry, $form ) {

	// Enter your form ID here
	if ( $form['id'] != 23 ) {
		return $value;
	}

	// Enter the file upload field ID here
	if ( $field->id != 10 ) {
		return $value;
	}

	$api = new Gravity_Flow_API( $form['id'] );
	$step = $api->get_current_step( $entry );

	if ( empty( $step ) ) {
		return $value;
	}

	// Enter the step ID here
	if ( $step->get_id() == 46 ) {
		$value = '';
		$files_json = $entry[ $field->id ];
		$files = json_decode( $files_json );
		foreach ( $files  as $file ) {
			$value .= sprintf( '<img src="%s" style="max-width:300px;" /><a onclick="DeleteFile(0,10,this);" title="Delete file" alt="Delete file" href="javascript:void(0);">
<img style="margin-left:10px;" src="/wp-content/plugins/gravityforms/images/delete.png">
</a>', esc_url( $file ) );
		}
	}
	return $value;
}

add_filter( 'gform_entry_field_value', 'sh_preview_image', 10, 4 );
