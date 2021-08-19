<?php

namespace app\common\components\currencyAPI;

use app\components\Helper;
use app\components\parser\NewsPostItem;
use DOMDocument;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use PhpQuery\PhpQuery;
use Symfony\Component\DomCrawler\Crawler;
use function PhpQuery\pq;

class TestHandler extends \yii\base\Component
{
//    const SOURCE_URL = 'https://rusplt.ru';
    const SOURCE_URL = 'https://rusplt.ru/policy/';

    /**
     * NewsPostItem constructor
     *
     * @param int $type PostItemType
     * @param string|null $text text item
     * @param string|null $image url to image
     * @param string|null $link url external link
     * @param string|null $headerLevel header level for type HEADER
     * @param string|null $youtubeId video youtube id
     */

    /**
     * NewsPost constructor
     *
     * @param string $parser parser Classname
     * @param string $title news title
     * @param string $description news description
     * @param string $createDate news create date in 'Y-m-d H:i:s' format UTC+0
     * @param string $original url to original news
     * @param string|null $image url to news image
     *
     * @throws \Exception
     */

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

    static function getCategories() {
        $categories = self::getNodeContent(self::SOURCE_URL, '.category');
        $links = [];
        foreach ($categories as $category) {
            $link = $category->attributes[1]->nodeValue;
            $link ? array_push($links, $category->attributes[1]->nodeValue) : '';
        }
        var_dump($links);
        return true;
    }

    static function getNewsCards() {
        $cards = self::getNodeContent(self::SOURCE_URL, '.b-news-card');
        foreach ($cards as $card) {
            foreach ($card->childNodes as $node) {
                if ($node->tagName === "img") {
                    var_dump($node->attributes[1]->nodeValue);
                }

            }
//            var_dump($card->childNodes[0]->attributes[1]->value);
//
//            var_dump($card->childNodes[1]);
//
//            var_dump($card->childNodes[1]->nodeValue);
//
//            var_dump($card->childNodes[2]->nodeValue);
        }

        return true;
    }

    static function parseCard() {
        $cards = self::getNodeContent(self::SOURCE_URL, '.category');
        $content = [];
        foreach ($cards as $category) {
            $link = $category->attributes[1]->nodeValue;
            $link ? array_push($links, $category->attributes[1]->nodeValue) : '';
        }
        var_dump($links);
        return true;
    }

    static function getNodeContent($url, $selector) {
        $html = APIClient::performRequest($url);
        $pq = new PhpQuery;
        $pq->load_str($html);
        return $url && $selector ? $pq->query($selector) : [];
    }
}