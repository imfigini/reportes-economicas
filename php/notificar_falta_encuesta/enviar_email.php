<?php

require_once ('/home/soporte/toba_2.7.13/php/3ros/phpmailer/class.phpmailer.php');
//require_once (toba_dir() . "/proyectos/Encuestas/php/abm_de_relevamientos/defines.php");
//require_once (CONSULTAS_PATH."consultas_toba.php");

class enviar_email
{
	static function getMailer() {
		$mail = new PHPMailer;
		$mail->IsSMTP();
		$mail->CharSet = 'UTF-8';
		$mail->Host       = "smtp.exa.unicen.edu.ar:25"; // SMTP server example
		$mail->SMTPDebug  = 1;                     // enables SMTP debug information (for testing)
		$mail->SMTPAuth   = false;                  // enable SMTP authentication
		$mail->Port       = 25;                    // set the SMTP port for the GMAIL server
		$mail->Username   = 'info-guarani'; 			//MisConsultas::get_parametro("Username"); //"mantis"; // SMTP account username example
		$mail->Password   = 'XR3u5JRz69'; 	//MisConsultas::get_parametro("Password"); //"PdIUI2787";        // SMTP account password example
		//$mail->SMTPSecure = ''; 
		$mail->Subject 	  = 'Aviso de encuestas pendientes de completar';		//MisConsultas::get_parametro("Subject");
		$mail->FromName   = 'Guarani';								//MisConsultas::get_parametro("FromName");
		$mail->From 	  = 'info-guarani@exa.unicen.edu.ar ';						//MisConsultas::get_parametro("FromMail");
		
		return $mail;
	}
	
	public static function enviarMail($envio, $agente) {
				
		$toMail = $agente['E_MAIL'];
		$toName = $agente['ALUMNO'];
		$bcc = 'info-guarani@slab.exa.unicen.edu.ar';
		
		$texto = "Estimado/a <b>$toName:</b> ";
		$texto .= "<p>Con motivo de una pequeña falla en el sistema Guarani, ud. ha podido inscribirse a cursada sin el requisito de completar las encuestas correspondientes al cuatrimestre anterior. 
				Sólo por esta vez y a modo de subsanar dicho inconveneinete es que se le solicitará que las complete la primera vez que solicite un certificado o la primera vez que desee inscribirse a una mesa de examen, lo que ocurra primero. 
				Por tal motivo le recomendamos que tenga en cuenta esta eventualidad para que lo tenga en consideración.";
		$texto .= "<p><i>Este mensaje fue originado automaticamente. Por favor no responder al mismo.</i></p>";

		$mail = enviar_email::getMailer();

		$mail->addAddress($toMail, $toName);			
		$mail->AddBCC($bcc, $bcc);			
		//$mail->AddAttachment($fileName);         // Add attachments

		$mail->Body    = $texto;
		$mail->AltBody = $texto;
		
		
		if(!$mail->Send()) {
		   return array(false, $mail->ErrorInfo);
		} 
		
		return array(true, '');			
	}
}
?>