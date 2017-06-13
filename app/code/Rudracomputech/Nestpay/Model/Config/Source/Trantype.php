<?php

namespace Rudracomputech\Nestpay\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Trantype implements ArrayInterface {
	public function toOptionArray() {
		return array(
			array('value' => 'Auth', 'label' => 'Auth'),
			array('value' => 'PreAuth', 'label' => 'PreAuth'),
		);
	}
}