<?php

class Bitcoin_IndexController extends Zend_Controller_Action
{
	public function indexAction()
	{
		$request = $this->getRequest();
		$currency = $request->getParam('currency');
		if(empty($currency)) {
			$currency = 'sgd';
		}
		$modelBitcoin = new Bitcoin_Model_Bitcoin();
		$respondBitcoin = $modelBitcoin->respondBitcoin($currency);
		$priceBtc = $modelBitcoin->getBtcPrice($respondBitcoin,$currency);
		echo $priceBtc;
	}
}