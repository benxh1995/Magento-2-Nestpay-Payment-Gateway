<?php
namespace Rudracomputech\Nestpay\Block\Payment;

use Magento\Framework\View\Element\Template;

class Form extends Template{
	
	 protected $_coreRegistry;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }
}
