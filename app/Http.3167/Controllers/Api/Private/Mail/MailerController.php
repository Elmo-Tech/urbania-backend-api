<?php

namespace App\Http\Controllers\Api\Private\Mail;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
 
class MailerController extends Controller 
{
 
    /**
    * Write code on Method
    *
    * @return response()
    */ 
    /*public function email() {
        return view("email");
    }*/
 
    /**
    * Write code on Method
    *
    * @return response()
    */ 
    public function composeemail(Request $request) {
        require base_path("vendor/autoload.php");
            
        $mail = new PHPMailer(true);     // Passing `true` enables exceptions
        try {
 
            // Email server settings
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = env('MAIL_HOST');             //  smtp host
            $mail->SMTPAuth = true;
            $mail->Username = env('MAIL_USERNAME');   //  sender username
            $mail->Password = env('MAIL_PASSWORD');       // sender password
            $mail->SMTPSecure = 'ssl';                  // encryption - ssl/tls
            $mail->Port = env('MAIL_PORT');                          // port - 587/465
            $mail->setFrom('mr10dev10@gmail.com', 'SenderName');
            $mail->addAddress($request->emailRecipient);
            //$mail->addCC($request->emailCc);
            //$mail->addBCC($request->emailBcc);

            $mail->addReplyTo('sender@example.com', 'SenderReplyName');
 
            if(isset($_FILES['emailAttachments'])) {
                for ($i=0; $i < count($_FILES['emailAttachments']['tmp_name']); $i++) {
                    $mail->addAttachment($_FILES['emailAttachments']['tmp_name'][$i], $_FILES['emailAttachments']['name'][$i]);
                }
            }
 
 
            $mail->isHTML(true);                // Set email content format to HTML
 
            $mail->Subject = $request->emailSubject;
            $mail->Body    = $request->emailBody;
            // $mail->AltBody = plain text version of email body;
 
            if( !$mail->send() ) {
                return response()->json([
                    'message' => 'NO SEND !'
                ], 401);
        
            }
            
            else {
                return response()->json([
                    'message' => 'message has been sent !'
                ], 200);
        
            }
 
        } catch (Exception $e) {
             return back()->with('error','Message could not be sent.');
        }
    }
}    
