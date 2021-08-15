<?php

namespace app\common\components\currencyAPI;


use Yii;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;

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
//        $currencies = APIClient::performRequest(self::SOURCE_URL);
//        $redis = Yii::$app->cache->redis;
////        $res  = $redis->executeCommand('set', ["keyName", $currencies, 'EX', 60]);
//        $res  = $redis->executeCommand('get', ["keyName"]);
//        var_dump($res);
    }
}