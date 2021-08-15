<?php

namespace app\common\components\currencyAPI;


use Yii;

class Handler extends \yii\base\Component
{
    const SOURCE_URL = 'https://www.cbr-xml-daily.ru/daily_json.js';

    static function getCurrencies()
    {
        $redis = Yii::$app->cache->redis;
        $currencies  = $redis->executeCommand('get', ["currencies"]);
        if ($currencies) {
            return json_decode($currencies, true)["Valute"];
        } else {
            $currencies = APIClient::performRequest(self::SOURCE_URL);
            $redis->executeCommand('set', ["currencies", $currencies, 'EX', 360]);
            return json_decode($currencies, true)["Valute"];
        }
    }

    static function postfixQualifire($value)
    {
        if($value == 1) {
            return "рублю";
        } else {
            if (substr(strval($value), -3) === "000") {
                return "рублей";
            } else {
                return "рублям";
            }
        }

    }

    static function prepareResultString()
    {
        $apiKey = Yii::$app->request->headers->get('X-API-KEY');
        $currency = strtoupper(Yii::$app->request->get('cur'));
        if ($apiKey === '123321' && $currency) {
            $currencyData = array_key_exists($currency, self::getCurrencies())
                ? self::getCurrencies()[$currency] : "non-existent currency";
            $resultString = $currencyData["Nominal"]." ".$currencyData["Name"]
                ." равен ".$currencyData["Value"]." ".self::postfixQualifire($currencyData["Value"]);

            return json_encode([$currency => $resultString], JSON_UNESCAPED_UNICODE);
        } else {
            return json_encode(['ERROR' => "Access denied. Wrong API key!"], JSON_UNESCAPED_UNICODE);
        }
    }
}