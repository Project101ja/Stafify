<?php
include 'Code.php';

require 'vendor/autoload.php'; // Make sure to include PHPMailer autoload

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'send_test_email') {
        sendTestEmail();
    } elseif ($action === 'save_template') {
        saveTemplate();
    }
}

function sendTestEmail() {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $_POST['smtp_username'] ?? ''; // SMTP Username (From Email)
        $mail->Password = $_POST['smtp_password'] ?? ''; // SMTP Password/App Password
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Email content
        $mail->setFrom($_POST['smtp_username'], $_POST['sender_name'] ?? '');
        $mail->addAddress($_POST['test_email'] ?? '');
        $mail->Subject = $_POST['subject'] ?? '';
        $mail->isHTML(true);
        $mail->Body = $_POST['message'] ?? '';
        
        if (!empty($_POST['cc'])) {
            $mail->addCC($_POST['cc']);
        }
        
        if (!empty($_POST['bcc'])) {
            $mail->addBCC($_POST['bcc']);
        }
        
        $mail->send();
        echo json_encode(['status' => 'success', 'message' => 'Test email sent successfully']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
    }
    exit;
}

function saveTemplate() {
    // In a real implementation, you would save to a database or file
    $templateData = [
        'action' => $_POST['leave_action'] ?? '',
        'to' => $_POST['email'] ?? '',
        'cc' => $_POST['cc'] ?? '',
        'bcc' => $_POST['bcc'] ?? '',
        'sender_name' => $_POST['sender_name'] ?? '',
        'smtp_username' => $_POST['smtp_username'] ?? '',
        'subject' => $_POST['subject'] ?? '',
        'message' => $_POST['message'] ?? '',
        'smtp_password' => $_POST['smtp_password'] ?? ''
    ];
    
    // Here you would typically save to database or file
    // For demonstration, we'll just return the data
    echo json_encode(['status' => 'success', 'message' => 'Template saved successfully', 'data' => $templateData]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap');

        :root {
          /* Color Branding - Essential colors */
          --primary-color: #1F5497;
          --secondary-color: #22A00E;
          --text-color: #747474;
          --accent-color: #22A00E;
          --white: #ffffff;
          --alt-white: #F8F9FA;
          --black: #000000;
          --alt-black: #222222;
          --transparent: transparent;
          --border-color: #CFCFCF;
          --blue-hover: #1E4B84;
          --blue-active: #2f69b3;

          /* Font Family - Single font for simplicity */
          --primary-font: 'Quicksand', sans-serif;

          /* Button styles */
          --button-padding: 12px 24px;
          --button-radius: 100px;
          --button-font-size: 16px;
          --button-text-color: var(--white);
          --button-weight: 500;

          /* Headings */
          --page-heading-color: #222222;
          --page-heading-size: 32px;
          --page-heading-size-mobile: 24px;
          --page-heading-weight: 600;
          
          --comp-heading-color: #3b3b3b;
          --comp-heading-size: 22px;
          --comp-heading-size-mobile: 18px;
          --comp-heading-weight: 600;
          
          --inner-heading-color: #696969;
          --inner-heading-size: 18px;
          --inner-heading-size-mobile: 14px;
          --inner-heading-weight: 600;
        }

        * {
          box-sizing: border-box;
          margin: 0;
          padding: 0;
        }

        ::-webkit-scrollbar {
          width: 4px;
        }

        ::-webkit-scrollbar-track {
          background: #f1f1f1;
          border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
          background: #95a5a6;
          border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
          background: var(--primary-color);
        }

        body {
          background-color: #f8f9fa;
          font-family: var(--primary-font);
        }

        h1, h2, h3, h4, h5, h6, p, input, select, option, textarea, div, span, button {
          font-family: var(--primary-font);
        }

        a {
          font-weight: 500;
          text-decoration: none;
        }

        ul {
          list-style: none;
          margin: 0;
          padding: 0;
        }

        a, button {
          transition: all 0.3s ease-in-out;
          cursor: pointer;
        }

        button {
          border: none;
        }

        /* Essential utility classes */
        .blue { color: var(--primary-color); }
        .flex { display: flex; }
        .flex-col { flex-direction: column; }
        .flex-row { flex-direction: row; }
        .justify-between { justify-content: space-between; }
        .justify-center { justify-content: center; }
        .items-center { align-items: center; }
        .w-full { width: 100%; }
        .gap-5 { gap: 5px; }
        .gap-10 { gap: 10px; }
        .gap-20 { gap: 20px; }
        .p-20 { padding: 20px; }
        .text-center { text-align: center; }

        .rounded-shadow-box {
          background-color: #ffffff;
          padding: 20px;
          border-radius: 20px;
          box-shadow: 0px 5px 30px -20px rgba(63, 63, 63, 0.1);
        }

        .page-title {
          font-family: var(--primary-font);
          font-size: var(--page-heading-size);
          color: var(--page-heading-color);
        }

        .component-headings {
          font-family: var(--primary-font);
          font-size: var(--comp-heading-size);
          font-weight: var(--comp-heading-weight);
          color: var(--comp-heading-color);
          padding-left: 20px;
        }

        .inner-titles {
          font-family: var(--primary-font);
          font-size: var(--inner-heading-size);
          font-weight: var(--inner-heading-weight);
          color: var(--inner-heading-color);
        }

        /* Button styles */
        .primary-button, .secondary-button, .outline-button {
          padding: var(--button-padding);
          font-family: var(--primary-font);
          color: var(--button-text-color);
          border-radius: var(--button-radius);
          font-size: var(--button-font-size);
          font-weight: var(--button-weight);
          margin-top: 10px;
          transition: all 0.3s ease-in-out;
        }

        .primary-button {
          background-color: var(--accent-color);
          border: 1px solid var(--accent-color);
        }

        .primary-button:hover {
          background-color: #29B513;
        }

        .secondary-button {
          background-color: var(--primary-color);
          border: 1px solid var(--primary-color);
        }

        .secondary-button:hover {
          background-color: #1f477b;
        }

        .outline-button {
          background-color: transparent;
          border: 1px solid #919191 !important;
          color: #919191;
        }

        .outline-button:hover {
          border: 1px solid #bbbbbb !important;
          color: #212121;
          background: #ededed;
        }

        /* Leave action styles */
        .leave-action-wrapper {
          display: flex;
          flex-direction: column;
          gap: 10px;
          width: 100%;
        }

        .leave-action-wrapper .leave-action-item button {
          background: #0000000d !important;
          color: #5f5f5f !important;
          border: 1px solid transparent !important;
          text-align: center !important;
          border-radius: 10px !important;
          transition: all 0.3s ease-in-out;
        }

        .leave-action-wrapper .leave-action-item button.secondary-button:hover,
        .leave-action-wrapper .leave-action-item button.secondary-button.active {
          background: #c7d4e5 !important;
          border: 1px solid #1f477b !important;
          color: #1f477b !important;
        }

        .leave-action-wrapper .leave-action-item button.primary-button:hover,
        .leave-action-wrapper .leave-action-item button.primary-button.active {
          background: #3cbd2730 !important;
          border: 1px solid #29B513 !important;
          color: #29B513 !important;
        }

        /* Toggle button styles */
        .emailVewModeToggle {
          position: relative;
          display: flex;
          gap: 10px;
          vertical-align: middle;
          width: 100%;
          background: var(--white);
          border-radius: 100px;
          padding: 7px;
          box-shadow: inset 0 0 25px -15px #313131ba;
        }

        .emailVewModeToggle .toggle-btn {
          width: 100% !important;
          text-align: center !important;
          padding: 10px 20px !important;
          border: none !important;
          cursor: pointer !important;
          font-size: 16px !important;
          background: none !important;
          position: relative !important;
          z-index: 2 !important;
          transition: color 0.3s ease !important;
          font-weight: 600 !important;
          border-color: transparent !important;
        }

        .emailVewModeToggle .toggle-btn.active {
          background-color: #3cbd2730 !important;
          border-radius: 100px !important;
          color: var(--accent-color) !important;
          border-color: transparent !important;
        }

        /* Form styles */
        .form-group {
          display: flex !important;
          flex-direction: column !important;
          gap: 10px !important;
        }

        .form-control {
          display: block;
          width: 100%;
          padding: 0.375rem 0.75rem;
          font-size: 1rem;
          line-height: 1.5;
          color: #495057;
          background-color: #fff;
          background-clip: padding-box;
          border: 1px solid #ced4da;
          border-radius: 0.25rem;
          transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-container {
          display: flex;
          flex-direction: column;
          gap: 20px;
        }

        /* Leave actions */
        .leave-actions { 
          margin-top: 10px;
          display: none; 
          flex-direction: column;
          gap: 10px;
        }

        .leave-actions.show {
          display: flex; 
        }

        .leave-action-button { 
          width: 100%; 
          text-align: left; 
        }

        /* Input group */
        .input-group {
          position: relative;
          display: flex;
          flex-wrap: wrap;
          align-items: stretch;
          width: 100%;
        }

        .input-group .form-control {
          position: relative;
          flex: 1 1 auto;
          width: 1%;
          margin-bottom: 0;
        }

        .input-group-append {
          display: flex;
          margin-left: -1px;
        }

        .input-group-append button {
          background: #1F5497 !important;
          color: #ffffff !important;
          border: 1px solid transparent !important;
          text-align: center !important;
          border-radius: 7px !important;
          transition: all 0.3s ease-in-out;
        }

        /* Modal styles */
        .modal {
          position: fixed;
          top: 0;
          left: 0;
          z-index: 1050;
          display: none;
          width: 100%;
          height: 100%;
          overflow: hidden;
          outline: 0;
          background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-dialog {
          position: relative;
          width: auto;
          margin: 1.75rem auto;
          max-width: 500px;
        }

        .modal-content {
          position: relative;
          display: flex;
          flex-direction: column;
          width: 100%;
          background-color: #fff;
          border: 1px solid rgba(0, 0, 0, 0.2);
          border-radius: 0.3rem;
          outline: 0;
        }

        .modal-header, .modal-body, .modal-footer {
          padding: 1rem;
        }

        .modal-header {
          display: flex;
          align-items: flex-start;
          justify-content: space-between;
          border-bottom: 1px solid #dee2e6;
        }

        .modal-footer {
          display: flex;
          align-items: center;
          justify-content: flex-end;
          border-top: 1px solid #dee2e6;
        }

        .close {
          float: right;
          font-size: 1.5rem;
          font-weight: 700;
          color: #000;
          background-color: transparent;
          border: 0;
          cursor: pointer;
        }

        /* Alert styles */
        .alert {
          position: relative;
          padding: 0.75rem 1.25rem;
          margin-bottom: 1rem;
          border: 1px solid transparent;
          border-radius: 0.25rem;
        }

        .alert-success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }
        .alert-info { color: #0c5460; background-color: #d1ecf1; border-color: #bee5eb; }
        .alert-warning { color: #856404; background-color: #fff3cd; border-color: #ffeeba; }
        .alert-danger { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; }

        /* Summernote editor fixes */
        .note-editor.note-frame.card {
          box-shadow: none !important;
          min-height: min-content;
          border: 1px solid #ced4da;
        }

        /* IMPORTANT FIX: Make toolbar visible */
        .note-toolbar.card-header {
          display: block !important;
          padding: 10px;
          background-color: #f8f9fa;
          border-bottom: 1px solid #ced4da;
        }

        .note-editor .note-btn {
          padding: 5px 10px;
          font-size: 14px;
          background-color: #fff;
          border: 1px solid #ced4da;
          border-radius: 4px;
          height: 35px;
        }

        .note-editor .note-btn:hover {
          background-color: #e9ecef;
        }

        .note-editor .note-dropdown-menu {
          min-width: 200px;
        }

        .note-editor .note-modal-footer {
          height: auto;
          padding: 10px;
        }

        /* Placeholder item */
        .placeholder-item {
          padding: 0.5rem;
          border: 1px solid #dee2e6;
          border-radius: 0.25rem;
          cursor: pointer;
        }

        .placeholder-item:hover {
          background-color: #f8f9fa;
        }

        /* Mobile styles */
        @media screen and (max-width: 768px) {
          .emailTemplateAction.desktop {
            display: none;
          }
          .emailTemplateAction.mobile {
            display: flex;
            justify-content: center;
          }
        }

        @media screen and (max-width: 530px) {
          .page-title {
            font-size: var(--page-heading-size-mobile);
          }
          .component-headings {
            font-size: var(--comp-heading-size-mobile);
          }
          .inner-titles {
            font-size: var(--inner-heading-size-mobile);
          }
          
          .leave-action-wrapper {
            display: flex;
            flex-direction: column;
          }
          
          .input-group {
            flex-direction: column;
            width: 100%;
            gap: 10px;
          }
        }

        /* Add this CSS to hide the radio button dots */
        .emailVewModeToggle .toggle-btn input[type="radio"] {
          display: none;
        }

        label {
            display: inline-block;
            margin-bottom: 0rem !important;
        }
    </style>
</head>
<body>
    <div class="flex justify-between items-center gap-10 header">
        <div class="flex flex-col gap-5 greetings">
            <div class="flex flex-col gap-20">
                <div class="flex justify-between items-center gap-20">
                    <h2 class="component-headings">Leave Notification</h2>
                    <div class="flex gap-10 emailTemplateAction desktop">
                        <button class="outline-button" id="testButton" onclick="sendTestEmail()">Send Test Email</button>
                        <button class="primary-button" id="saveButton" onclick="saveTemplate()">Save Template</button>
                    </div>
                </div>
                <div class="flex gap-20 w-full emailNotificationWrapper">
                    <div class="flex flex-col gap-20 w-full">
                        <div class="flex flex-col gap-20 rounded-shadow-box w-full">
                            <div class="flex gap-20 justify-between">
                                <div class="emailVewModeToggle" data-toggle="buttons">
                                    <label class="toggle-btn active" id="employeeToggle">
                                        <input type="radio" name="viewMode" value="employee" checked> EMPLOYEE
                                    </label>
                                    <label class="toggle-btn" id="companyToggle">
                                        <input type="radio" name="viewMode" value="company"> COMPANY
                                    </label>
                                </div>
                            </div>
                            <div class="toggle-section">
                                <!-- Employee Leave Actions -->
                                <div id="employeeLeaveActions" class="leave-actions show">
                                    <h3 class="inner-titles">Employee Leave Actions</h3>
                                    <div class="leave-action-wrapper">
                                        <div class="leave-action-item">
                                            <button type="button" class="secondary-button leave-action-button" data-action="leavePending">Leave Pending</button>
                                        </div>
                                        <div class="leave-action-item">
                                            <button type="button" class="secondary-button leave-action-button" data-action="leaveComment">Leave Comment</button>
                                        </div>
                                        <div class="leave-action-item">
                                            <button type="button" class="secondary-button leave-action-button" data-action="leaveApprove">Leave Approve</button>
                                        </div>
                                        <div class="leave-action-item">
                                            <button type="button" class="secondary-button leave-action-button" data-action="leaveDecline">Leave Decline</button>
                                        </div>
                                    </div>
                                </div>
                                <!-- Company Leave Actions -->
                                <div id="companyLeaveActions" class="leave-actions">
                                    <h3 class="inner-titles">Company Leave Actions</h3>
                                    <div class="leave-action-wrapper">
                                        <div class="leave-action-item">
                                            <button type="button" class="primary-button leave-action-button" data-action="companyLeavePending">Company Leave Pending</button>
                                        </div>
                                        <div class="leave-action-item">
                                            <button type="button" class="primary-button leave-action-button" data-action="companyComment">Company Comment</button>
                                        </div>
                                        <div class="leave-action-item">
                                            <button type="button" class="primary-button leave-action-button" data-action="companyLeaveApprove">Company Leave Approve</button>
                                        </div>
                                        <div class="leave-action-item">
                                            <button type="button" class="primary-button leave-action-button" data-action="companyLeaveDecline">Company Leave Decline</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-shadow-box w-full">
                            <div class="recipients-section">
                                <h3 class="inner-titles">Recipients</h3>
                                <div class="form-container">
                                    <div class="form-group">
                                        <label>To: <span class="text-danger">*</span></label>
                                        <input type="text" id="email" class="form-control email-input" placeholder="recipient@example.com">
                                        <small class="form-text text-muted">Enter email addresses separated by commas.</small>
                                    </div>
                                    <div class="form-group">
                                        <label>CC:</label>
                                        <input type="text" id="cc" class="form-control email-input" placeholder="cc@example.com">
                                    </div>
                                    <div class="form-group">
                                        <label>BCC:</label>
                                        <input type="text" id="bcc" class="form-control email-input" placeholder="bcc@example.com">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-20 w-full">
                        <div class="rounded-shadow-box w-full">
                            <div class="form-container">
                                <div class="form-group">
                                    <label>Sender's Name:</label>
                                    <input type="text" id="senderName" class="form-control" placeholder="Your Name">
                                </div>
                                <div class="form-group">
                                    <label>SMTP Username: <span class="text-danger">*</span></label>
                                    <input type="email" id="smtpUsername" class="form-control" placeholder="your-email@gmail.com">
                                    <small class="form-text text-muted">
                                        This is your Gmail address used for SMTP authentication.
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label>SMTP Password/App Password: <span class="text-danger">*</span></label>
                                    <input type="password" id="smtpPassword" class="form-control" placeholder="Your SMTP password">
                                    <small class="form-text text-muted">
                                        For Gmail, you may need to generate an <a href="https://support.google.com/accounts/answer/185833" target="_blank">App Password</a> if you have 2FA enabled.
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label>Subject: <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" id="subject" class="form-control" placeholder="Enter subject">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" onclick="showPlaceholdersModal('subject')">Add Placeholder</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Message: <span class="text-danger">*</span></label>
                                    <textarea id="editor"></textarea>
                                </div>
                                <div id="status" class="mt-3 text-center"></div>
                            </div>
                        </div>
                        <div class="modal fade" id="placeholdersModal" tabindex="-1" role="dialog" aria-labelledby="placeholdersModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="placeholdersModalLabel">Insert Placeholder</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div id="placeholderModalContent"></div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-10 emailTemplateAction mobile" style="display: none;">
                            <button class="outline-button" id="testButton" onclick="sendTestEmail()">Send Test Email</button>
                            <button class="primary-button" id="saveButton" onclick="saveTemplate()">Save Template</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        var currentTarget = '';
        var placeholders = [];
        var emailData = [];
        var emailColumn = '';
        var savedTemplates = {};
        var activeLeaveAction = '';

        $(document).ready(function() {
            $('#editor').summernote({
                height: 250,
                placeholder: "Write your email here...",
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']],
                    ['mybutton', ['placeholder']]
                ],
                buttons: {
                    placeholder: function(context) {
                        var ui = $.summernote.ui;
                        var button = ui.button({
                            contents: '<i class="fas fa-tags"></i> Placeholder',
                            tooltip: 'Insert Placeholder',
                            click: function() {
                                showPlaceholdersModal('editor');
                            }
                        });
                        return button.render();
                    }
                }
            });

            loadPlaceholders();
            loadEmailData();
            initializeEmailInputs();
            initializeToggle();
            initializeLeaveActions();
            
            showStatus("Ready to send emails", "info");
        });

        function initializeToggle() {
            $('#employeeToggle').click(function() {
                $(this).addClass('active');
                $('#companyToggle').removeClass('active');
                $('#employeeLeaveActions').addClass('show');
                $('#companyLeaveActions').removeClass('show');
            });

            $('#companyToggle').click(function() {
                $(this).addClass('active');
                $('#employeeToggle').removeClass('active');
                $('#employeeLeaveActions').removeClass('show');
                $('#companyLeaveActions').addClass('show');
            });
        }
      
        function initializeLeaveActions() {
            $('.leave-action-button').click(function() {
                $('.leave-action-button').removeClass('active');
                $(this).addClass('active');
                activeLeaveAction = $(this).data('action');
                loadPlaceholders();
                applyLeaveAction(activeLeaveAction);
            });
        }

        function applyLeaveAction(actionId) {
            // This would be replaced with your PHP implementation
            // For now, we'll just show a status message
            showStatus('Selected action: ' + actionId, 'success');
        }

        function saveTemplate() {
            if (!activeLeaveAction) {
                showStatus("Please select a leave action first", "warning");
                return;
            }

            var templateData = {
                action: activeLeaveAction,
                to: $("#email").val(),
                cc: $("#cc").val(),
                bcc: $("#bcc").val(),
                sender_name: $("#senderName").val(),
                smtp_username: $("#smtpUsername").val(),
                smtp_password: $("#smtpPassword").val(),
                subject: $("#subject").val(),
                message: $('#editor').summernote('code')
            };
            
            $.ajax({
                type: "POST",
                url: "",
                data: {
                    action: 'save_template',
                    leave_action: activeLeaveAction,
                    email: templateData.to,
                    cc: templateData.cc,
                    bcc: templateData.bcc,
                    sender_name: templateData.sender_name,
                    smtp_username: templateData.smtp_username,
                    smtp_password: templateData.smtp_password,
                    subject: templateData.subject,
                    message: templateData.message
                },
                success: function(response) {
                    var result = JSON.parse(response);
                    showStatus(result.message, result.status);
                },
                error: function(xhr, status, error) {
                    showStatus("Error saving template: " + error, "danger");
                }
            });
        }

        function loadEmailData() {
            // This would be replaced with your PHP implementation
            showStatus("Email data loaded", "success");
        }

        function updateEmailDropdowns() {
            // This would be replaced with your PHP implementation
        }

        function previewPersonalization(email) {
            if (!email) return;
            // This would be replaced with your PHP implementation
            showStatus("Personalization data for " + email + " loaded", "success");
        }

        function loadPlaceholders() {
            if (!activeLeaveAction) {
                showStatus("Please select a leave action first", "warning");
                return;
            }

            // This would be replaced with your PHP implementation
            // For now, we'll use some sample placeholders
            placeholders = ['name', 'email', 'date', 'reason'];
            showStatus("Placeholders loaded for " + activeLeaveAction, "success");
        }

        function showPlaceholdersModal(target) {
            currentTarget = target;
            var modalContent = $("#placeholderModalContent");
            modalContent.empty();

            if (placeholders && placeholders.length > 0) {
                placeholders.forEach(function(header) {
                    var placeholder = '{{' + header + '}}';
                    var item = $('<div class="placeholder-item my-2">' + placeholder + '</div>');
                    item.click(function() {
                        insertPlaceholder(placeholder, currentTarget);
                        $('#placeholdersModal').modal('hide');
                    });
                    modalContent.append(item);
                });
            } else {
                modalContent.html('<div class="alert alert-warning">No placeholders available</div>');
            }
            $('#placeholdersModal').modal('show');
        }

        function insertPlaceholder(placeholder, target) {
            if (target === 'editor') {
                $('#editor').summernote('editor.saveRange'); 
                $('#editor').summernote('editor.restoreRange'); 
                $('#editor').summernote('editor.insertText', placeholder);
            } else if (target === 'subject') {
                var subjectField = $('#subject');
                var cursorPos = subjectField[0].selectionStart;
                var textBefore = subjectField.val().substring(0, cursorPos);
                var textAfter = subjectField.val().substring(cursorPos);
                subjectField.val(textBefore + placeholder + textAfter);
                subjectField[0].selectionStart = cursorPos + placeholder.length;
                subjectField[0].selectionEnd = cursorPos + placeholder.length;
                subjectField.focus();
            }
        }

        function sendTestEmail() {
            var smtpUsername = $("#smtpUsername").val();
            var smtpPassword = $("#smtpPassword").val();
            var senderName = $("#senderName").val();
            var subject = $("#subject").val();
            var content = $('#editor').summernote('code');
            var to = $("#email").val();
            
            if (!smtpUsername || !smtpPassword) {
                alert("⚠️ Please fill in SMTP Username and Password before sending a test email.");
                return;
            }
            
            if (!subject || !content) {
                alert("⚠️ Please fill in Subject and Message before sending a test email.");
                return;
            }
            
            if (!to) {
                alert("⚠️ Please fill in the recipient email address before sending a test email.");
                return;
            }
            
            // Use the first email in the "To" field as the test recipient
            var testEmail = to.split(/,\s*/)[0];
            
            $("#status").html('<div class="alert alert-info">Sending test email... Please wait.</div>');
            $("#testButton").prop('disabled', true);
            
            $.ajax({
                type: "POST",
                url: "",
                data: {
                    action: 'send_test_email',
                    smtp_username: smtpUsername,
                    smtp_password: smtpPassword,
                    sender_name: senderName,
                    test_email: testEmail,
                    subject: subject,
                    message: content,
                    email: to,
                    cc: $("#cc").val(),
                    bcc: $("#bcc").val(),
                    leave_action: activeLeaveAction
                },
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result.status === 'success') {
                        showStatus("Test email sent successfully to " + testEmail, "success");
                    } else {
                        showStatus("❌ Error sending test email: " + result.message, "danger");
                    }
                    $("#testButton").prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    showStatus("❌ Error sending test email: " + error, "danger");
                    $("#testButton").prop('disabled', false);
                }
            });
        }

        function showStatus(message, type) {
            $("#status").html('<div class="alert alert-' + type + '">' + message + '</div>');
            if (type === "success") {
                setTimeout(function() {
                    $("#status").html('');
                }, 5000);
            }
        }

        function initializeEmailInputs() {
            $('.email-input').each(function() {
                var $input = $(this);
                
                $input.on('change', function() {
                    if ($(this).attr('id') === 'email') {
                        var firstEmail = $(this).val().split(/,\s*/)[0];
                        if (firstEmail) {
                            previewPersonalization(firstEmail);
                        }
                    }
                });
            });
        }
    </script>
</body>
</html>