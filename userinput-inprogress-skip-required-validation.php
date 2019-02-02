<?php
/**
 * Plugin Name: Gravity Flow Library - User Input skip required validation for in progress
 * Description: Bypass required field validation on defined form/fields when saving in progress user input step
 * Version: 1.0
 * Author: Steven Henty
 * Author URI: https://gravityflow.io
 * License: GPL-3.0+
 *
 * Copyright 2019 Steven Henty
 *
 * Requires Gravity Flow 1.8.1+
 */
/*
WHEN WOULD I USE THIS SNIPPET?
You have a large form with many fields that are required but you do not want to validate them during a user input step in progress.

HOW WOULD I USE THIS SNIPPET?
- Activate this plugin
- Replace defined variables ($form_id, $step_id) with your workflow specific values for a user input step with save progress setting active.
*/

add_filter( 'gravityflow_validation_user_input',  'workflow_inprogress_bypass_required_fields', 10, 3 );

function workflow_inprogress_bypass_required_fields( $validation_result, $step, $new_status ) {

	$form_id = '123';
	$step_id = '45';

	if ( ( $validation_result['form']['id'] == $form_id && rgpost( 'step_id' ) == $step_id && $new_status == 'in_progress') ) {

		$form = $validation_result['form'];

		foreach ( $form['fields'] as &$field ) {
			$field->failed_validation = false;
			$field->validation_message = '';
		}

		$validation_result = array(
			'is_valid' => true,
			'form'     => $form,
		);

	}

	return $validation_result;
}
