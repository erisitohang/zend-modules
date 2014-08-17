<?php
class Bitcoin_Model_Bitcoin
{
    /**
     * @param $currency
     * @return mixed
     */
    public function respondBitcoin($currency) {
		$cache = $this->cacheResult($currency);
		if(!$response = $cache->load($currency.'Response')) {
			$client = new Zend_Http_Client();
			$client->setUri("http://preev.com/pulse/units:btc+$currency/sources:bitfinex+bitstamp+btce+localbitcoins");
			$client->setConfig(array('maxredirects'=>0, 'timeout'=>30));
			try{
				$response = $client->request();
				$cache->save($response,$currency.'Response');
				$getResponse = $response->getBody();
			}catch(Exception $e){
				$getResponse = array();
			}
		}
		$json = json_decode($getResponse,true);
		return $json;
	}

    /**
     * @param $currency
     * @return mixed
     */
    public function cacheResult($currency){
		$frontEndOption = array('lifetime'=>100, 'automatic_serialization'=>true);
		$backendOptions = array('cache_dir'=>'../application/tmp');
		$cache = Zend_Cache::factory('Core', 'File', $frontEndOption, $backendOptions);
		return $cache;
	}

    /**
     * @param $respondBitcoin
     * @param $currency
     * @return float
     */
    public function getBtcPrice($respondBitcoin, $currency){
		$btcPrice = 0;
		if(isset($respondBitcoin['btc']['usd'])){
			$btcUsd = $respondBitcoin['btc']['usd'];
			$totalVolume = 0;
			foreach($btcUsd as $b){
				$totalVolume += $b['volume'];
			}
			if($totalVolume > 0){
				foreach($btcUsd as $b){
					$btcPrice += $b['last'] * $b['volume'] / $totalVolume;
				}
			}
		}
		if(isset($respondBitcoin[$currency]['usd']['other']['last'])) {
			$btcPrice = $btcPrice / $respondBitcoin[$currency]['usd']['other']['last'];
		}
		return round($btcPrice,1);
	}
}