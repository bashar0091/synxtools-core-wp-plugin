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
