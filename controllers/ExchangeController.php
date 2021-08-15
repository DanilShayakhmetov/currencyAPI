<?php

namespace app\controllers;


use app\common\components\currencyAPI\Handler;
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
        return Handler::prepareResultString();
    }

}