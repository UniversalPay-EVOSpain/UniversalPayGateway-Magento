<?php
class Kineox_Kxpay_Block_Form extends Mage_Payment_Block_Form {
	protected function _construct() {
		parent::_construct ();
		$this->setTemplate ( 'payment/form/kxpaypay.phtml' );
	}
}