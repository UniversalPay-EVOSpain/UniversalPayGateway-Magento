<?php
namespace Kineox\Kxpay\Controller\Checkout;

use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\DB\Transaction;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Store\Model\StoreManagerInterface;

use Kineox\Kxpay\Controller\KxpayController;
use Kineox\Kxpay\Helper\Lib;

class Notify extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;
    protected $_kxpayController;
    protected $_session;
    protected $_invoiceService;
    protected $_invoiceSender;
    protected $_cart;
    protected $_formKey;
    protected $_productRepository;

    public function __construct(Context $context, Session $session, PageFactory $resultPageFactory, StoreManagerInterface $storeManager, KxpayController $kxpayController, InvoiceService $invoiceService, InvoiceSender $invoiceSender, Cart $cart, ProductRepository $productRepository, FormKey $formKey)
    {
    	$this->_session = $session;
    	$this->_invoiceSender = $invoiceSender;
    	$this->_invoiceService = $invoiceService;
    	$this->_kxpayController = $kxpayController;
    	$this->_resultPageFactory = $resultPageFactory;
    	$this->_cart = $cart;
    	$this->_formKey = $formKey;
    	$this->_productRepository = $productRepository;
    	parent::__construct($context);
    }
    
    public function execute()
    {
    	$resultPage = $this->_resultPageFactory->create();
    	$resultPage->getConfig()->getTitle()->append(__("Notificación")." Kxpay");
    	$resultPage->getLayout()->initMessages();
    	
    	$idLog = Lib::generateIdLog();
        $active_log = $this->_kxpayController->get_active_log();
        
        
		$order_status = $this->_kxpayController->get_order_status();
		if($order_status==="")
			$order_status="Processing";

		Lib::writeLog($idLog.' Order validation started.', $active_log);
		
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
                $order = $objectManager->create('\Magento\Sales\Model\Order')->load($order_id);

				if ($signature == $received_signature) {  
                    Lib::writeLog($idLog.' Order creation started.', $active_log);
                    if(!$order->canInvoice()) {
                        $order->addStatusHistoryComment(__("Kxpay, imposible generar Factura."), false);
                        $order->save();
                    } else{
                        $transaction = new Transaction();
                        
                        $invoice=$this->_invoiceService->prepareInvoice($order);
                        $invoice->register();
                        $invoice->save();
                        $transactionSave = $transaction->addObject($invoice)->addObject($invoice->getOrder());
                        $transactionSave->save();
                        if(!@$this->_invoiceSender->send($invoice))
                            $order->addStatusHistoryComment(__("Kxpay, imposible enviar Factura."), false);
                        
                        $order->addStatusHistoryComment(__("Kxpay ha generado la Factura del pedido"), false)->save();		
                        
                        $order->sendNewOrderEmail();
                        
                        $order_status=$this->_kxpayController->get_order_status();
                        $order->setState('new')->setStatus($order_status)->save();
                        $order->addStatusHistoryComment(__("Pago con Kxpay registrado con éxito."), false)
                            ->setIsCustomerNotified(false)
                            ->save();
                    }
                } else {
                    Lib::writeLog($idLog.' Signatures don`t match: '.$received_signature.' - '.$signature, $active_log);
                    
                    if ($order->canCancel()) {
                        try {
                            $order->cancel();
                            $order->save();
                
                            $order->addStatusHistoryComment(__("Pedido cancelado por Kxpay."), false)
                            ->setIsCustomerNotified(false)
                            ->save();
                        } catch (Exception $e) {
                            Lib::writeLog($idLog,"Order ".$order_id.": Exception: $e",$active_log);
                        }
                    }
					die();
                }
            } else {
				Lib::writeLog($idLog.' ERROR: Payment Code '.$array->PAYMENT_CODE, $active_log);
				die();
			}
        }else {
			Lib::writeLog($idLog.' No data received', $active_log);
			die();
		}
    }
    
	
}