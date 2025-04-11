<?php
/**
 * 邮件发送函数
 * @copyright			(C) 2005-2010
 * @lastmodify			2021-06-06
 */
function runlog($server, $name, $msg) {
	if (is_gb2312($msg) && function_exists('iconv')) {
		$new = iconv('GB2312', 'UTF-8', $msg);
		if ($new) {
			$msg = $new;
		}
	}
	$error = $name.'-'.$msg;
	@file_put_contents(CACHE_PATH.'email_log.php', date('Y-m-d H:i:s').' ['.$server.'] '.str_replace(array(PHP_EOL, chr(13), chr(10)), '', $msg).PHP_EOL, FILE_APPEND);
}

function is_gb2312($str) {
	return function_exists('mb_detect_encoding') && mb_detect_encoding($str,"UTF-8, ISO-8859-1, GBK")!="UTF-8";
}

/**
 * 发送邮件
 * @param $toemail 收件人email
 * @param $subject 邮件主题
 * @param $message 正文
 * @param $from 发件人
 * @param $cfg 邮件配置信息
 * @param $sitename 邮件站点名称
 */

function sendmail($toemail, $subject, $message, $from='',$cfg = array(), $sitename='') {
	if($sitename=='') {
		$sitename = dr_site_info('site_title', get_siteid());
	}
	
	if($cfg && is_array($cfg)) {
		$from = $cfg['from'];
		$mail = $cfg;
		$mail_type = $cfg['mail_type']; //邮件发送模式
	} else {
		$cfg = getcache('common','commons');
		$from = $cfg['mail_from'];
		$mail_type = $cfg['mail_type']; //邮件发送模式
		if($cfg['mail_user']=='' || $cfg['mail_password'] ==''){
			return false;
		}
		$mail= array (
			'mailsend' => 2,
			'maildelimiter' => 1,
			'mailusername' => 1,
			'server' => $cfg['mail_server'],
			'port' => $cfg['mail_port'],
			'auth' => $cfg['mail_auth'],
			'from' => $cfg['mail_from'],
			'auth_username' => $cfg['mail_user'],
			'auth_password' => $cfg['mail_password']
		);		
	}
	//mail 发送模式
	if($mail_type==0) {
		$subject = "=?UTF-8?B?".base64_encode($subject)."?=";
        $from_user = "=?UTF-8?B?".base64_encode($sitename)."?=";
        $headers = "From: ".$from_user." <".$from.">\r\n".
            "MIME-Version: 1.0" . "\r\n" .
            "Content-type: text/html; charset=".CHARSET."" . "\r\n";
		mail($toemail, $subject, $message, $headers);
		return true;
	}

	$is_starttls = 0;
	if (strpos($mail['server'], 'starttls://') === 0) {
		$is_starttls = 1;
		$mail['server'] = str_replace('starttls://', '', $mail['server']);
	}

	$mailusername = isset($mail['mailusername']) ? $mail['mailusername'] : 1;
	$maildelimiter = $mail['maildelimiter'] == 1 ? "\r\n" : ($mail['maildelimiter'] == 2 ? "\r" : "\n"); //换行符
	$mail['port'] = $mail['port'] ? $mail['port'] : 25;

	$email_from = '=?'.CHARSET.'?B?'.base64_encode($sitename)."?= <".$mail['from'].">";
	$email_to = preg_match('/^(.+?) \<(.+?)\>$/',$toemail, $mats) ? ($mailusername ? '=?'.CHARSET.'?B?'.base64_encode($mats[1])."?= <$mats[2]>" : $mats[2]) : $toemail;
	$email_subject = '=?'.CHARSET.'?B?'.base64_encode(preg_replace("/[\r|\n]/", '', '['.$sitename.'] '.$subject)).'?=';
	$email_message = chunk_split(base64_encode(str_replace("\n", "\r\n", str_replace("\r", "\n", str_replace("\r\n", "\n", str_replace("\n\r", "\r", $message))))));

	$host = $_SERVER['HTTP_HOST'];
	$headers = "From: $email_from{$maildelimiter}X-Priority: 3{$maildelimiter}X-Mailer: $host {$maildelimiter}MIME-Version: 1.0{$maildelimiter}Content-type: text/html; charset=".CHARSET."{$maildelimiter}Content-Transfer-Encoding: base64{$maildelimiter}";


	if(!$fp = fsockopen($mail['server'], $mail['port'], $errno, $errstr, 30)) {
		runlog($mail['server'].' - '.$mail['auth_username'].' - '.$toemail, "fsockopen 无法连接到邮件服务器 [".$errno."-".$errstr."]");
		return FALSE;
	}

	stream_set_blocking($fp, true);
	$lastmessage = fgets($fp, 512);
	if(substr($lastmessage, 0, 3) != '220') {
		runlog($mail['server'].' - '.$mail['auth_username'].' - '.$toemail, "连接失败", $lastmessage);
		return FALSE;
	}

	fputs($fp, ($mail['auth'] ? 'EHLO' : 'HELO')." cms\r\n");
	$lastmessage = fgets($fp, 512);
	if(substr($lastmessage, 0, 3) != 220 && substr($lastmessage, 0, 3) != 250) {
		runlog($mail['server'].' - '.$mail['auth_username'].' - '.$toemail, "HELO/EHLO", $lastmessage);
		return FALSE;
	}

	// 是否starttls
	if ($is_starttls) {
		fputs($fp, "STARTTLS cms\r\n");
		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 0, 3) != 220 && substr($lastmessage, 0, 3) != 250) {
			runlog($mail['server'].' - '.$mail['auth_username'].' - '.$toemail, "STARTTLS", $lastmessage);
			return FALSE;
		}
	}

	while(1) {
		if(substr($lastmessage, 3, 1) != '-' || empty($lastmessage)) {
			break;
		}
		$lastmessage = fgets($fp, 512);
	}

	//$crypto = stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);

	// 登录账号认证
	if($mail['auth']) {
		fputs($fp, "AUTH LOGIN\r\n");
		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 0, 3) != 334) {
			runlog($mail['server'].' - '.$mail['auth_username'].' - '.$toemail, "认证登录失败", $lastmessage);
			return FALSE;
		}
		fputs($fp, base64_encode($mail['auth_username']) . "\r\n");
		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 0, 3) != 334) {
			runlog($mail['server'].' - '.$mail['auth_username'].' - '.$toemail, "账号验证失败", $lastmessage);
			return FALSE;
		}
		fputs($fp, base64_encode($mail['auth_password']) . "\r\n");
		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 0, 3) != 235) {
			runlog($mail['server'].' - '.$mail['auth_username'].' - '.$toemail, "密码验证失败", $lastmessage);
			return FALSE;
		}

		$email_from = $mail['from'];
	}

	fputs($fp, "MAIL FROM: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $email_from).">\r\n");
	$lastmessage = fgets($fp, 512);
	if(substr($lastmessage, 0, 3) != 250) {
		fputs($fp, "MAIL FROM: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $email_from).">\r\n");
		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 0, 3) != 250) {
			runlog($mail['server'].' - '.$mail['auth_username'].' - '.$toemail, "MAIL FROM", $lastmessage);
			return FALSE;
		}
	}

	fputs($fp, "RCPT TO: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $toemail).">\r\n");
	$lastmessage = fgets($fp, 512);
	if(substr($lastmessage, 0, 3) != 250) {
		fputs($fp, "RCPT TO: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $toemail).">\r\n");
		$lastmessage = fgets($fp, 512);
		runlog($mail['server'].' - '.$mail['auth_username'].' - '.$toemail, "RCPT TO", $lastmessage);
		return FALSE;
	}
	fputs($fp, "DATA\r\n");
	$lastmessage = fgets($fp, 512);
	if(substr($lastmessage, 0, 3) != 354) {
		runlog($mail['server'].' - '.$mail['auth_username'].' - '.$toemail, "DATA", $lastmessage);
		return FALSE;
	}
	$headers .= 'Message-ID: <'.gmdate('YmdHs').'.'.substr(md5($email_message.microtime()), 0, 6).get_rand_value().'@'.$_SERVER['HTTP_HOST'].">{$maildelimiter}";

	fputs($fp, "Date: ".gmdate('r')."\r\n");
	fputs($fp, "To: ".$email_to."\r\n");
	fputs($fp, "Subject: ".$email_subject."\r\n");
	fputs($fp, $headers."\r\n");
	fputs($fp, "\r\n\r\n");
	fputs($fp, "$email_message\r\n.\r\n");
	$lastmessage = fgets($fp, 512);
	if(substr($lastmessage, 0, 3) != 250) {
		runlog($mail['server'].' - '.$mail['auth_username'].' - '.$toemail, "END", $lastmessage);
		return FALSE;
	}
	fputs($fp, "QUIT\r\n");
	return TRUE;
}
?>