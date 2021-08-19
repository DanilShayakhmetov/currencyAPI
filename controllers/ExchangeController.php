<?php

namespace app\controllers;


use app\common\components\currencyAPI\Handler;
use app\common\components\currencyAPI\TestHandler;
use PHPUnit\Util\Test;
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
        return TestHandler::getNewsCards();
    }

}