<?php
/**
 * Plugin Name: Gravity Flow Library - Convenience Merge Tags
 * Description: Merge tags to make placing dynamic content in multiple locations related to multiple forms easier.
 * Version: 1.0
 * Author: Steven Henty
 * Author URI: https://gravityflow.io
 * License: GPL-3.0+
 *
 * Copyright 2023 Steven Henty
 *
 * Requires Gravity Flow 2.8.7+
 */

/*
MERGE TAG: {entry_status} DISPLAY THE CURRENT STEP NAME BASED ON AN ENTRY ID

WHEN WOULD I USE THIS SNIPPET?
You want to display the current step name of an entry based on the entry ID. It might be used in:
- An instruction field of Form A that used a Form Connector to create a related entry in Form B to show the status of Form B entry workflow.
- In a notification message to display the step name a past entry is on which customer is logging a ticket against
- In a fields default value to do a lookup at the time of form submission

HOW WOULD I USE THIS SNIPPET?
- Activate this plugin
- Put the merge tag into an appropriate location in one of two approaches:
     1) {entry_status: entry_id="123"} where 123 is that entry ID that you want the merge tag return the step name for 
     2) {entry_status: entry_id="{:15}"} if Field 15 in the form where the merge tag is being used has the entry ID you want the merge tag to return the step name for.
*/

add_filter( 'gform_replace_merge_tags', 'custom_merge_tag_entry_status', 10, 7 );
function custom_merge_tag_entry_status( $text, $form, $entry, $url_encode, $esc_html, $nl2br, $format ) {
 
	$matches = array();

	preg_match_all( '/{entry_status(:(.*?))?}/', $text, $matches, PREG_SET_ORDER );

	if ( ! empty( $matches ) ) {

		foreach ( $matches as $match ) {
			$full_tag       = $match[0];
			$options_string = isset( $match[2] ) ? $match[2] : false;
		}

		$attributes = shortcode_parse_atts( $options_string );
	
        if ( ! isset( $attributes['entry_id'] ) ) {
            return str_replace( $full_tag, '', $text );
        }
        
        $entry = GFAPI::get_entry(  $attributes['entry_id'] );
            
        if ( is_wp_error( $entry ) ) {
            return str_replace( $full_tag, '', $text );
        }
         
        $api = new Gravity_Flow_API( $entry['form_id'] );
        $step = $api->get_current_step( $entry );

        if ( ! $step ) {
            return str_replace( $full_tag, '', $text );
        }
        
        return str_replace( $full_tag, $step->get_name(), $text );
	}

	return $text;
}



/*
MERGE TAG: {step_context} - DISPLAY THE CURRENT STEP NAME BASED ON AN ENTRY ID

WHEN WOULD I USE THIS?
You want to display a table or image which represents the larger grouping of steps in a workflow and not have to modify the markup or image in every place you use it when the process or graphics need to change.

Examples:
- A job application that might have 20 steps in the workflow across 5 larger sections (Interview, HR Review, Hiring Manager, Confirm Salary, Employee Onboarding)

HOW WOULD I USE THIS?
- Customize the switch statement to include cases that match your process and images (or html markup) that match your branding.
- Activate this plugin
- Put the merge tag into an appropriate location with the phase of information to display such as {step_context: phase="interview"} 
    - In a notification message
    - In an instruction field of each step
*/

add_filter( 'gform_replace_merge_tags', 'custom_merge_tag_step_context', 10, 7 );
function custom_merge_tag_step_context( $text, $form, $entry, $url_encode, $esc_html, $nl2br, $format ) {
 
	$matches = array();

	preg_match_all( '/{step_context(:(.*?))?}/', $text, $matches, PREG_SET_ORDER );

	if ( ! empty( $matches ) ) {

		foreach ( $matches as $match ) {
			$full_tag       = $match[0];
			$options_string = isset( $match[2] ) ? $match[2] : false;
		}

		$attributes = shortcode_parse_atts( $options_string );
	
        if ( ! isset( $attributes['phase'] ) ) {
            return str_replace( $full_tag, '', $text );
        }
        
        //Don't want to use a hard-coded image path? Use https://developer.wordpress.org/reference/functions/wp_upload_dir/ for files from the media library.
        //Don't want to use images at all? Return an html fragment (a table?) with the different columns highlighted via inline CSS.

        $output = '';

        switch( strtolower( $attributes['phase'] ) ):
            case 'interview':
                $output = '<img src="https://example.com/wp-content/uploads/phase-interview.jpg"/>';
                break;

            case 'hr-review':
                $output = '<img src="https://example.com/wp-content/uploads/phase-hr-review.jpg"/>';
                break;
        endswitch;
        
        return str_replace( $full_tag, $output, $text );
	}

	return $text;
}