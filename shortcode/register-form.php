<?php
// Prevent direct access to the plugin file
defined('ABSPATH') || exit;

/**
 * Register Form Shortcode
 */
function register_form_shortcode()
{
   ob_start();
?>
   <div class="register_form_wrapper">
      <?php echo wp_kses_post(title_v1('Bedrijfsgegevens')); ?>
      <div class="register_form_group">
         <div class="register_input_item">
            <?php echo wp_kses_post(input_v1('text', 'company_name', 'Bedrijfsnaam', true)); ?>
         </div>
         <div class="register_input_item">
            <?php echo wp_kses_post(input_v1('text', 'kvk_number', 'KVK-nummer', true)); ?>
         </div>
         <div class="register_input_item">
            <?php echo wp_kses_post(input_v1('text', 'btw_id', 'Btw-id', true)); ?>
         </div>
         <div class="register_input_item">
            <?php echo wp_kses_post(input_v1('text', 'bank_account', 'Bankrekening', true)); ?>
         </div>
      </div>
   </div>
<?php
   return ob_get_clean();
}
add_shortcode('register_form', 'register_form_shortcode');
