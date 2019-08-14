<?php
class Ewall_Cartsummary_IndexController extends Mage_Core_Controller_Front_Action{
    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }
    public function indexAction() {		
		try {
            $cartData = $this->getRequest()->getParam('cart');
            if (is_array($cartData)) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                foreach ($cartData as $index => $data) {
                    if (isset($data['qty'])) {
                        $cartData[$index]['qty'] = $filter->filter(trim($data['qty']));
                    }
                }
                $cart = Mage::getSingleton('checkout/cart');
                if (! $cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
                    $cart->getQuote()->setCustomerId(null);
                }

                $cartData = $cart->suggestItemsQty($cartData);
                $cart->updateItems($cartData)
                    ->save();
            }
            $this->_getSession()->setCartWasUpdated(true);
            $message = $this->__('Products were updated on your shopping cart');
			$this->_getSession()->addSuccess($message);
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError(Mage::helper('core')->escapeHtml($e->getMessage()));
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot update shopping cart'));
            Mage::logException($e);
        }
		$this->_redirect('checkout/onepage');
    }
    public function deleteAction()
    {
		$id = (int) Mage::app()->getRequest()->getParam('id');       
        if ($id) {
            try {
				$cart = Mage::getSingleton('checkout/cart');
				$message = $this->__('Items deleted on your shopping cart');
				$this->_getSession()->addSuccess($message);
                $cart->removeItem($id)->save();
            } catch (Exception $e) {
                $this->_getSession()->addError($this->__('Cannot remove the item'));
                Mage::logException($e);
            }
        }
       $this->_redirect('checkout/onepage');
    }    
}
