<?php
/*
Plugin Name: Schedule Step By Business Hours
Description: Adjust step schedule to start during business hours
Version: 1.0
Author: Jamie Oastler
Author URI: http://www.idealienstudios.com
License: GPL-3.0+

Copyright 2018 Jamie Oastler
*/

/*
*** Instructions ***
- Set step schedule with a default delay (5 minutes)
- Update the check of Step ID to match your use case
- Update the start_hour and end_hour

*** Note ***
This functionality does not take into consideration minutes, only the hours and day such that:
- If the workflow step was requested at 11:43PM on a Tuesday it would be scheduled to send out at 9:43AM Wednesday.
- If the workflow step was requested at 6:12AM on a Saturday It would be scheduled to send out at 9:12AM Monday.
*/

add_filter( 'gravityflow_step_schedule_timestamp', 'schedule_business_hours', 10, 3 );
function schedule_business_hours( $schedule_timestamp, $schedule_type, $step ) {

	//Ensure you are only adjusting the desired form/step
	if ( $step->get_id() !== 76 ) {
		return $schedule_timestamp;
	}

	gravity_flow()->log_debug( __METHOD__ . '(): Default Schedule: ' . date( 'Y-m-d H:i:s', $schedule_timestamp ) );

	//Define for your business irrespective of time-zone
	$start_hour = 9;
	$end_hour = 17;

	$current_timestamp = time();
	$tz = get_option('timezone_string');
	$dt = new DateTime("now", new DateTimeZone($tz));
	$dt->setTimestamp($current_timestamp);

	$current_hour = $dt->format('H');
	$current_day_of_week = $dt->format('N');

	//Modify the value of $current_timestamp if you want to test the step arriving separate from actual time.
	gravity_flow()->log_debug( __METHOD__ . '(): Comparing '. $current_hour . ' against ' . $start_hour . ' - ' . $end_hour);

	//Weekday + Business Hours
	if ( in_array( $current_day_of_week, array( 1, 2, 3, 4, 5 ) ) && $current_hour >= $start_hour && $current_hour <= $end_hour ) {
		gravity_flow()->log_debug( __METHOD__ . '(): Business Hour Request - Proceeding with default schedule');
		
		return $schedule_timestamp;
	}
	
	//Weekday + Before Business Hours
	if( in_array( $current_day_of_week, array( 1, 2, 3, 4, 5 ) ) && $current_hour <= $start_hour ) {
		$delay_hours = $start_hour - $current_hour;

	//Mon to Thurs + After Business Hours
	} else if( in_array( $current_day_of_week, array( 1, 2, 3, 4 ) ) && $current_hour >= $end_hour ) {
		$delay_hours = ( 23 - $current_hour ) + 1 + $start_hour;

	//Friday + After Business Hours
	} else if( in_array( $current_day_of_week, array( 5 ) ) && $current_hour >= $end_hour ) {
		$delay_hours = ( 23 - $current_hour ) + 1 + $start_hour + 48;

	//Saturday 
	} else if( in_array( $current_day_of_week, array( 6 ) ) ) {
		$delay_hours = ( 23 - $current_hour ) + 1 + $start_hour + 24;

	//Sunday
	} else if( in_array( $current_day_of_week, array( 7 ) ) ) {
		$delay_hours = ( 23 - $current_hour ) + 1 + $start_hour;

	//Edge case handling
	} else {
		$delay_hours = 0;
	}

	$delayed_timestamp = $schedule_timestamp + ( $delay_hours * 60 * 60 );

	$dt->setTimestamp($delayed_timestamp);

	gravity_flow()->log_debug( __METHOD__ . '(): Outside Business Hour Request');
	gravity_flow()->log_debug( __METHOD__ . '(): Delaying by ' . $delay_hours . ' hour(s) to ' . $dt->format('Y-m-d h:m:s') );
	return $delayed_timestamp;
}