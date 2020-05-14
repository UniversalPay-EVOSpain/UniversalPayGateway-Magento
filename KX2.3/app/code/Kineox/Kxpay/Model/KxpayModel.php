<?php
namespace Kineox\Kxpay\Model;

class KxpayModel extends \Magento\Payment\Model\Method\AbstractMethod
{
    protected $_code = 'kxpay';

    protected $_isOffline = false;
    
    protected $_isGateway = true;
    
    public function getConfigData($field, $storeId=null){
    	return parent::getConfigData($field, $storeId);
    }

    function getTitle(){
    	return __(parent::getTitle());
    }
}
