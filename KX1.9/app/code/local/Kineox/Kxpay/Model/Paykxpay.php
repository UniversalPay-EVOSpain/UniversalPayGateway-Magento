<?php

class Kineox_Kxpay_Model_Paykxpay extends Mage_Payment_Model_Method_Abstract
{

    protected $_code                    = 'kxpay';
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = true;
    protected $_canRefund               = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc               = false;
    protected $_formBlockType           = 'kxpay/form';
 
	public function getOrderPlaceRedirectUrl() {
		return Mage::getUrl('kxpay/payment/form', array('_secure' => true));
	}
 
	public function getTitle(){
		return __(parent::getTitle());
	}

}
?>
