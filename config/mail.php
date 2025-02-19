<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';  // Autoload PHPMailer classes

class Mail
{
    private PHPMailer $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);

        try {
            // SMTP Configuration
            $this->mail->isSMTP();
            $this->mail->Host = 'smtp.gmail.com';
            $this->mail->SMTPAuth = true;
            $this->mail->Username = 'your_email@gmail.com';  // Your Gmail address
            $this->mail->Password = 'your_password';  // Your Gmail password (⚠️ NOT SECURE! Use an App Password)
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port = 587;

            // Sender Info
            $this->mail->setFrom('your_email@gmail.com', 'EcoRide');
        } catch (Exception $e) {
            error_log("Mailer Error: " . $this->mail->ErrorInfo);
        }
    }

    // Get user email from database using user_id
    public function getUserEmail($user_id, $db)
    {
        $sql = "SELECT email FROM users WHERE user_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        return $user ? $user['email'] : false;
    }

    // Send email to the user based on their user_id
    public function sendMail($user_id, $subject, $message, $db)
    {
        try {
            // Fetch recipient email dynamically from the database
            $recipient_email = $this->getUserEmail($user_id, $db);
            if (!$recipient_email) {
                throw new Exception("User email not found");
            }

            // Clear previous recipients
            $this->mail->clearAddresses();
            $this->mail->addAddress($recipient_email);  // Add the recipient email dynamically

            // Set email content
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body = $message;

            // Send Email
            if ($this->mail->send()) {
                return "Email successfully sent to $recipient_email";
            } else {
                return "Failed to send email to $recipient_email. Error: " . $this->mail->ErrorInfo;
            }
        } catch (Exception $e) {
            return "Mail sending failed: " . $e->getMessage();
        }
    }
}

