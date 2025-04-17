<?php
// Email Sender using PHPMailer

// First, let's include PHPMailer using Composer's autoloader
// Make sure you've installed PHPMailer via Composer first
require 'vendor/autoload.php';

// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Create a function to send emails
function sendEmail($to, $subject, $message, $fromName, $fromEmail, $smtpUsername, $smtpPassword, $attachment = null) {
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true); // true enables exceptions

    try {
        // Server settings - Using Gmail SMTP
        $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output (DEBUG_SERVER shows more details)
        $mail->isSMTP(); // Send using SMTP
        $mail->Host = 'smtp.gmail.com'; // Gmail SMTP server
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->Username = $smtpUsername; // SMTP username
        $mail->Password = $smtpPassword; // SMTP password or App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
        $mail->Port = 587; // TCP port for TLS
        
        // Alternatively, if using SSL:
        // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL encryption
        // $mail->Port = 465; // TCP port for SSL

        // Recipients
        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($to); // Add a recipient

        // Add attachment if provided
        if ($attachment !== null && file_exists($attachment)) {
            $mail->addAttachment($attachment);
        }

        // Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body = $message; // HTML message
        $mail->AltBody = strip_tags($message); // Plain text version for non-HTML mail clients

        // Send the email
        $mail->send();
        return ['success' => true, 'message' => 'Email has been sent successfully'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => "Email could not be sent. Mailer Error: {$mail->ErrorInfo}"];
    }
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $toEmail = $_POST['to_email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    $fromName = $_POST['from_name'] ?? '';
    $fromEmail = $_POST['from_email'] ?? '';
    $smtpUsername = $_POST['smtp_username'] ?? '';
    $smtpPassword = $_POST['smtp_password'] ?? '';
    
    // Handle file upload
    $attachment = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $tempFile = $_FILES['attachment']['tmp_name'];
        $targetFile = 'uploads/' . basename($_FILES['attachment']['name']);
        
        // Create uploads directory if it doesn't exist
        if (!is_dir('uploads')) {
            mkdir('uploads', 0755, true);
        }
        
        // Move the uploaded file
        if (move_uploaded_file($tempFile, $targetFile)) {
            $attachment = $targetFile;
        }
    }
    
    // Send the email
    $result = sendEmail($toEmail, $subject, $message, $fromName, $fromEmail, $smtpUsername, $smtpPassword, $attachment);
    
    // Clean up the attachment after sending
    if ($attachment && file_exists($attachment)) {
        unlink($attachment);
    }
    
    // Set the response message
    $responseMessage = $result['message'];
    $isSuccess = $result['success'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Sender</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        textarea {
            height: 150px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #dff0d8;
            color: #3c763d;
        }
        .alert-danger {
            background-color: #f2dede;
            color: #a94442;
        }
        .smtp-settings {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            border: 1px solid #e9ecef;
        }
        .smtp-settings h3 {
            margin-top: 0;
        }
    </style>
</head>
<body>
    <h1>Email Sender</h1>
    
    <?php if (isset($responseMessage)): ?>
        <div class="alert <?php echo $isSuccess ? 'alert-success' : 'alert-danger'; ?>">
            <?php echo htmlspecialchars($responseMessage); ?>
        </div>
    <?php endif; ?>
    
    <form method="post" enctype="multipart/form-data">
        <div class="smtp-settings">
            <h3>SMTP Settings</h3>
            <div class="form-group">
                <label for="smtp_username">SMTP Username:</label>
                <input type="email" id="smtp_username" name="smtp_username" placeholder="your-email@gmail.com" required>
            </div>
            
            <div class="form-group">
                <label for="smtp_password">SMTP Password/App Password:</label>
                <input type="password" id="smtp_password" name="smtp_password" placeholder="Your password or app password" required>
                <small>For Gmail, use an <a href="https://support.google.com/accounts/answer/185833" target="_blank">App Password</a> if 2FA is enabled</small>
            </div>
        </div>
        
        <div class="form-group">
            <label for="from_name">From Name:</label>
            <input type="text" id="from_name" name="from_name" required>
        </div>
        
        <div class="form-group">
            <label for="from_email">From Email:</label>
            <input type="email" id="from_email" name="from_email" required>
        </div>
        
        <div class="form-group">
            <label for="to_email">To Email:</label>
            <input type="email" id="to_email" name="to_email" required>
        </div>
        
        <div class="form-group">
            <label for="subject">Subject:</label>
            <input type="text" id="subject" name="subject" required>
        </div>
        
        <div class="form-group">
            <label for="message">Message (HTML supported):</label>
            <textarea id="message" name="message" required></textarea>
        </div>
        
        <div class="form-group">
            <label for="attachment">Attachment (optional):</label>
            <input type="file" id="attachment" name="attachment">
        </div>
        
        <button type="submit">Send Email</button>
    </form>
</body>
</html>