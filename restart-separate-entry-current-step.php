<?php
/**
 * Plugin Name: Entry Current Step Restart
 * Description: Restart the current step of a different entry based on the entry ID value in a field of the current entry
 * Version: 1.0
 * Author: Steven Henty
 * Author URI: http://www.stevenhenty.com
 * License: GPL-3.0+
 *
 * Copyright 2019 Steven Henty
 *
 * Requires Gravity Flow 1.8.1+ and the Form Connector Extension 1.2.1+
 */

add_action(
	'gravityflow_loaded',
	function() {
		class Gravity_Flow_Entry_Step_Restart extends Gravity_Flow_Step {
			// Make this unique
			public $_step_type = 'entry_step_restart';

			/**
			* Returns the label for the step type.
			*
			* @return string
			*/
			public function get_label() {
				return 'Entry Step Restart';
			}

			public function get_settings() {
				$settings = array(
					'title'  => 'Entry Step REstart',
					'fields' => array(
						array(
							'name'     => 'restart_entry_field_id',
							'label'    => esc_html__( 'Entry ID Field', 'gravityflowrestartformstep' ),
							'type'     => 'field_select',
							'tooltip'  => __( 'Select the field which will contain the entry ID of the entry that will be restarted.', 'gravityflowformconnector' ),
							'required' => true,
						),
					),
				);

				return $settings;
			}

			/**
			* Process the step.
			*
			* @return bool Is the step complete?
			*/
			public function process() {
				$field_id      = $this->restart_entry_field_id;
				$entry         = $this->get_entry();
				$restart_entry = GFAPI::get_entry( $entry[ $field_id ] );
				$this->log_debug( __METHOD__ . '(): Starting process for entry #' . $this->restart_entry_id );

				if ( ! is_wp_error( $restart_entry ) ) {
					$this->log_debug( __METHOD__ . '(): Restarting step' );
					$api = new Gravity_Flow_API( $restart_entry['form_id'] );
					$api->restart_step( $restart_entry );
					return true;
				} else {
					$this->log_debug( __METHOD__ . '(): Error retrieving entry for step restart' );
					return true;
				}
			}
		}
		// Register the step
		Gravity_Flow_Steps::register( new Gravity_Flow_Entry_Step_Restart() );
	},
	200
);
