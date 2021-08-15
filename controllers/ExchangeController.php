<?php

namespace app\controllers;


use app\common\components\currencyAPI\Handler;
use Yii;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;
use yii\web\Controller;

class ExchangeController extends Controller
{

    /**
     * get API data.
     *
     * @return string
     */
    public function actionIndex()
    {
        $result = Handler::getCurrencies();
        var_dump($result);
        return "hello";
    }

}