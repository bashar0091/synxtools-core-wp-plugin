jQuery(document).ready(function ($) {
  // Inject spinner styles into the <head>
  const spinnerStyles = `
      .user-approval-loading {
          display: inline-block;
          width: 16px;
          height: 16px;
          border: 2px solid rgba(0, 0, 0, 0.2);
          border-top-color: rgba(0, 0, 0, 0.8);
          border-radius: 50%;
          animation: spin 0.6s linear infinite;
          margin-left: 5px;
          vertical-align: middle;
      }
      @keyframes spin {
          to {
              transform: rotate(360deg);
          }
      }
  `;

  $("<style>").prop("type", "text/css").html(spinnerStyles).appendTo("head");

  // Handle approval toggle click event
  $(document).on("click", ".toggle-approval", function (e) {
    e.preventDefault();

    const userId = $(this).data("user-id");
    const approval = $(this).data("approval");
    const $link = $(this);

    // Add a loading spinner next to the link
    const $spinner = $('<span class="user-approval-loading"></span>');
    $link.after($spinner); // Add spinner after the link

    $.ajax({
      url: userApproval.ajaxUrl,
      type: "POST",
      data: {
        action: "toggle_user_approval",
        security: userApproval.nonce,
        user_id: userId,
        approval: approval,
      },
      success(response) {
        if (response.success) {
          const statusText = approval ? "Approved" : "Pending";
          const newApproval = approval ? 0 : 1;
          const newActionText = approval ? "Disapprove" : "Approve";
          const newColor = approval ? "green" : "red";

          $link
            .closest("td")
            .html(
              `<span style="color:${newColor};">${statusText}</span> | <a href="javascript:void(0)" class="toggle-approval" data-user-id="${userId}" data-approval="${newApproval}">${newActionText}</a>`
            );
        } else {
          alert("Failed to update approval status.");
        }
      },
      error() {
        alert("Error processing request.");
      },
      complete() {
        // Remove the spinner after the request completes
        $spinner.remove();
      },
    });
  });
});
