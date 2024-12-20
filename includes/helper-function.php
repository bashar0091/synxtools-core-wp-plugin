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

// Function to redirect unapproved users after login failure
function redirect_unapproved_user()
{
    if (is_user_logged_in() && !current_user_can('administrator')) {
        $user_id = get_current_user_id();
        $is_approved = get_user_meta($user_id, 'is_approved', true);
        if ($is_approved == 0) {
            wp_logout();
            // Store the error message in a global variable
            global $unapproved_user_message;
            $unapproved_user_message = 'Error: Your Account is not approved, Please contact with site admin.';
        }
    }
}
add_action('init', 'redirect_unapproved_user');

// Display error message before the login form
function display_unapproved_user_message()
{
    global $unapproved_user_message;

    if (isset($unapproved_user_message)) {
        echo '<div class="woocommerce-error">' . esc_html($unapproved_user_message) . '</div>';
        // Clear the message after displaying it
        unset($unapproved_user_message);
    }
}
add_action('woocommerce_login_form_start', 'display_unapproved_user_message');

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
            return '<span style="color:green;">Approved</span> | <a href="javascript:void(0)" class="toggle-approval" data-user-id="' . $user_id . '" data-approval="0">Disapprove</a>';
        } else {
            return '<span style="color:red;">Pending</span> | <a href="javascript:void(0)" class="toggle-approval" data-user-id="' . $user_id . '" data-approval="1">Approve</a>';
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


// redirect to woocommerce default register page to own register page 
function woo_default_register_to_redirect()
{
    if (is_page('my-account') && isset($_GET['action']) && $_GET['action'] == 'register') {
        wp_redirect(home_url('/registrenen'));
        exit;
    }
}
add_action('template_redirect', 'woo_default_register_to_redirect');


// get woocommerce all product discount for specific user 
add_filter('woocommerce_product_get_price', 'apply_global_discount_for_logged_in_user', 10, 2);
add_filter('woocommerce_product_get_regular_price', 'apply_global_discount_for_logged_in_user', 10, 2);
add_filter('woocommerce_variation_get_price', 'apply_global_discount_for_logged_in_user', 10, 2);
add_filter('woocommerce_variation_get_regular_price', 'apply_global_discount_for_logged_in_user', 10, 2);

function apply_global_discount_for_logged_in_user($price, $product)
{
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $discount_percentage = (float) get_user_meta($user_id, 'discount_percentage', true); // Ensure percentage is numeric

        // Apply discount only if a valid discount percentage is set
        if ($discount_percentage > 0) {
            $discount = ($price * $discount_percentage) / 100;
            $discounted_price = $price - $discount;
            return $discounted_price;
        }
    }

    return $price;
}
