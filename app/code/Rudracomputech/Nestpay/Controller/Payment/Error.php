<?php
namespace Rudracomputech\Nestpay\Controller\Payment;




class Error extends \Magento\Framework\App\Action\Action {
	
  
  
	protected $_checkoutSession;
	protected $_resultPageFactory;
	protected $layoutFactory;
	protected $rawResultFactory;
	
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
	  protected $_transportBuilder;
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
		 \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
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
			 $this->_transportBuilder= $transportBuilder;
			 $this->_storeManager = $storeManager;
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
		
		 $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('nestpay_payment_error');
        $this->_view->renderLayout();
		
    
    }
	
	
	
	
}
