<?php

include_once("config.php");

final class CurrencyConverter {

    private static $quotes = [];

    private static function loadExchangeRates($from, $to) {
        $params['access_key'] = $_ENV["CURRENCYLAYER_API_ACCESS_KEY"];
        $params['currencies'] = $to;
        $params['source'] = $from;
        $params['format'] = 1;
        $url = 'http://apilayer.net/api/live?'.http_build_query($params);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($ch);
        curl_close($ch);
        $rsp = json_decode($json, true);

        if (array_key_exists('error', $rsp)) {
            $error = $rsp['error'];
            throw new \InvalidArgumentException($error['info'], $error['code']);
        }

        $quotes = $rsp['quotes'];
        CurrencyConverter::$quotes[$from . $to] = $quotes[$from . $to];
    }

    public static function convert($from, $to, $value) {
        if (!array_key_exists($from . $to, CurrencyConverter::$quotes)) {
            CurrencyConverter::loadExchangeRates($from, $to);
        }
        $quote = CurrencyConverter::$quotes[$from . $to];
        return round($value / $quote, 2);
    }
}

// echo "1usd = " . CurrencyConverter::convert('USD', 'GBP', 1) . "gbp" . PHP_EOL;
// echo "50usd = " . CurrencyConverter::convert('USD', 'GBP', 50) . "gbp" . PHP_EOL;
// echo "0usd = " . CurrencyConverter::convert('USD', 'GBP', 0) . "gbp" . PHP_EOL;
// echo "17.99usd = " . CurrencyConverter::convert('USD', 'GBP', 17.99) . "gbp" . PHP_EOL;

// echo "1eur = " . CurrencyConverter::convert('EUR', 'GBP', 1) . "gbp" . PHP_EOL;
// echo "50eur = " . CurrencyConverter::convert('EUR', 'GBP', 50) . "gbp" . PHP_EOL;
// echo "0eur = " . CurrencyConverter::convert('EUR', 'GBP', 0) . "gbp" . PHP_EOL;
// echo "17.99eur = " . CurrencyConverter::convert('EUR', 'GBP', 17.99) . "gbp" . PHP_EOL;

?>