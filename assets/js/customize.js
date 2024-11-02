(function ($) {
  "use strict";

  $(document).ready(function () {
    // register form submit
    $(document).on("submit", ".register_form_on_submit", function (e) {
      e.preventDefault();
      var t = $(this);
      var register_data = t.serialize(); // Serialize form data
      $(".response_text").text("Processing...");

      var formData = new FormData(this); // Create FormData object to include file upload
      formData.append("action", "registration_ajax");
      formData.append("register_data", register_data); // Append serialized data
      formData.append("register_form_nonce", $("#register_form_nonce").val()); // Append nonce

      $.ajax({
        type: "POST",
        url: dataAjax.ajaxurl,
        data: formData,
        processData: false, // Prevent jQuery from processing the data
        contentType: false, // Prevent jQuery from overriding content type
        success: function (response) {
          console.log(response);
          if (response.success) {
            $(".response_text").text(
              response.data.message + " Redirecting to Login Page..."
            );
            window.location.href = dataAjax.homeurl;
          } else {
            // Clear previous error messages
            $(".response_text").text("");
            $(".error-message").remove();

            // Handle password errors
            if (response.data.password_error) {
              $(".password_error_show").after(
                '<div class="error-message">' +
                  response.data.password_error +
                  "</div>"
              );
            }

            // Handle email errors
            if (response.data.email_error) {
              $(".email_error_show").after(
                '<div class="error-message">' +
                  response.data.email_error +
                  "</div>"
              );
            }

            // Handle upload errors
            if (response.data.upload_error) {
              $(".upload_error_show").after(
                '<div class="error-message">' +
                  response.data.upload_error +
                  "</div>"
              );
            }

            // Jump to the error section
            $("html, body").animate(
              {
                scrollTop: $(".jump_section").first().offset().top, // Scroll to the first error message
              },
              100
            ); // Duration in milliseconds
          }
        },
        error: function (error) {
          console.log(error);
        },
      });
    });

    // file upload checker
    $(document).on("change", ".file_on_upload", function (e) {
      e.preventDefault();

      const file = this.files[0];
      const maxSize = 5 * 1024 * 1024; // 5MB in bytes
      const validTypes = ["image/jpeg", "image/png", "application/pdf"];

      if (file) {
        // Check file type
        if (!validTypes.includes(file.type)) {
          alert("Invalid file type. Only JPG, PNG, and PDF are allowed.");
          $(this).val(""); // Clear the input
          $(".file_name_display").text(""); // Clear the file name display
          $(".file_name_display").hide();
          return;
        }

        // Check file size
        if (file.size > maxSize) {
          alert("File size exceeds 5MB limit.");
          $(this).val(""); // Clear the input
          $(".file_name_display").text(""); // Clear the file name display
          $(".file_name_display").hide();
          return;
        }

        // Display file name in text box
        $(".file_name_display").show();
        $(".file_name_display").text(file.name);
      }
    });
  });
})(jQuery);
