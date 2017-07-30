<?php
/**
 * Plugin Name: Advanced Form Submission Step
 * Description: Provides custom logic for assigning a Form Submission step.
 * Version: 1.0
 * Author: Steven Henty
 * Author URI: http://www.stevenhenty.com
 * License: GPL-3.0+
 *
 * Copyright 2017 Steven Henty
 */

add_action( 'gravityflow_loaded', function () {
	class Gravity_Flow_Advanced_Form_Submission extends Gravity_Flow_Step_Form_Submission {
		public $_step_type = 'advanced_form_submission';

		public function get_settings() {

			$settings = parent::get_settings();

			// Remove the first four settings related to assignees.
			for ( $i = 0; $i <= 3; $i ++ ) {
				unset( $settings['fields'][ $i ] );
			}

			return $settings;
		}

		/**
		 * Returns the label for the step type.
		 *
		 * @return string
		 */
		public function get_label() {
			return 'Advanced Form Submission';
		}

		/**
		 * Return the assignees.
		 *
		 * @return Gravity_Flow_Assignee[]
		 */
		public function get_assignees() {

			// This is just an example.
			// Perform your logic to select assignee keys.

			$assignee_keys = array(
				'user_id|1',
			);

			foreach ( $assignee_keys as $assignee_key ) {

				// Check the user or role exists before adding.
				$this->maybe_add_assignee( $assignee_key );

			}

			return $this->_assignees;
		}
	}

	Gravity_Flow_Steps::register( new Gravity_Flow_Advanced_Form_Submission() );
}, 200 );
