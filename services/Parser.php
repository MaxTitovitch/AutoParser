<?php

namespace Parsing;

require  __DIR__ . "/OlxParser.php";
require  __DIR__ . "/AutoRiaParser.php";
require  __DIR__ . "/RstParser.php";

class Parser {

    private static $colors = [
        'Белый' => '#ffffff',
        'Черный' => '#000000',
        'Синий' => '#0000ff',
        'Серый' => '#808080',
        'Серебристый' => '#c5c9c7',
        'Красный' => '#ff0000',
        'Зеленый' => '#00ff00',
        'Апельсин' => '#ffa500',
        'Асфальт' => '#606060',
        'Бежевый' => '#f5f5dc',
        'Бирюзовый' => '#30d5c8',
        'Бронзовый' => '#cd7f32',
        'Вишнёвый' => '#911e42',
        'Голубой' => '#42aaff',
        'Желтый' => '#ffff00',
        'Золотой' => '#ffd700',
        'Коричневый' => '#964b00',
        'Магнолии' => '#f8f4ff',
        'Матовый' => '#808080',
        'Оливковый' => '#808000',
        'Розовый' => '#ffc0cb',
        'Сафари' => '#e9d1af',
        'Фиолетовый' => '#8b00ff',
        'Хамелеон' => '#cd00cd',
    ];

    private $parsedURLs = [
        'https://www.olx.ua/transport/legkovye-avtomobili/',
        "https://auto.ria.com/search/?categories.main.id=1&price.currency=1&sort[0].order=dates.created.desc&abroad.not=0&custom.not=1&size=20&page=",
        "http://rst.ua/oldcars/?task=newresults&make%5B%5D=0&year%5B%5D=0&year%5B%5D=0&price%5B%5D=0&price%5B%5D=0&engine%5B%5D=0&engine%5B%5D=0&gear=0&fuel=0&drive=0&condition=0&from=sform&body%5B%5D=10&body%5B%5D=6&body%5B%5D=1&body%5B%5D=3&body%5B%5D=2&body%5B%5D=5&body%5B%5D=11&body%5B%5D=4&body%5B%5D=27&start=",

    ];

    private $autoQuantity;

    public function __construct($autoQuantity = 100, $parsedURLs) {
        $this->autoQuantity = $autoQuantity;
        $this->parsedURLs = $parsedURLs;
        // echo count($parsedURLs);
    }


    public function getAutos($lastOlxAutoName = null, $lastAutoRiaAutoName = null, $lastRstAutoName = null){
        $parsedAutos = [];
        for ($i=0; $i < count($this->parsedURLs); $i++) { 
            if(stripos($this->parsedURLs[$i], 'olx') !== false){
                $parsedAutos = array_merge($parsedAutos, $this->getAutoFromOlx($this->parsedURLs[$i], count($this->parsedURLs)));
            }
            if(stripos($this->parsedURLs[$i], 'ria') !== false){
                $parsedAutos = array_merge($parsedAutos, $this->getAutoFromAutoRia($this->parsedURLs[$i], count($this->parsedURLs)));
            }
            if(stripos($this->parsedURLs[$i], 'rst') !== false){
                $parsedAutos = array_merge($parsedAutos, $this->getAutoFromRst($this->parsedURLs[$i], count($this->parsedURLs)));
            }
        }
        return $parsedAutos;
    }

    private function getAutoFromOlx($lastAutoName, $quantityLink) {
        $quantity =  $this->autoQuantity - ((int)($this->autoQuantity / $quantityLink) * ($quantityLink-1));
        // echo $quantity;
        $parser = new OlxParser($lastAutoName, $lastAutoName, $quantity);
        return $parser->getAutos();
    }

    private function getAutoFromAutoRia($lastAutoName, $quantityLink) {
        $quantity =  (int)($this->autoQuantity / $quantityLink);
        $parser = new AutoRiaParser($lastAutoName, $lastAutoName, $quantity);
        return $parser->getAutos();
    }

    private function getAutoFromRst($lastAutoName, $quantityLink) {
        $quantity =  (int)($this->autoQuantity / $quantityLink);
        $parser = new RstParser($lastAutoName, $lastAutoName, $quantity);
        return $parser->getAutos();
    }

    public static function printWithPre($object) {
        echo '<pre>';
        print_r($object);
        echo '</pre>';
    }

    public static function parse($url, $lastUrl=false){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if($lastUrl) curl_setopt($ch, CURLOPT_REFERER, $lastUrl);

        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public static function mb_ucfirst_llast($string) {
        $string = trim($string);
        return mb_strtoupper(mb_substr($string, 0, 1)) . mb_strtolower(mb_substr($string, 1));
    }

    public static function getColor($key) {
        if(array_key_exists($key, self::$colors)){
            return self::$colors[$key];
        }
        return 'Не указано';
    }
}
