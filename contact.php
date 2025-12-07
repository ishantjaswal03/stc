<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Set content type to JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cf-submitted'])) {
    // Get form data and sanitize
    $name = htmlspecialchars(trim($_POST['cf-name'] ?? ''));
    $email = filter_var(trim($_POST['cf-email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $company = htmlspecialchars(trim($_POST['cf-company'] ?? ''));
    $industry = htmlspecialchars(trim($_POST['cf-industry'] ?? ''));
    $message = htmlspecialchars(trim($_POST['cf-message'] ?? ''));
    
    // Validate required fields
    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode([
            'success' => false,
            'message' => 'Please fill in all required fields.'
        ]);
        exit;
    }
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false,
            'message' => 'Please enter a valid email address.'
        ]);
        exit;
    }
    
    // Create PHPMailer instance
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ankit@sapphiretechconsulting.com';  // Your Google Workspace email
        $mail->Password   = 'zoykmxxnksaqrldn';     // Replace with your App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Recipients
        $mail->setFrom('hello@sapphiretechconsulting.com', 'Sapphire Consulting Website');
        $mail->addAddress('contact@sapphiretechconsulting.com');  // Your contact group
        $mail->addReplyTo($email, $name);  // When you reply, it goes to the form user
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = "New Project Request from " . $name . ($company ? " - " . $company : "");
        
        // Email body
        $mail->Body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .header { background: #28b274; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { padding: 25px; background: #f9f9f9; }
                .field { margin-bottom: 15px; padding: 10px; background: white; border-radius: 5px; border-left: 4px solid #61FFB1; }
                .label { font-weight: bold; color: #28b274; display: block; margin-bottom: 5px; }
                .value { color: #333; }
                .message-field { background: white; padding: 15px; border-radius: 5px; border-left: 4px solid #61FFB1; margin-top: 10px; }
                .footer { background: #28b274; color: white; padding: 15px; text-align: center; font-size: 12px; border-radius: 0 0 10px 10px; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h2>🚀 New Project Request - Sapphire Consulting</h2>
            </div>
            <div class='content'>
                <div class='field'>
                    <span class='label'>👤 Name:</span>
                    <span class='value'>$name</span>
                </div>
                <div class='field'>
                    <span class='label'>📧 Email:</span>
                    <span class='value'>$email</span>
                </div>";
        
        if (!empty($company)) {
            $mail->Body .= "
                <div class='field'>
                    <span class='label'>🏢 Company:</span>
                    <span class='value'>$company</span>
                </div>";
        }
        
        if (!empty($industry)) {
            $mail->Body .= "
                <div class='field'>
                    <span class='label'>🏭 Industry:</span>
                    <span class='value'>$industry</span>
                </div>";
        }
        
        $mail->Body .= "
                <div class='field'>
                    <span class='label'>📋 Project Details:</span>
                    <div class='message-field'>" . nl2br($message) . "</div>
                </div>
            </div>
            <div class='footer'>
                <p>📧 Sent from: sapphiretechconsulting.com</p>
                <p>📅 Date: " . date('Y-m-d H:i:s T') . "</p>
                <p>🌐 IP Address: " . $_SERVER['REMOTE_ADDR'] . "</p>
            </div>
        </body>
        </html>";
        
        // Send email
        $mail->send();
        
        echo json_encode([
            'success' => true,
            'message' => 'Thank you for your project request! We will review your requirements within 24 hours and get back to you with next steps.'
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Message could not be sent. Please email us directly at contact@sapphiretechconsulting.com. Error: ' . $mail->ErrorInfo
        ]);
    }
    
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
}
?>