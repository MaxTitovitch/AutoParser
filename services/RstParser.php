<?php

namespace Parsing;

use entity\Auto;
use Sunra\PhpSimple\HtmlDomParser;

use Sunra\PhpSimpl;

class RstParser {
    private $url;
    private $lastAuto;
    private $quantity;
    private $exitFlag = false;
    private $uniqueArray = [];
    private $errorCount = 0;

    private $autos = [];

    private $mounts = ['января', 'февраля', 'марта', 'мая', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];

    public function __construct($url, $lastAuto, $quantity) {
        $this->url = $url . "&start=";
        $this->lastAuto = $lastAuto;
        $this->quantity = $quantity;
    }

    public function getAutos(){
        $id = 1;
        while (!$this->exitFlag){
            $this->getAutosPage($this->url . $id);
            $id++;
        }
        return $this->autos;
    }

    private function getAutosPage($url){
        $document = HtmlDomParser::str_get_html(Parser::parse($url));
        $rows = $document->find('.rst-ocb-i');
        for ($i = 0; $i < count($rows); $i++) {
            $pageLink = 'http://rst.ua' . $rows[$i]->find('.rst-ocb-i-a')[0]->attr['href'];
            if(mb_strlen($pageLink) < 30) {continue;}
            if(!strcasecmp($pageLink, $this->lastAuto) OR $this->quantity-- <= 0) {
                $this->exitFlag = true;
                break;
            }
            $auto = $this->getData($pageLink, $url);
            if(in_array($auto->carMake . $auto->car . $auto->yearOfIssue, $this->uniqueArray)){
                $this->quantity++;
                $this->errorCount++;
                if($this->errorCount > 10) {
                    $this->exitFlag = true;
                    break;
                }
                else continue;
            } else {
                $this->autos[] = $auto; 
                $this->uniqueArray[] = $auto->carMake . $auto->car . $auto->yearOfIssue ;
            }
        }
    }

    private function getData($url, $lastUrl){
        $document = HtmlDomParser::str_get_html(Parser::parse($url, $lastUrl));
        $document->find("#rst-page-oldcars-item-header")[0]->children(0)->outertext = '';
        $markAndModel = explode(' ', $this->convert($document->find("#rst-page-oldcars-item-header")[0]->innertext));

        return new Auto(
            $document->find("#rst-page-oldcars-mainphoto")[0]->attr['src'],
            $this->findInTable('Дата добавления', $document),
            'Не указано',

            $markAndModel[1],
            trim(str_replace([',', 'обмен'], '', implode(' ', array_splice($markAndModel, 2)))),
            trim(str_replace("'", ' ',$this->convert($document->find(".rst-uix-price-param strong")[0]->innertext))),
            
            $this->changeType($this->findInTable('Тип кузова', $document)),
            $this->findInTable('Двигатель', $document) . ' л, КПП: ' . $this->changeKpp($this->findInTable('КПП', $document)),
            $this->findInTable('Год выпуска', $document),
            
            $this->changeMilage($this->findInTable('Год выпуска', $document, 1)),
            $this->changeColor($this->findInTable('Тип кузова', $document, 1)),
            true, 

            $this->changeCity($this->findInTable('Область', $document, 0), $this->findInTable('Область', $document, 1)),
            $url 
        );
    }

    private function convert($string) {
        return iconv('windows-1251', 'utf-8', $string);
    }

    private function changeType($string) {
        $arrayReplaced = ['(', 'дверей', 'двери', ')', '/Родстер', '2', '3', '4', '5', 'Внедорожник-', '/'];
        $string = trim(str_replace($arrayReplaced, '', $string));
        $string = str_replace('Внедорожник/Кроссовер', 'Внедорожник Кроссовер', $string);
        $string = $string =="Внедорожник" ? str_replace('Внедорожник', 'Внедорожник Кроссовер', $string) : $string;
        return $string;
    }  

    private function changeKpp($string) {
        $string = str_replace('Автомат', 'Автоматическая', $string);
        $string = str_replace('Механика', 'Механическая', $string);
        return $string;
    }

    private function changeMilage($string) {
        $string = str_replace([')', '(', '-', 'пробег'], '', $string);
        return trim($string);
    }

    private function changeColor($string) {
        $string = str_replace([')', '(', '-', 'цвет'], '', $string);
        return Parser::getColor(trim($string));
    }

    private function changeCity($region, $city) {
        $city = str_replace([')', '(', '-', 'город '], '', $city);
        $region = $this->changeRegion($region);
        return $region . ' область, ' . $city;
    }

    function changeRegion($region)    {
        $arrayForBig = ['ль', 'ов', 'ир', 'ев', 'ым'];
        if(mb_substr($region, mb_strlen($region)-2)== 'ца'){
            return mb_substr($region, 0, mb_strlen($region)-1) . 'кая';
        }
        if(mb_substr($region, mb_strlen($region)-2)== 'ва'){
            return mb_substr($region, 0, mb_strlen($region)-1) . 'ская';
        }
        if(mb_substr($region, mb_strlen($region)-2)== 'сы' || mb_substr($region, mb_strlen($region)-2)== 'са'){
            return mb_substr($region, 0, mb_strlen($region)-1) . 'кая';
        }
        if(mb_substr($region, mb_strlen($region)-2)== 'ье'){
            return mb_substr($region, 0, mb_strlen($region)-2) . 'ская';
        }
        if(in_array(mb_substr($region, mb_strlen($region)-2), $arrayForBig)){
           return $region . 'ская';
        }
        return $region . 'ая';
    }

    private function findInTable($key, $document, $id = 0, $default = 'Не указано') {
        $value = '';
        $elementsTh = $document->find('.rst-uix-list-superline, .rst-uix-table-superline')[0]->find('.rst-uix-float-left, .rst-uix-align-left');
        for ($i=0; $i < count($elementsTh); $i++) { 
            if(trim($this->convert($elementsTh[$i]->plaintext)) == $key){
                return $this->convert(trim($elementsTh[$i]->next_sibling()->children($id)->plaintext));
            }
        }
        return $default;
    }

    private function checkColor($style) {
        $replaces = ['background-color:', ' ', ';', 'box-shadow:inset0001px#e0e3e4'];
        $color = str_replace($replaces, '', $style);                       
        return $color == '' ? 'Не указано' : $color;
    }

    private function correctVolume($volume) {
        return explode('•', trim($volume))[0];
        
    }

    private function correctKpp($type) {
        $type = str_replace('Ручная / Механика', 'Механическая', $type);
        return str_replace('Автомат', 'Автоматическая', $type);
    }
}