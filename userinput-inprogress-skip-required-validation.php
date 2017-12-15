<?php
//https://github.com/gravityflow/gravityflowlibrary
/*
WHEN WOULD I USE THIS SNIPPET?
You have a large form with many fields that are required but you do not want to validate them during a user input step in progress.

HOW WOULD I USE THIS SNIPPET?
Copy and paste the code into your child theme's functions.php or place inside a custom functionality plugin.
Replace defined variables ($form_id, $step_id) with your workflow specific values for a user input step with save progress setting active.
*/

add_filter( 'gravityflow_validation_user_input',  'workflow_inprogress_bypass_required_fields', 10, 3 );

function workflow_inprogress_bypass_required_fields( $validation_result, $step, $new_status ) {

    $form_id = '123';
    $step_id = '45';

    if( ($validation_result['form']['id'] == $form_id && rgpost( 'step_id' ) == $step_id && $new_status == 'in_progress') ) {

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