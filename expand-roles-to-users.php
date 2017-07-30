<?php
/*
Plugin Name: Expand Roles to Users
Description: Converts Role Assignees to Users
Version: 1.0
Author: Steven Henty
Author URI: http://www.stevenhenty.com
License: GPL-3.0+

Copyright 2017 Steven Henty
*/


/**
 * Expands a role into individual user assignees.
 *
 *
 * @var Gravity_Flow_Assignee[] $assignees
 * @var Gravity_Flow_Step       $step
 *
 * @return Gravity_Flow_Assignee[]
 */
function sh_gravityflow_step_assignees( $assignees, $step ) {

	// Replace the step ID
	if ( $step->get_id() != 237 ) {
		return $assignees;
	}

	/* @var Gravity_Flow_Assignee[] $new_assignees */

	$new_assignees = array();
	foreach ( $assignees as $key => $assignee ) {
		if ( $assignee->get_type() == 'role' ) {

			$users = get_users( array(
					'role__in' => $assignee->get_id(),
				)
			);

			foreach ( $users as $user ) {
				$new_assignees[] = new Gravity_Flow_Assignee( 'user_id|' . $user->ID, $step );
			}
			unset( $assignees[ $key ] );
		}
	}

	if ( ! empty( $new_assignees ) ) {
		$assignees = array_merge( $assignees, $new_assignees );
	}

	return $assignees;
}

add_filter( 'gravityflow_step_assignees', 'sh_gravityflow_step_assignees', 10, 2 );
