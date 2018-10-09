<?php
namespace Rudracomputech\Nestpay\Controller\Payment;




class Redirect extends \Magento\Framework\App\Action\Action {
	
  
  
	protected $_checkoutSession;
	protected $_resultPageFactory;
	protected $layoutFactory;
	
	 /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;
	
	
	/**
     * Order object
     *
     * @var \Magento\Sales\Model\Order
     */
    protected $_order;
	
	 /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_layout;
    protected $_logger;
	private $redirectFactory;
	protected $messageManager;
	
    protected $liveUrl = 'https://epay.halkbank.mk/fim/est3Dgate';
    protected $testUrl = 'https://entegrasyon.asseco-see.com.tr/fim/est3Dgate';
    /**
     * Constructor
     * 
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
	 
	 protected $resultRawFactory;
	 
	 
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
		 \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\Controller\Result\RedirectFactory $redirectFactory,
		 \Magento\Framework\View\LayoutFactory $layoutFactory,
		\Magento\Framework\Message\ManagerInterface $messageManager,
        \Psr\Log\LoggerInterface $logger,
		 \Magento\Framework\View\LayoutInterface $layout
    )
    {
		
$this->_resultPageFactory = $resultPageFactory;
    	 $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
		  $this->redirectFactory = $redirectFactory;
		  $this->messageManager = $messageManager;
		    $this->layoutFactory = $layoutFactory;
	   $this->_layout = $layout;
	    $this->resultRawFactory = $resultRawFactory; 
		
		
        $this->_logger = $logger;
        parent::__construct($context);
    }
	
	
   protected function _getOrder($incrementId= null)
    {
        if (!$this->_order) {
            $incrementId = $incrementId ? $incrementId : $this->_getCheckout()->getLastRealOrderId();
            $this->_order = $this->_orderFactory->create()->loadByIncrementId($incrementId);
        }
        return $this->_order;
    }
	
	/**
     * Get frontend checkout session object
     *
     * @return \Magento\Checkout\Model\Session
     */
    protected function _getCheckout()
    {
        return $this->_checkoutSession;
    }

    /**
     * Execute view action
     * 
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
		
		
		$layout  = $this->layoutFactory->create();
		
	
		
	            
			if (!$this->_getOrder()->getId()) {
                $this->messageManager->addError( __("No order for processing found."));

			

			return $this->redirectFactory->create()->setPath('checkout/cart/index');
            }
			
			
			
			$order = $this->_getOrder();
			
		#$payment = $info->getPayment();
       # $order = $payment->getOrder();
		#$payment = $order->getPayment();
		
      
		
		
		$configHelper = $this->_objectManager->get('Rudracomputech\Nestpay\Helper\Data');
		
		
		
		$clientid = str_replace("|", "\\|", str_replace("\\", "\\\\",  $configHelper->getConfig('payment/nestpay/clientid')));
        $oid = str_replace("|", "\\|", str_replace("\\", "\\\\", $order->getIncrementId()));
        $amountp = str_replace("|", "\\|", str_replace("\\", "\\\\", $order->getGrandTotal()));
        $amount = round($amountp,2);
        $okurl = str_replace("|", "\\|", str_replace("\\", "\\\\",  $configHelper->getConfig('payment/nestpay/response')));
        $failurl = str_replace("|", "\\|", str_replace("\\", "\\\\",  $configHelper->getConfig('payment/nestpay/response')));
        $trantype = str_replace("|", "\\|", str_replace("\\", "\\\\",  $configHelper->getConfig('payment/nestpay/trantype')));
        $instalment = '';

        $rnd = microtime();

        $currency = str_replace("|", "\\|", str_replace("\\", "\\\\",  $configHelper->getConfig('payment/nestpay/currency')));
        $storekey = str_replace("|", "\\|", str_replace("\\", "\\\\",  $configHelper->getConfig('payment/nestpay/storekey')));
        $lang = "en";
        $storetype = "3D_PAY_HOSTING";

        $plaintext = $clientid .  $oid . $amount . $okurl . $failurl . $trantype . $instalment . $rnd . $storekey;

        $hashValue = hash('sha1', $plaintext);
        $hash = base64_encode(pack('H*', $hashValue));
		
		
		if ($configHelper->getConfig('payment/nestpay/test_mode')) {
            $gatewayurl =  $this->testUrl;
        } else {
            $gatewayurl =  $this->liveUrl;
        }
		
		#var_dump($order->getBillingAddress()->getData());die("here");
		
		$arrayData = array(
            'clientid'  => $clientid,
            'storetype' => $storetype,
            'hash'      => $hash,
            'trantype'  => $trantype,
            'ammount'   => $amount,
            'currency'  => $currency,
            'oid'       => $oid,
            'okUrl'     => $okurl,
            'failUrl'   => $failurl,
            'lang'      => $lang,
            'encoding'  => 'utf-8',
            'rnd'       => $rnd,
            'gatewayurl'       => $gatewayurl,
            'sendCustomerInfo'       => $configHelper->getConfig('payment/nestpay/customer_info'),
            'billingAddress_firstname'       => $order->getBillingAddress()->getData('firstname'),
            'billingAddress_lastname'       => $order->getBillingAddress()->getData('lastname'),
            'billingAddress_Street1'       =>  $order->getBillingAddress()->getStreet1(),
            'billingAddress_Street2'       =>$order->getBillingAddress()->getStreet2(),
            'billingAddress_city'       => $order->getBillingAddress()->getData('city'),
            'billingAddress_region'       => $order->getBillingAddress()->getData('region'),
            'billingAddress_postcode'       =>$order->getBillingAddress()->getData('postcode') ,
            'billingAddress_country_id'       =>$order->getBillingAddress()->getData('country_id') ,
            'billingAddress_email'       => $order->getBillingAddress()->getData('email'),
            'billingAddress_phone'       => $order->getBillingAddress()->getData('phone'),
            'shippingAddress_company'       =>$order->getShippingAddress()->getData('company') ,
            'shippingAddress_firstname'       => $order->getShippingAddress()->getData('firstname'),
            'shippingAddress_lastname'       => $order->getShippingAddress()->getData('lastname'),
            'shippingAddress_Street1'       => $order->getShippingAddress()->getStreet1() ,
            'shippingAddress_Street2'       => $order->getShippingAddress()->getStreet2() ,
            'shippingAddress_city'       => $order->getShippingAddress()->getData('city'),
            'shippingAddress_region'       => $order->getShippingAddress()->getData('region'),
            'shippingAddress_postcode'       => $order->getShippingAddress()->getData('postcode'),
            'shippingAddress_country_id'       => $order->getShippingAddress()->getData('country_id') 
        );
		
		$block = $layout->createBlock("\Magento\Framework\View\Element\Template","nestpay",['data' => [$arrayData]])->setData('area', 'frontend')->setTemplate('Rudracomputech_Nestpay::payment/form/redirect.phtml')->toHtml();
		$resultRaw = $this->resultRawFactory->create();
		$resultRaw->setContents($block);
		return $resultRaw;

		
       
    }
	
	
	
	
}
