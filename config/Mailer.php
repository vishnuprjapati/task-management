<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require  __DIR__ . '/../vendor/autoload.php';

class Mailer {
    private $mail;

    public function __construct() {
        $this->mail = new PHPMailer(true);
        
        // Server settings
        $this->mail->isSMTP();
        $this->mail->Host = ''; // Your SMTP server
        $this->mail->SMTPAuth = true;
        $this->mail->Username = '';
        $this->mail->Password = '';
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = 587;       
        // Sender info
        $this->mail->setFrom('prajaptivishnu11@gmail.com', 'Task Manager');
    }

    public function send($to, $subject, $body) {
        try {
            $this->mail->addAddress($to);
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body = $body;
            
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer Error: {$this->mail->ErrorInfo}");
            return false;
        }
    }

    // public function send($to, $subject, $body, $data = []) {
    //     try {
    //         $this->mail->addAddress($to);
    //         $this->mail->isHTML(true);
    //         $this->mail->Subject = $subject;
    
    //         // Process template with data
    //         if (is_array($body)) { // ✅ fixed missing parenthesis
    //             extract($body['data']);
    //             ob_start();
    //             include __DIR__.'/../views/emails/'.$body['template'];
    //             $this->mail->Body = ob_get_clean();
    //         } else {
    //             $this->mail->Body = $body;
    //         }
    
    //         $this->mail->send();
    //         return true;
    //     } catch (Exception $e) {
    //         error_log("Mailer Error: {$this->mail->ErrorInfo}");
    //         return false;
    //     }
    // }
    
}
?>