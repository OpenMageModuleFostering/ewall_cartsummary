<?php

class Ewall_Cartsummary_Block_Summary extends Mage_Core_Block_Template
{
	public function getQuote()
	{
		$grandTotal=Mage::getSingleton('checkout/cart')->getQuote(); 
		return $grandTotal;
	} 

	public function getShippingArray(){
		if($customer = Mage::getSingleton('customer/session')->isLoggedIn()) {
			$val = $this->getQuote();
		} else {
			$val = Mage::getSingleton('checkout/session')->getQuote();
		}
		return $val;
	}
}
