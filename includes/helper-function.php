<?php
// Prevent direct access to the plugin file
defined('ABSPATH') || exit;

// title v1
function title_v1($text = '')
{
    ob_start();
?>
    <h2 class="form_title_v1"><?php echo esc_html($text); ?></h2>
<?php
    return ob_get_clean();
}


// generate input v1
function input_v1($type = '', $name = '', $placeholder = '', $required = false)
{
    ob_start();
?>
    <span>
        <input
            type="<?php echo esc_attr($type); ?>"
            name="<?php echo esc_attr($name); ?>"
            id="<?php echo esc_attr($name); ?>"
            placeholder="<?php echo esc_attr($placeholder); ?>"
            <?php echo $required ? 'required' : ''; ?>>
    </span>
<?php
    return ob_get_clean();
}
