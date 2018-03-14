<?php
/**
 *  Yogurt : MVC Development Framework with PHP<http://www.yogurtframework.com/>
 *
 * @author          rick <158672319@qq.com>
 * @copyright		Copyright (c)2009-2013
 * @link			http://www.yogurtframework.com
 * @license         http://www.yogurtframework.com/license/
 */
/**
 * mailer class of yogurt framework.
 * @filesource		yogurt/utils/Mailer.class.php
 * @since			YOGURT v 0.9
 * @version			$3.0
 */
//require (LIB_DIR.'phpmailer-2.30/class.phpmailer.php');
require (LIB_DIR.'PHPMailer/PHPMailerAutoload.php');
class Mailer extends PHPMailer{
    public function Mailer() {
    	global $MAILCONF;
        $this->CharSet = $MAILCONF['mail']['charset'];
	    $this->ContentType = $MAILCONF['mail']['contenttype'];
	    $this->Encoding = $MAILCONF['mail']['encoding'];
	    $this->From = $MAILCONF['mail']['from'];
	    $this->FromName = $MAILCONF['mail']['name']; // 收件标题
	    $this->Mailer = $MAILCONF['mail']['mailer'];
	    //$this->Sendmail = $MAILCONF['mail']['path'];
	    $this->Hostname = $MAILCONF['mail']['host'];
	    //smtp config
	    $this->Host = $MAILCONF['smtp']['host'];
	    $this->Port = $MAILCONF['smtp']['port'];
	    $this->Username = $MAILCONF['smtp']['user'];
	    $this->Password = $MAILCONF['smtp']['password'];
	    $this->SMTPAuth = $MAILCONF['smtp']['auth'];
	    $this->SMTPSecure = $MAILCONF['smtp']['secure'];
	    $this->WordWrap = $MAILCONF['smtp']['wordwrap'];
    }

    public function send() {
        
    }
}
?>
