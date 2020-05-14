<?php

namespace Kineox\Kxpay\Controller\Checkout;

use Kineox\Kxpay\Controller\KxpayController;
use Kineox\Kxpay\Helper\Lib;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Checkout\Model\Session;
use Magento\Store\Model\StoreManagerInterface; 
use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Model\Cart;

class Error extends  \Magento\Framework\App\Action\Action {
	protected $_session;
	protected $_resultPageFactory;
	protected $_storeManager;
	protected $_redsysController;
	protected $_productRepository;
	protected $_cart;

	public function __construct(
			Context $context, 
			PageFactory $resultPageFactory, 
			Session $session, 
			StoreManagerInterface $storeManager, 
			KxpayController $kxpayController, 
			ProductRepository $productRepository, 
			Cart $cart) {
		
		$this->_session = $session;
		$this->_resultPageFactory = $resultPageFactory;
		$this->_storeManager = $storeManager;
		$this->_redsysController = $redsysController;
		$this->_productRepository = $productRepository;
		$this->_cart = $cart;

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

	public function get_redsysController() {
		return $this->_redsysController;
	}

	public function get_productRepository() {
		return $this->_productRepository;
	}

	public function get_cart() {
		return $this->_cart;
	}

	public function execute() {
		$session = $this->get_session();
		$resultPageFactory = $this->get_resultPageFactory();
		$redsysController = $this->get_redsysController();
		
		$order = $session->getLastRealOrder();
		$cart = $this->get_cart();
		$formKey = $redsysController->get_formKey()->getFormKey();
		$productRepository = $this->get_productRepository();

		$saveCart = $redsysController->get_errorpago();

		if (!isset($_GET['form_key'])){
			$status = 0;
		}
		elseif ($saveCart) {
			$status = 1;
			Lib::RestoreCart($order, $cart, $formKey, $productRepository);
		}
		else{
			$status = 2;
		}

		$resultPage = $resultPageFactory->create();
		$resultPage->getConfig()->getTitle()->prepend("Error procesando el pago");
		$resultPage->getLayout()->initMessages();
		$resultPage->getLayout()->getBlock('kxpay_checkout_error')->setStatus($status);
		return $resultPage;
	}
}
