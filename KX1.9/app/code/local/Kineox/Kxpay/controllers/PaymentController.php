<?php
class Kineox_Kxpay_PaymentController extends Mage_Core_Controller_Front_Action {        
	
	public function formAction() {

		$lib = Mage::helper('kineox_kxpay/lib');
		
		$environment =Mage::getStoreConfig('payment/kxpay/environment',Mage::app()->getStore());
		$secure_key =Mage::getStoreConfig('payment/kxpay/secure_key',Mage::app()->getStore());
		$merchant_identifier =Mage::getStoreConfig('payment/kxpay/merchant_identifier',Mage::app()->getStore());
		$p1c =Mage::getStoreConfig('payment/kxpay/p1c',Mage::app()->getStore());
		$p1c_text =Mage::getStoreConfig('payment/kxpay/p1c_text',Mage::app()->getStore());
		$p1c_link =Mage::getStoreConfig('payment/kxpay/p1c_link',Mage::app()->getStore());
		$style_back_boton =Mage::getStoreConfig('payment/kxpay/style_back_boton',Mage::app()->getStore());
		$style_color_boton =Mage::getStoreConfig('payment/kxpay/style_color_boton',Mage::app()->getStore());
		$style_back_frame =Mage::getStoreConfig('payment/kxpay/style_back_frame',Mage::app()->getStore());
		$style_color_label =Mage::getStoreConfig('payment/kxpay/style_color_label',Mage::app()->getStore());
		$require_cardholder =Mage::getStoreConfig('payment/kxpay/require_cardholder',Mage::app()->getStore());
		$log =Mage::getStoreConfig('payment/kxpay/log',Mage::app()->getStore());

        $card = Mage::getStoreConfig('payment/kxpay/card',Mage::app()->getStore());
        $sofort = Mage::getStoreConfig('payment/kxpay/sofort',Mage::app()->getStore());
        $correos = Mage::getStoreConfig('payment/kxpay/correos',Mage::app()->getStore());
        $trustly = Mage::getStoreConfig('payment/kxpay/trustly',Mage::app()->getStore());
        $biocryptology = Mage::getStoreConfig('payment/kxpay/biocryptology',Mage::app()->getStore());
        $paypal =Mage::getStoreConfig('payment/kxpay/paypal',Mage::app()->getStore());
        $barzahlen = Mage::getStoreConfig('payment/kxpay/barzahlen',Mage::app()->getStore());
        $amazonpay = Mage::getStoreConfig('payment/kxpay/amazonpay',Mage::app()->getStore());
        $gpay = Mage::getStoreConfig('payment/kxpay/gpay',Mage::app()->getStore());
        $bizum = Mage::getStoreConfig('payment/kxpay/bizum',Mage::app()->getStore());
	
		switch ($environment) {
			case 0:
				$urltoken = 'https://test.imspagofacil.es/client2/token-pro';
				$urlenv = "https://test.imspagofacil.es/client2/load";
			break;
			case 1: 
				$urltoken = 'https://imspagofacil.es/client2/token-pro';
				$urlenv = "https://imspagofacil.es/client2/load";
			break;
		}	
		$_order = new Mage_Sales_Model_Order();
		$orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
		$_order->loadByIncrementId($orderId);

		$state = 'new';
		$status = 'pending';
		$comment = "Kxpay ha actualizado el estado del pedido con el valor ".$status;
		$isCustomerNotified = true;
		$_order->setState($state, $status, $comment, $isCustomerNotified);
		$_order->save();
		
		$customer = Mage::getSingleton('customer/session')->getCustomer(); 

		$data = array();
		$params = array();
		$url_store = Mage::getBaseUrl().'kxpay/payment/notify';

		$data['OPERATION'] = str_pad ($orderId.str_pad(time()%1000, 3, '0', STR_PAD_LEFT), 12, "0", STR_PAD_LEFT);
		$lib->writeLog("Order ID: '".$orderId."'. Order Number Gateway: '" . $data['OPERATION'] . "'", $log);
		$data['AMOUNT'] = (int)number_format($_order->getBaseGrandTotal(), 2, '', '');

		$params['LOCALE'] = Mage::app()->getLocale()->getLocaleCode();
		$params['CURRENCY'] = Mage::app()->getStore()->getCurrentCurrencyCode();
		$data['URL_RESPONSE'] = $url_store;
		$data['URL_OK'] = Mage::getBaseUrl().'checkout/onepage/success';
		$data['URL_KO'] = Mage::getBaseUrl().'checkout/onepage/failure';
		
		if ($style_back_boton != 'FFFFFF' || $style_color_boton != 'FFFFFF' || $style_back_frame != 'FFFFFF' || $style_color_label != 'FFFFFF'){
			if ($style_back_boton)
				$params['STYLE_BACK_BOTON'] = str_replace('#','', $style_back_boton);
			if ($style_color_boton)
				$params['STYLE_COLOR_BOTON'] = str_replace('#','', $style_color_boton);
			if ($style_back_frame)
				$params['STYLE_BACK_FRAME'] = str_replace('#','', $style_back_frame);
			if ($style_color_label)
				$params['STYLE_COLOR_LABEL'] = str_replace('#','', $style_color_label);
		}


		$params['TARGET'] = '_parent';

		if ($require_cardholder){
			$params['REQUIRE_CARDHOLDER'] = 'true';
		} else {
			$params['REQUIRE_CARDHOLDER'] = 'false';
		}
		$params['AMOUNT_MAX'] = $data['AMOUNT'];
		$params['AMOUNT_MIN'] = $data['AMOUNT'];

		if ($p1c && $customer->entity_id){
			$params['P1C'] = 'true';
		}
		$params['P1C_TEXT'] = $p1c_text;
		$params['P1C_LINK'] = $p1c_link;

		$params['IDENTIFIER'] = $customer->entity_id;
		$params['PERSONAL_IDENTITY_NUMBER'] = $customer->taxvat;

		$address = $_order->getShippingAddress();    

		$params['MAIL_BC'] = $customer->email;
		$params['TELEFONO_BC'] = $address->getTelephone();
		
		$params['PAYMENT_CHANNELS'] = array();
		if ($card == '1')
			$params['PAYMENT_CHANNELS'][] = "CARD"; 
		if ($sofort == '1')
			$params['PAYMENT_CHANNELS'][] = "SOFORT"; 
		if ($biocryptology == '1')
			$params['PAYMENT_CHANNELS'][] = "BIOCRYPTOLOGY"; 
		if ($bizum == '1')
			$params['PAYMENT_CHANNELS'][] = "BIZUM"; 
		if ($gpay == '1')
			$params['PAYMENT_CHANNELS'][] = "GOOGLE"; 
		if ($paypal == '1')
			$params['PAYMENT_CHANNELS'][] = "PAYPAL"; 
		if ($amazonpay == '1')
			$params['PAYMENT_CHANNELS'][] = "AMAZON"; 
		if ($trustly == '1')
			$params['PAYMENT_CHANNELS'][] = "TRUSTLY"; 
		if ($barzahlen == '1')
			$params['PAYMENT_CHANNELS'][] = "BARZAHLEN"; 
		if ($correos == '1')
			$params['PAYMENT_CHANNELS'][] = "CORREOS"; 
		$params['PAYMENT_CHANNELS'] = implode(';',$params['PAYMENT_CHANNELS']);	

		$data['MERCHANT_IDENTIFIER'] = $merchant_identifier;
		$data['PARAMS'] = $params;

		$data['SIGNATURE'] = hash("sha256",$data["MERCHANT_IDENTIFIER"].$data["AMOUNT"].$data['OPERATION'].$data["URL_RESPONSE"].$data["URL_OK"].$data["URL_KO"].$secure_key, FALSE );
		
				   
		$data_string = json_encode($data);
		$ch = curl_init($urltoken);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST" );
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_HTTPHEADER, array (
			'Content-Type: application/json',
			'Content-Length: ' . strlen ( $data_string ) 
		));
		$response = curl_exec($ch);
		curl_close($ch);
		$result_string = json_decode($response, TRUE);
		$token = $result_string ["TOKEN"];
		
		$this->loadLayout();
		
		$this->getLayout()->getBlock("kxpay_paymentform")->setKXURL($urlenv);
		$this->getLayout()->getBlock("kxpay_paymentform")->setKXToken($token);
		$this->getLayout()->getBlock("kxpay_paymentform")->setCustomData($data);
		
		$this->renderLayout ();
	}

	
    public function notifyAction()
    {
		$lib=Mage::helper("kineox_kxpay/lib");
		
		$idLog = $lib->generateIdLog();
		$active_log =Mage::getStoreConfig('payment/kxpay/log',Mage::app()->getStore());
		$key =Mage::getStoreConfig('payment/kxpay/secure_key',Mage::app()->getStore());
		$merchant_identifier =Mage::getStoreConfig('payment/kxpay/merchant_identifier',Mage::app()->getStore());
		$orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
		$order_status =Mage::getStoreConfig('payment/kxpay/order_status',Mage::app()->getStore());
		if($order_status==="")
			$order_status="Processing";

		$lib->writeLog($idLog.' Order validation started.', $active_log);
		
		$json = file_get_contents('php://input');
		$array = json_decode($json,false);
		if (!empty($array)){
			if ( $array->PAYMENT_CODE == '000' || $array->PAYMENT_CODE == '010' ) {
				$received_signature	= $array->SIGNATURE;
				$data = array (
					"MERCHANT_IDENTIFIER" => $array->MERCHANT_IDENTIFIER,
					"MERCHANT_OPERATION" => $array->MERCHANT_OPERATION,
					"PAYMENT_AMOUNT" => $array->PAYMENT_AMOUNT,
					"PAYMENT_OPERATION" => $array->PAYMENT_OPERATION,
					"PAYMENT_CHANNEL" => $array->PAYMENT_CHANNEL,
					"PAYMENT_DATE" => $array->PAYMENT_DATE,
					"PAYMENT_CODE" => $array->PAYMENT_CODE
				);
				
				$signature = hash ( "sha256", $data["MERCHANT_IDENTIFIER"].$data["PAYMENT_AMOUNT"].$data["MERCHANT_OPERATION"].$data["PAYMENT_OPERATION"].$data["PAYMENT_CHANNEL"].$data["PAYMENT_DATE"].$data["PAYMENT_CODE"].$key, FALSE );

				$order_id = (int)substr($data['MERCHANT_OPERATION'], 0, -3);
				$order = Mage::getModel('sales/order')->loadByIncrementId($order_id);

				if (strtolower($order->getState()) == 'processing')
					die();

				if ($signature == $received_signature) {  
					$lib->writeLog($idLog.' Merchant Operation: '.$data['MERCHANT_OPERATION'], $active_log);
					
					if(!$order->canInvoice()) {
						die();
					} else {
						$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
						$invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
						$invoice->register();
						$invoice->getOrder()->setCustomerNoteNotify(true);
						$order->addStatusHistoryComment($this->__('KxPay ha generado la Factura del pedido'), false);
						$transactionSave = Mage::getModel('core/resource_transaction')
							->addObject($invoice)
							->addObject($invoice->getOrder());
						$transactionSave->save();
						$order->sendNewOrderEmail();
						
						//Se actualiza el pedido
						$state = 'new';
						$comment = $this->__("Kxpay ha actualizado el estado del pedido con el valor").' "'.$order_status.'"';
						$isCustomerNotified = true;
						$order->setState($state, $order_status, $comment, $isCustomerNotified);
						$order->save();
						$lib->writeLog($idLog." El pedido con ID " . $orderId . " es válido y se ha registrado correctamente.",$active_log);
					}

					die();
					
				} else {
					$lib->writeLog($idLog.' Signatures don`t match: '.$received_signature.' - '.$signature, $active_log);
					$state = 'new';
					$status = 'canceled';
					$order->setState($state, $status, $comment, false);
					$order->registerCancellation("")->save();
					$order->save();
					die();
				}
			} else {
				$lib->writeLog($idLog.' ERROR: Payment Code '.$array->PAYMENT_CODE, $active_log);
				die();
			}
		} else {
			$lib->writeLog($idLog.' No data received', $active_log);
			die();
		}
	}

}
