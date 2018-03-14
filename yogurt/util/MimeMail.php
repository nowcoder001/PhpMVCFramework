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
 * MimeMail class of yogurt framework.
 * @filesource		yogurt/utils/MimeMail.class.php
 * @since			YOGURT v 0.9
 * @version			$3.0
 */

include_once (LIB_DIR.'html_mime_mail_2.5/htmlMimeMail.php');
class MimeMail extends htmlMimeMail{
	
	/**
	 * construts of MimeMail
	 */
    public function MimeMail() {
    	$this->build_params['html_encoding'] = 'quoted-printable';
        $this->build_params['text_encoding'] = '7bit';
        $this->build_params['html_charset']  = 'utf-8';
        $this->build_params['text_charset']  = 'utf-8';
        $this->build_params['head_charset']  = 'utf-8';

		$this->smtp_params['host'] = 'localhost';
        $this->smtp_params['port'] = 25;
        $this->smtp_params['auth'] = false;
        $this->smtp_params['user'] = '';
        $this->smtp_params['pass'] = '';
    }
}
?>