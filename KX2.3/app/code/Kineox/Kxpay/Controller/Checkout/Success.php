<?php

namespace Kineox\Kxpay\Controller\Checkout;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Checkout\Model\Session;
use Magento\Store\Model\StoreManagerInterface;
use Kineox\Kxpay\Controller\KxpayController;

use Magento\Framework\Filesystem\DirectoryList;


class Success extends \Magento\Framework\App\Action\Action {
	protected $_session;
	protected $_resultPageFactory;
	protected $_storeManager;
	protected $_kxpayController;
	protected $_dir;

	public function __construct(Context $context, PageFactory $resultPageFactory, Session $session, StoreManagerInterface $storeManager, DirectoryList $dir, KxpayController $kxpayController) {
		$this->_session = $session;
		$this->_resultPageFactory = $resultPageFactory;
		$this->_storeManager = $storeManager;
		$this->_kxpayController = $kxpayController;
		$this->_dir = $dir;
		return parent::__construct($context);
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
		$resultPageFactory = $this->get_resultPageFactory();

		$resultPage = $resultPageFactory->create();
		$resultPage->getConfig()->getTitle()->prepend(__("Gracias por su compra"));
		$resultPage->getLayout()->initMessages();
		$resultPage->getLayout()->getBlock("kxpay_checkout_success")->setMessage("Gracias por confiar en nosotros. El pago del pedido ha sido tramitado con éxito.");
		return $resultPage;
	}
}