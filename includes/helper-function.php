<?php
// Prevent direct access to the plugin file
defined('ABSPATH') || exit;

// title v1
function title_v1($text = '')
{
?>
    <h2 class="form_title_v1"><?php echo esc_html($text); ?></h2>
<?php
}


// generate input v1
function input_v1($type = '', $name = '', $placeholder = '', $required = false)
{
?>
    <span class="r_input_wrapper">
        <input
            type="<?php echo esc_attr($type); ?>"
            name="<?php echo esc_attr($name); ?>"
            id="<?php echo esc_attr($name); ?>"
            placeholder="<?php echo esc_attr($placeholder); ?>"
            <?php echo $required ? 'required' : ''; ?>>
        <?php
        if ($required) {
        ?>
            <b>*</b>
        <?php
        }

        if ($type == 'email') {
        ?>
            <img src="<?php echo esc_url(plugin_dir_url(__FILE__) . '../assets/images/email-icon.png'); ?>" alt="icon">
        <?php
        }
        ?>
    </span>
<?php
}

// generate input radio
function input_radio_v1($name = '', $required = false, $value = [])
{
?>
    <span class="r_input_wrapper">
        <?php
        if (!empty($value)) {
            foreach ($value as $key => $label) {
        ?>
                <label>
                    <input
                        type="radio"
                        name="<?php echo esc_attr($name); ?>"
                        <?php echo $required ? 'required' : ''; ?>
                        value="<?php echo esc_attr($label); ?>">
                    <?php echo esc_html($label); ?>
                </label>
        <?php
            }
        }
        ?>

        <?php if ($required) { ?>
            <b>*</b>
        <?php } ?>
    </span>
<?php
}

// generate upload option wrap
function input_file_v1($name = '', $required = false)
{
?>
    <span class="r_input_wrapper upload_doc_wrap">
        <p class="upload_doc_title">Document <?php echo $required ? '<b>*</b>' : ''; ?></p>
        <label>
            <span> Upload file</span>
            <input
                type="file"
                class="file_on_upload"
                name="<?php echo esc_attr($name); ?>"
                id="<?php echo esc_attr($name); ?>"
                <?php echo $required ? 'required' : ''; ?>>

            <p class="file_name_display" style="display:none;"></p>
        </label>
    </span>
<?php
}

// synxtools multiple data 
function field_data($name)
{
    $data = [];
    if ($name == 'contact') {
        $data = [
            '1' => 'Mevrouw',
            '2' => 'De Heer',
            '3' => 'Geen keuze',
        ];
    }
    return $data;
}

// Restrict register page for logged-in non-admin users
function redirect_register_page()
{
    if (is_page('registrenen') && is_user_logged_in() && !current_user_can('administrator')) {
        wp_redirect(home_url());
        exit;
    }
}
add_action('template_redirect', 'redirect_register_page');

// Block login for unapproved users
function restrict_unapproved_login($user, $username, $password)
{
    $user_id = $user ? $user->ID : 0;
    if ($user_id && get_user_meta($user_id, 'is_approved', true) != 1) {
        return new WP_Error('not_approved', 'Your account is awaiting approval.');
    }
    return $user;
}
add_filter('wp_authenticate_user', 'restrict_unapproved_login', 10, 3);

// Add a new column to the Users table
function add_approval_column($columns)
{
    $columns['approval'] = 'Approval';
    return $columns;
}
add_filter('manage_users_columns', 'add_approval_column');

// Populate the "Approval" column, skip for administrators
function show_approval_column($value, $column_name, $user_id)
{
    if ('approval' === $column_name) {
        $user = get_userdata($user_id);

        // Check if the user has the 'administrator' role
        if (in_array('administrator', $user->roles, true)) {
            return '<span style="color:gray;">N/A</span>'; // Display "N/A" or leave empty for admins
        }

        $is_approved = get_user_meta($user_id, 'is_approved', true);
        if ($is_approved) {
            return '<span style="color:green;">Approved</span> | <a href="#" class="toggle-approval" data-user-id="' . $user_id . '" data-approval="0">Disapprove</a>';
        } else {
            return '<span style="color:red;">Pending</span> | <a href="#" class="toggle-approval" data-user-id="' . $user_id . '" data-approval="1">Approve</a>';
        }
    }
    return $value;
}
add_filter('manage_users_custom_column', 'show_approval_column', 10, 3);


// AJAX handler to toggle user approval
function toggle_user_approval()
{
    // Verify the nonce for security
    check_ajax_referer('user_approval_nonce', 'security');

    // Check user capabilities
    if (!current_user_can('manage_options')) {
        wp_send_json_error('You do not have permission to approve users.');
    }

    $user_id = intval($_POST['user_id']);
    $approval_status = intval($_POST['approval']); // 1 for approved, 0 for disapproved

    // Update the user's approval status
    update_user_meta($user_id, 'is_approved', $approval_status);

    if ($approval_status == 1) {
        $user = get_userdata($user_id);
        wp_mail(
            $user->user_email,
            'Your account has been approved',
            'Congratulations! Your account has been approved. You can now log in.'
        );
    }

    wp_send_json_success(['status' => $approval_status ? 'Approved' : 'Pending']);
}
add_action('wp_ajax_toggle_user_approval', 'toggle_user_approval');

// Enqueue script on the Users page
function enqueue_user_approval_script($hook)
{
    if ('users.php' !== $hook) {
        return;
    }

    wp_enqueue_script('user-approval-script', plugin_dir_url(__FILE__) . '../assets/js/user-approval.js', ['jquery'], null, true);
    wp_localize_script('user-approval-script', 'userApproval', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('user_approval_nonce')
    ]);
}
add_action('admin_enqueue_scripts', 'enqueue_user_approval_script');
