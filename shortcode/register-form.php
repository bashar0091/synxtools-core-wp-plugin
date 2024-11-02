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
      <form action="#!" method="post" class="register_form_on_submit">
         <div>
            <?php title_v1('Bedrijfsgegevens'); ?>
            <div class="register_form_group">
               <div class="register_input_item">
                  <?php input_v1('text', 'company_name', 'Bedrijfsnaam', true); ?>
               </div>
               <div class="register_input_item">
                  <?php input_v1('text', 'kvk_number', 'KVK-nummer', true); ?>
               </div>
               <div class="register_input_item">
                  <?php input_v1('text', 'btw_id', 'Btw-id', true); ?>
               </div>
               <div class="register_input_item">
                  <?php input_v1('text', 'bank_account', 'Bankrekening', false); ?>
               </div>
               <div class="register_input_item">
                  <?php input_v1('text', 'acc_holder_name', 'Naam rekeninghouder', false); ?>
               </div>
            </div>
         </div>

         <div class="mt_50">
            <?php title_v1('Bedrijfsadres'); ?>
            <div class="register_form_group">
               <div class="register_input_item register_input_item_v1">
                  <?php input_v1('text', 'street', 'Straat', true); ?>
                  <?php input_v1('text', 'house_no', 'Huisnr', true); ?>
               </div>
               <div class="register_input_item register_input_item_v2">
                  <?php input_v1('text', 'postcode', 'Postcode', true); ?>
                  <?php input_v1('text', 'place', 'Plaats', true); ?>
               </div>
               <div class="register_input_item">
                  <?php input_v1('text', 'land', 'Land', true); ?>
               </div>
               <div class="register_input_item">
                  <?php input_v1('tel', 'phone', 'Telefoon', true); ?>
               </div>
               <div class="register_input_item">
                  <?php input_v1('tel', 'phone2', 'Telefoon 2', false); ?>
               </div>
               <div class="register_input_item">
                  <?php input_v1('email', 'email', 'E-mailadres', true); ?>
               </div>
               <div class="register_input_item">
                  <?php input_v1('email', 'invoice_email', 'Factuur e-mailadres', true); ?>
               </div>
            </div>
         </div>

         <div class="mt_50 jump_section">
            <?php title_v1('Contactpersoon'); ?>
            <div class="register_form_group">
               <div class="register_input_item register_input_item_v4">
                  <?php
                  $contact_data = field_data('contact');
                  input_radio_v1('contact_person', true, $contact_data);
                  ?>
               </div>
               <div class="register_input_item register_input_item_v3">
                  <?php input_v1('text', 'first_name', 'Voornaam', true); ?>
                  <?php input_v1('text', 'middle_name', 'tussenv.', false); ?>
                  <?php input_v1('text', 'last_name', 'Achternaam', true); ?>
               </div>
               <div class="register_input_item">
                  <?php input_v1('tel', 'contact_phone', 'Telefoon', true); ?>
               </div>
               <div class="register_input_item email_error_show">
                  <?php input_v1('email', 'contact_email', 'E-mailadres', true); ?>
               </div>
               <div class="register_input_item register_input_item_v7 password_error_show">
                  <?php input_v1('password', 'password', 'Wachtwoord', true); ?>
                  <?php input_v1('password', 'confirm_password', 'Wachtwoord herhalen', true); ?>
               </div>
            </div>
         </div>

         <div class="mt_50">
            <?php title_v1('Hoe heeft u ons gevonden?'); ?>
            <div class="register_form_group">
               <div class="register_input_item">
                  <?php input_v1('text', 'found_via', 'Gevonden via', true); ?>
               </div>
            </div>
         </div>

         <div class="mt_50">
            <div class="register_form_group">
               <div class="register_input_item register_input_item_v5">
                  <?php input_file_v1('upload_docs', true); ?>
               </div>
            </div>
         </div>

         <div class="mt_50">
            <div class="register_form_group">
               <div class="register_input_item register_input_item_v6">
                  <label>
                     <input type="checkbox" name="confirmation" value="on" required>
                     Ik ga akkoord met de algemene voorwaarden
                  </label>
               </div>
            </div>
         </div>

         <div class="mt_50">
            <?php wp_nonce_field('register_form_action', 'register_form_nonce'); ?>
            <button class="register_submit_btn_v1" type="submit">Klant worden</button>
            <p class="response_text"></p>
         </div>
      </form>
   </div>
<?php
   return ob_get_clean();
}
add_shortcode('register_form', 'register_form_shortcode');
