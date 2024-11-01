<?php

// Prevent direct access to the plugin file
defined('ABSPATH') || exit;

function registration_ajax()
{
    if (isset($_POST['register_data']) && !empty($_POST['register_data'])) {
        // Unslash and sanitize the nonce field
        $nonce = isset($_POST['register_form_nonce']) ? sanitize_text_field(wp_unslash($_POST['register_form_nonce'])) : '';

        if (wp_verify_nonce($nonce, 'register_form_action')) {
            // Unslash and sanitize registration data
            $register_data_serial = sanitize_text_field(wp_unslash($_POST['register_data']));
            parse_str($register_data_serial, $register_data);

            // Sanitize individual fields
            $company_name = isset($register_data['company_name']) ? sanitize_text_field($register_data['company_name']) : '';
            wp_send_json_success(['company_name' => $company_name]);
        } else {
            wp_send_json_error(['message' => 'Nonce verification failed.']);
        }
    } else {
        wp_send_json_error(['message' => 'No registration data received.']);
    }
    wp_die();
}
add_action('wp_ajax_registration_ajax', 'registration_ajax');
add_action('wp_ajax_nopriv_registration_ajax', 'registration_ajax');
