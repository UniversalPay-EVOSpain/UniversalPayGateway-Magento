<?php
namespace Kineox\Kxpay\Controller\Checkout;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Checkout\Model\Session;
use Magento\Store\Model\StoreManagerInterface; 
use Kineox\Kxpay\Controller\KxpayController;


class Paymentform extends \Magento\Framework\App\Action\Action {
	protected $_session;
	protected $_resultPageFactory;
	protected $_storeManager;
	protected $_kxpayController;

	public function __construct(Context $context, PageFactory $resultPageFactory, Session $session, StoreManagerInterface $storeManager, KxpayController $kxpayController) {
		$this->_session = $session;
		$this->_resultPageFactory = $resultPageFactory;
		$this->_storeManager = $storeManager;
		$this->_kxpayController = $kxpayController;
		return parent::__construct($context);
	}

	private function validateRequest(
		HttpRequest $request,
		ActionInterface $action
	) {
		$valid = null;
		if ($action instanceof CsrfAwareActionInterface) {
			$valid = $action->validateForCsrf($request);
		}
		if ($valid === null) {
			$valid = !$request->isPost()
				|| $request->isAjax()
				|| $this->formKeyValidator->validate($request);
		}

		return $valid;
	}

	public function get_session() {
		return $this->_session;
	}

	public function get_resultPageFactory() {
		return $this->_resultPageFactory;
	}

	public function get_storeManager() {
		return $this->_storeManager;
	}

	public function get_kxpayController() {
		return $this->_kxpayController;
	}

	public function execute() {
		$session = $this->get_session();
		$resultPageFactory = $this->get_resultPageFactory();
		$kxpayController = $this->get_kxpayController();

        $_order = $session->getLastRealOrder();
        $orderId = $_order->getId();
        
		$state = 'new';
		$status = 'pending';
		$comment = "Kxpay ha actualizado el estado del pedido con el valor ".$status;
		$isCustomerNotified = true;
		$_order->setState($state, $status, $comment, $isCustomerNotified);
		$_order->save();
        
        $environment = $this->_kxpayController->get_environment();
        $secure_key = $this->_kxpayController->get_secure_key();
        $merchant_identifier = $this->_kxpayController->get_merchant_identifier();
        $p1c = $this->_kxpayController->get_p1c();
        $p1c_text = $this->_kxpayController->get_p1c_text();
        $p1c_link = $this->_kxpayController->get_p1c_link();
        $style_back_boton = $this->_kxpayController->get_style_back_boton();
        $style_color_boton = $this->_kxpayController->get_style_color_boton();
        $style_back_frame = $this->_kxpayController->get_style_back_frame();
        $style_color_label = $this->_kxpayController->get_style_color_label();
		$require_cardholder = $this->_kxpayController->get_require_cardholder();
		
        $card = $this->_kxpayController->get_card();
        $sofort = $this->_kxpayController->get_sofort();
        $correos = $this->_kxpayController->get_correos();
        $trustly = $this->_kxpayController->get_trustly();
        $biocryptology = $this->_kxpayController->get_biocryptology();
        $paypal = $this->_kxpayController->get_paypal();
        $barzahlen = $this->_kxpayController->get_barzahlen();
        $amazonpay = $this->_kxpayController->get_amazonpay();
        $gpay = $this->_kxpayController->get_gpay();
        $bizum = $this->_kxpayController->get_bizum();
        
        $url_endpoint = $this->_kxpayController->get_baseURL()."kxpay/checkout/notify";
        $url_ko = $this->_kxpayController->get_baseURL()."kxpay/checkout/error";
        $url_ok = $this->_kxpayController->get_baseURL()."kxpay/checkout/success";
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

		$data['OPERATION'] = str_pad ($orderId.str_pad(time()%1000, 3, '0', STR_PAD_LEFT), 12, "0", STR_PAD_LEFT);
		
        $data['AMOUNT'] = (int)number_format($_order->getBaseGrandTotal(), 2, '', '');
        
		$params['LOCALE'] = $this->_kxpayController->get_storeLanguage();
		$params['CURRENCY'] = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
		$data['URL_RESPONSE'] = $url_endpoint;
		$data['URL_OK'] = $url_ok;
        $data['URL_KO'] = $url_ko;
        
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
        
        $address = $_order->getBillingAddress()->getData();
		$taxvat = $_order->getCustomerTaxvat();
		$email = $_order->getCustomerEmail();
		$customer_id = $_order->getCustomerId();

		if ($p1c && $customer_id){
			$params['P1C'] = 'true';
		}
		$params['P1C_TEXT'] = $p1c_text;
		$params['P1C_LINK'] = $p1c_link;

		$params['IDENTIFIER'] = $customer_id;
		$params['PERSONAL_IDENTITY_NUMBER'] =  $taxvat;

		$params['MAIL_BC'] = $address['email'];
		$params['TELEFONO_BC'] = $address['telephone'];
		
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

		$resultPage = $resultPageFactory->create();
		$resultPage->getConfig()->getTitle()->prepend(__("Pago mediante Kxpay"));
		$resultPage->getLayout()->initMessages();
		$resultPage->getLayout()->getBlock('kxpay_checkout_paymentform')->setKXURL($urlenv);
        $resultPage->getLayout()->getBlock('kxpay_checkout_paymentform')->setKXToken($token);
        
		return $resultPage;
	}
}