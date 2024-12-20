<?php

// Prevent direct access to the plugin file
defined('ABSPATH') || exit;

function registration_ajax()
{
    // Check if registration data is received
    if (isset($_POST['register_data']) && !empty($_POST['register_data'])) {
        // Unslash and sanitize the nonce field
        $nonce = isset($_POST['register_form_nonce']) ? sanitize_text_field(wp_unslash($_POST['register_form_nonce'])) : '';

        // Verify nonce for security
        if (wp_verify_nonce($nonce, 'register_form_action')) {
            // Unslash and sanitize registration data
            $register_data_serial = wp_unslash($_POST['register_data']);
            parse_str($register_data_serial, $register_data);

            // Sanitize individual fields
            $fields = [
                'company_name',
                'kvk_number',
                'btw_id',
                'bank_account',
                'acc_holder_name',
                'street',
                'house_no',
                'postcode',
                'place',
                'land',
                'phone',
                'phone2',
                'email',
                'invoice_email',
                'contact_person',
                'first_name',
                'middle_name',
                'last_name',
                'contact_phone',
                'contact_email',
                'password',
                'confirm_password',
                'found_via',
            ];

            foreach ($fields as $field) {
                $$field = isset($register_data[$field]) ? sanitize_text_field($register_data[$field]) : '';
            }

            // Generate username from first and last name
            $username_base = sanitize_user($first_name . '_' . $last_name);
            $random_number = wp_rand(1000, 9999);
            $username = $username_base . '_' . $random_number;

            // Check if email already exists
            if (email_exists($contact_email)) {
                wp_send_json_error(['email_error' => 'Email is already registered.']);
            } elseif ($password !== $confirm_password) {
                wp_send_json_error(['password_error' => 'Passwords do not match.']);
            } else {
                if (strlen($password) < 8) {
                    wp_send_json_error(['password_error' => 'Password must be at least 8 characters long.']);
                } else {
                    // Create the user
                    $user_id = wp_create_user($username, $password, $contact_email);
                    if (is_wp_error($user_id)) {
                        wp_send_json_error(['message' => $user_id->get_error_message()]);
                    } else {
                        // Set user role to 'customer' and mark as pending
                        $user = new WP_User($user_id);
                        $user->set_role('customer');

                        // Set user as pending approval
                        update_user_meta($user_id, 'is_approved', 0); // 0 = pending, 1 = approved

                        // Update user first and last names
                        wp_update_user([
                            'ID' => $user_id,
                            'first_name' => $first_name,
                            'last_name' => $last_name,
                        ]);

                        // Update user meta fields
                        foreach ($fields as $field) {
                            if ($field !== 'password' && $field !== 'confirm_password') {
                                update_user_meta($user_id, $field, $$field);
                            }
                        }

                        // Handle file upload
                        if (isset($_FILES['upload_docs']) && $_FILES['upload_docs']['error'] === UPLOAD_ERR_OK) {
                            $file = $_FILES['upload_docs'];

                            // Validate the uploaded file
                            $allowed_types = ['application/pdf', 'image/jpeg', 'image/png']; // Add more MIME types as needed
                            if (!in_array($file['type'], $allowed_types)) {
                                wp_send_json_error(['upload_error' => 'Invalid file type.']);
                            }

                            // Move the uploaded file to a designated directory
                            $upload_dir = wp_upload_dir();
                            $target_file = $upload_dir['path'] . '/' . basename($file['name']);

                            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                                // File uploaded successfully
                                $uploaded_file_url = $upload_dir['url'] . '/' . basename($file['name']);

                                // Prepare the attachment array
                                $attachment = [
                                    'guid'           => $uploaded_file_url,
                                    'post_mime_type' => $file['type'],
                                    'post_title'     => sanitize_file_name(basename($file['name'])),
                                    'post_content'   => '',
                                    'post_status'    => 'inherit',
                                ];

                                // Insert the attachment into the WordPress database
                                $attachment_id = wp_insert_attachment($attachment, $target_file);

                                // Include the required file for image manipulation
                                require_once(ABSPATH . 'wp-admin/includes/image.php');

                                // Generate attachment metadata and update the database record
                                $attachment_data = wp_generate_attachment_metadata($attachment_id, $target_file);
                                wp_update_attachment_metadata($attachment_id, $attachment_data);
                            } else {
                                wp_send_json_error(['upload_error' => 'File upload failed.']);
                            }
                        }

                        // Store the uploaded file URL in user meta
                        update_user_meta($user_id, 'upload_docs', $uploaded_file_url);

                        wp_send_json_success(['message' => 'Registration successful! Your account is pending approval by the admin.']);
                    }
                }
            }
        } else {
            wp_send_json_error(['message' => 'Nonce verification failed.']);
        }
    } else {
        wp_send_json_error(['message' => 'No registration data received.']);
    }

    wp_die(); // Terminate AJAX request properly
}

add_action('wp_ajax_registration_ajax', 'registration_ajax');
add_action('wp_ajax_nopriv_registration_ajax', 'registration_ajax');
