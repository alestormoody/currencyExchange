<?php
    /**
     *  Convert Class to return the Exchange Rate
     *  
     */
    class Convert {

        private $currencies = [
            'USD' => [
                'CZK',
            ],
            'EUR' => [
                'CZK',
            ],
            'GBP' => [
                'CZK',
            ],
            // 'YEN' => [
            //     'USD',
            //     'EUR',
            //     'GBP',
            // ],

        ];
        public $object;

        /**
         *  construct that generate the object with the currencies exchange we need
         */

        function __construct()
        {
            foreach($this->currencies as $from => $currency) {
                foreach($currency as $to) {
                    $this->object = (object) array_merge( (array)$this->object, array( $from.'-'.$to => SELF::currencyAPI($to, $from)) );
                }
            }
        }

        /**
         * Print Method
         * @return $this->object    contain the conversions
         */
        public function getRate() {
            return json_encode($this->object);
        }

        /**
         * CALL TO CURRENCY EXCHANGE API 
         *
         * @param string $to        currency want to buy
         * @param string $from      currency used to buy
         * 
         * @return float $params    exchange rate in $to currency
         * 
         * !! ATTENTION : please note that the actual API not provide unlimited query in the free version
         * The code check if the first API is still working, if it's not, use a second one to provide the
         * currency exchange. Even the second API, in the free version, should have some limit.
        */
        private function currencyAPI($to = 'CZK', $from = 'ERR') {

            if($from == 'ERR') { return 'Please provide a currency to convert from'; }

            $result = SELF::apilayer($from, $to);

            // if the first API exceeded the convertion limit
            if(!is_float($result)) { 
                $result = SELF::exchangerate($from, $to);
            }

            return number_format((float)$result, 4, '.', '');

        }

        private function apilayer($from, $to) {

            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.apilayer.com/exchangerates_data/convert?to='.$to.'&from='.$from.'&amount=1',
            CURLOPT_HTTPHEADER => array(
                "Content-Type: text/plain",
                "apikey: 5qTeOEtGi0XRH77JCqTQVs7iRnpOyLZ3"
            ),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET"
            ));
            
            $result = curl_exec($curl);
            curl_close($curl);

            $return = get_object_vars(json_decode($result));

            return $return['result'] ?? 0;

        }

        private function exchangerate($from, $to) {
            $req_url = 'https://api.exchangerate.host/convert?from='.$from.'&to='.$to;
            $response_json = file_get_contents($req_url);
            if(false !== $response_json) {
                try {
                    $response = get_object_vars(json_decode($response_json));
                    if($response['success'] === true) {
                        return $response['info']->rate;
                    }
                } catch(Exception $e) {
                    return $e;
                }
            }
        }

    }

    $convert = new Convert();
    $return = $convert->getRate();
    echo $return;
