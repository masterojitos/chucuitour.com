<?php
// Load PHP Mailer
//
require 'PHPMailer/PHPMailerAutoload.php';
$mail = new PHPMailer;

/*
|--------------------------------------------------------------------------
| Configure your contact form
|--------------------------------------------------------------------------
|
| Set value of '$receiver' to email address that want to receive inquiries.
| Also, '$default_subject' is the subject that you'll see in your inbox.
|
| It's better to set `$sender_email` and `$sender_name` values, so there's
| more chance to receive the email at gmail, yahoo, hotmail, etc.
|
*/
$receiver        = "hola@chucuitour.com";
// $receiver        = "masterojitos@gmail.com";
$default_subject = "Chucuitour - Reserva Web";

$sender_email    = "chucuitour@gmail.com";
$sender_name     = "Chucuitour";
$error_message   = "Ocurrió un error. Por favor, intente luego.";


/*
|--------------------------------------------------------------------------
| Configure PHP Mailer
|--------------------------------------------------------------------------
|
| By default, we're using the default configuration. If you need to change
| default settings or use a custion SMTP server, do it here.
|
| More info: https://github.com/PHPMailer/PHPMailer
|
*/
$mail->isSMTP();
$mail->SMTPAuth = true;
$mail­->SMTPSecure = "ssl";
$mail->CharSet = 'UTF-8';
// $mail->Host = 'smtp.gmail.com';
// $mail->Port = 465;
// $mail->Username = "chucuitour@gmail.com";
// $mail->Password = "Chucuitour2018";
$mail->Host = 'smtp.sendgrid.net';
$mail->Port = 587;
$mail->Username = 'teclasend';
$mail->Password = 'Tecl@send000';
$mail->isHTML(true);


/*
|--------------------------------------------------------------------------
| Sending email
|--------------------------------------------------------------------------
|
| This part of code is responsible to send the email. So you don't need to
| change anything here.
|
*/

$email = $_POST['email'];
if ( ! empty( $email ) && filter_var( $email, FILTER_VALIDATE_EMAIL ) )
{

  // detect & prevent header injections
  //
  $test = "/(content-type|bcc:|cc:|to:)/i";
  foreach ( $_POST as $key => $val ) {
    if ( preg_match( $test, $val ) ) {
      exit;
    }
  }


  // Sender name
  //
  $name = '';
  if ( isset( $_POST['name'] ) ) {
    $name = $_POST['name'];
  }

  if ( isset( $_POST['firstname'] ) && isset( $_POST['lastname'] ) ) {
    $name = $_POST['firstname'] .' '. $_POST['lastname'];
  }


  // Email subject
  //
  $subject = '';
  if ( isset( $_POST['subject'] ) ) {
    $subject = $_POST['subject'];
  }

  if ($subject == "") {
    $subject = $default_subject;
  }

  if ( ! empty( $name ) ) {
    // $subject .= ' - By '. $name;
  }


  // Message content
  //
  $content = '';

  // Attach other input values to the end of message
  //
  unset( $_POST['subject'] );
  foreach ($_POST as $key => $value) {
    $key = str_replace( array('-', '_'), ' ', $key);
    $content .= '<p><b>'. ucfirst($key) .'</b><br>'. nl2br( $value ) .'</p>';
  }


  // Prepare PHP Mailer
  //
  $mail->setFrom($sender_email, $sender_name);
  $mail->addAddress($receiver);
  $mail->addReplyTo($email, $name);
  $mail->Subject = $subject;
  $mail->Body    = $content;
  $mail->AltBody = strip_tags($content);
  if( !$mail->send() ) {
    echo json_encode( array(
      'status'  => 'error',
      'message' => $error_message,
      'reason'  => $mail->ErrorInfo,
    ));
  } else {
    echo json_encode( array( 'status' => 'success' ) );
  }

}


?>
