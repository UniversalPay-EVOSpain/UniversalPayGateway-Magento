<?php

if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION); //5.2.7 ->  50207       5.5.28 -> 50528
    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}

class Kineox_Kxpay_Helper_Lib extends Mage_Core_Helper_Abstract {
	
	private static $errors=null;
	
	function generateIdLog() {
		$vars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$stringLength = strlen($vars);
		$result = '';
		for ($i = 0; $i < 20; $i++) {
			$result .= $vars[rand(0, $stringLength - 1)];
		}
		return $result;
	}
	function writeLog($text,$active) {
		if($active){
			Mage::log('KxPay: '.$text);
			$logfilename = 'Logs/kxpay.log';
			$fp = @fopen($logfilename, 'a');
			if ($fp) {
				fwrite($fp, date('d/m/Y H:i:s') . ' | ' . $text . "\r\n");
				fclose($fp);
			}
		}
	}
	
}