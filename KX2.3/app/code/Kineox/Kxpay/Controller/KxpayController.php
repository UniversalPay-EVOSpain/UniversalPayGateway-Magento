<?php
namespace Kineox\Kxpay\Controller;

use Kineox\Kxpay\Model\KxpayModel;
use Magento\Store\Model\StoreManagerInterface;
use Kineox\Kxpay\Helper\Lib;

class KxpayController extends \Magento\Framework\App\Action\Action
{
    protected $_baseURL;
    protected $_environment;
    protected $_merchant_identifier;
    protected $_secure_key;
    protected $_p1c;
    protected $_p1c_text;
    protected $_p1c_link;
    protected $_style_back_boton;
    protected $_style_color_boton;
    protected $_style_back_frame;
    protected $_style_color_label;
    protected $_require_cardholder;
    protected $_active_log;
    protected $_order_status;
   

    public function __construct(KxpayModel $model, StoreManagerInterface $storeManager) {
    	$this->_baseURL = $storeManager->getStore()->getBaseUrl();
    	
    	$this->_environment = $model->getConfigData('environment');
    	$this->_merchant_identifier = $model->getConfigData('merchant_identifier');
    	$this->_secure_key = $model->getConfigData('secure_key');
    	$this->_p1c = $model->getConfigData('p1c');
    	$this->_p1c_text = $model->getConfigData('p1c_text');
    	$this->_p1c_link = $model->getConfigData('p1c_link');
    	$this->_style_back_boton = $model->getConfigData('style_back_boton');
    	$this->_style_color_boton = $model->getConfigData('style_color_boton');
    	$this->_style_back_frame = $model->getConfigData('style_back_frame');
    	$this->_style_color_label = $model->getConfigData('style_color_label');
    	$this->_require_cardholder = $model->getConfigData('require_cardholder');
    	$this->_active_log = $model->getConfigData('active_log');
    	$this->_card = $model->getConfigData('card');
    	$this->_sofort = $model->getConfigData('sofort');
    	$this->_correos = $model->getConfigData('correos');
    	$this->_trustly = $model->getConfigData('trustly');
    	$this->_biocryptology = $model->getConfigData('biocryptology');
    	$this->_paypal = $model->getConfigData('paypal');
    	$this->_barzahlen = $model->getConfigData('barzahlen');
    	$this->_amazonpay = $model->getConfigData('amazonpay');
    	$this->_gpay = $model->getConfigData('gpay');
    	$this->_bizum = $model->getConfigData('bizum');
    }

	public function get_baseURL(){
		return $this->_baseURL;
	}
	public function get_environment(){
		return $this->_environment;
	}
	public function get_merchant_identifier(){
		return $this->_merchant_identifier;
	}
	public function get_secure_key(){
		return $this->_secure_key;
	}
	public function get_p1c(){
		return $this->_p1c;
	}
	public function get_p1c_text(){
		return $this->_p1c_text;
	}
	public function get_p1c_link(){
		return $this->_p1c_link;
	}
	public function get_style_back_boton(){
		return $this->_style_back_boton;
	}
	public function get_style_color_boton(){
		return $this->_style_color_boton;
	}
	public function get_style_back_frame(){
		return $this->_style_back_frame;
	}
	public function get_style_color_label(){
		return $this->_style_color_label;
	}
	public function get_require_cardholder(){
		return $this->_require_cardholder;
	}
	public function get_active_log(){
		return $this->_active_log;
	}
	public function get_order_status(){
		return $this->_order_status;
	}
	public function get_card(){
		return $this->_card;
	}
	public function get_sofort(){
		return $this->_sofort;
	}
	public function get_correos(){
		return $this->_correos;
	}
	public function get_trustly(){
		return $this->_trustly;
	}
	public function get_biocryptology(){
		return $this->_biocryptology;
	}
	public function get_paypal(){
		return $this->_paypal;
	}
	public function get_barzahlen(){
		return $this->_barzahlen;
	}
	public function get_amazonpay(){
		return $this->_amazonpay;
	}
	public function get_gpay(){
		return $this->_gpay;
	}
	public function get_bizum(){
		return $this->_bizum;
	}

	public function get_storeLanguage(){
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		$resolver = $om->get('Magento\Framework\Locale\Resolver');
		return $resolver->getLocale();
	}


	public function execute(){
		die();
	}
}