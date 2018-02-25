<?php
/*
Plugin Name: Organization User Fields
Description: Adds Department and Manager fields to the user profile.
Version: 1.0
Author: Steven Henty
Author URI: http://www.stevenhenty.com
License: GPL-3.0+

Copyright 2018 Steven Henty

*** Instructions ***
Use in merge tags e.g
{user:manager} {user:department}
{created_by:manager} {created_by:department}

Adjust the array of departments.
*/


add_action( 'show_user_profile', 'gravityflow_org_user_profile_fields' );
add_action( 'edit_user_profile', 'gravityflow_org_user_profile_fields' );

function gravityflow_org_user_profile_fields( $user ) {
	$departments = array(
		// key => label
		''          => 'Select one',
		'marketing' => 'Marketing',
		'finance'   => 'Finance',
		'it'        => 'IT',
		'sales'     => 'Sales',
	);

	?>
<h3>Organization Details</h3>

<table class="form-table">
	<tr>
		<th><label for="department">Department</label></th>
		<td>
			<select name="department" id="department">
				<?php
				$output = '';
				foreach ( $departments as $key => $department ) {
					$selected = selected( $key, get_user_meta( $user->ID, 'department', true ), false );
					$output .= "\t<option value='{$key}'{$selected}>" . esc_html( $department ) . "</option>\n";
				}
				echo $output;
				?>
			</select>

			<br />
			<span class="description">Select the user's department</span>
		</td>
	</tr>
	<tr>
		<th><label for="manager">Manager</label></th>
		<td>
			<?php
			$args = array(
				'name'     => 'manager_id',
				'id'       => 'manager',
				'selected' => get_user_meta( $user->ID, 'manager_id', true ),
				// Uncomment next line to display users from only one role
				//'role' => 'manager',
			);
			wp_dropdown_users( $args );
			?>
			<br/>
			<span class="description">Select a manager</span>
		</td>
	</tr>
</table>
<?php
}

add_action( 'personal_options_update', 'save_gravityflow_org_user_profile_fields' );
add_action( 'edit_user_profile_update', 'save_gravityflow_org_user_profile_fields' );

function save_extra_user_profile_fields( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}
	update_user_meta( $user_id, 'department', sanitize_key( $_POST['department'] ) );
	update_user_meta( $user_id, 'manager_id', absint( $_POST['manager_id'] ) );
}
