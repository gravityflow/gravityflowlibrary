<?php
/**
 * Plugin Name: Gravity Flow Library - Map email field to user field
 * Description: During form submission maps a user id into user field based on email field value
 * Version: 1.0
 * Author: Jamie Oastler
 * Author URI: http://idealienstudios.com
 * License: GPL-3.0+
 *
 * Copyright 2018 Jamie Oastler
 *
 * Requires Gravity Flow 1.8.1+
 */

/*
WHEN WOULD I USE THIS SNIPPET?
You want user, Gravity Forms CLI or other automation tool, to submit a form providing email address and have a step get assigned to their WordPress user.
If you were to assign the step directly to email field they must access it via token which is not associated to the WP User despite user profile matching email.

HOW WOULD I USE THIS SNIPPET?
Copy and paste the code into your child theme's functions.php or place inside a custom functionality plugin.
Create a form with email field and (likely) hidden user field.
Replace defined variables ($form_id, $email_field_id, $user_field_id) with your workflow specific values to map the email field value to a hidden user field
*/

add_action( 'gform_pre_submission', 'map_email_to_user_field', 10, 1 );

function map_email_to_user_field( $form ) {

	$form_id = '123';
	$email_field_id = '456';
	$user_field_id = '789';

	if ( $form['id'] == $form_id && null !== rgpost( 'input_' . $email_field_id ) ) {
		$user = get_user_by( 'email', rgpost( 'input_' . $email_field_id ) );
		if ( $user ) {
			$_POST[ 'input_' . $user_field_id ] = $user->ID;
		}
	}
}