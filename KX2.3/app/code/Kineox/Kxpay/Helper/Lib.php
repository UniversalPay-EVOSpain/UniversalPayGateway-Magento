<?php
namespace Kineox\Kxpay\Helper;

use Magento\Framework\DB\Transaction;
 
class Lib extends \Magento\Framework\App\Helper\AbstractHelper {
    
    public static function generateIdLog() {
        $vars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $stringLength = strlen($vars);
        $result = '';
        for ($i = 0; $i < 20; $i++) {
            $result .= $vars[rand(0, $stringLength - 1)];
        }
        return $result;
    }
    public static function writeLog($idLog, $text, $active_log) {
    	if($active_log){
			$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/kxpay.log');
			$logger = new \Zend\Log\Logger();
			$logger->addWriter($writer);
			
			$logger->info("Kxpay".$idLog." - ".$text);
    	}
    }

	public static function RestoreCart($order, $cart, $formKey, $productRepository) {
		if ($order && $cart->getItemsCount() == 0) {
			$orderItems = $order->getAllItems();
			if ($orderItems) {
				foreach ($orderItems as $item) {
					$info = $item->getProductOptionByCode('info_buyRequest');
					$params = array(
						"form_key" => $formKey
					);
					$product = $productRepository->getById($item->getProductId());
					$cart->addProduct($product, $params + $info);
				}
				$cart->save();
			}
		}
	}
}