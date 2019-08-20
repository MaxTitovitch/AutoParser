<?php

namespace Parsing;

use entity\Auto;
use Sunra\PhpSimple\HtmlDomParser;

use Sunra\PhpSimpl;

class AutoRiaParser {
    private $url;
    private $lastAuto;
    private $quantity;
    private $exitFlag = false;

    private $dateAddress = 'https://auto.ria.com/demo/bu/searchPage/v2/view/auto/2478/247821/XXXXX?lang_id=2';

    private $autos = [];

    public function __construct($url, $lastAuto, $quantity) {
        $this->url = str_replace(['&page=0', '&page=1'], '', str_replace(['page=0', 'page=1'], '', $url)) . '&page=';
        $this->lastAuto = $lastAuto;
        $this->quantity = $quantity;
    }

    public function getAutos(){
        $id = 0;
        while (!$this->exitFlag){
            $this->getAutosPage($this->url . $id);
            $id++;
        }
        return $this->autos;
    }

    private function getAutosPage($url){
        $document = HtmlDomParser::str_get_html(Parser::parse($url));
        // echo $url;
        // echo $document;
        $rows = $document->find('#searchResults .ticket-item');
        for ($i = 0; $i < count($rows); $i++) {
            $pageLink = $rows[$i]->find('.address')[0]->attr['href'];
            if(strripos($pageLink, 'gosalon')) {continue;}
            if(!strcasecmp($pageLink, $this->lastAuto) OR $this->quantity-- <= 0) {
                $this->exitFlag = true;
                break;
            }
            $auto = $this->getData($pageLink);

            if((int)($auto->autoType[0]) != 0) {
                $this->quantity++;
                continue;
            }
            $this->autos[] = $auto;
        }
    }

    private function getData($url) {
        $document = HtmlDomParser::str_get_html(Parser::parse($url));
        $codes = explode('_', str_replace('.html' , '', $url));
        $urlDate = str_replace('XXXXX', $codes[count($codes)-1], $this->dateAddress);
        $jsonResult = json_decode(Parser::parse($urlDate));
        $fullName = explode(' ', str_replace('Новый ', '', $document->find(".auto-content_title")[0]->plaintext));

        return new Auto(
            $document->find(".photo-620x465 .m-auto")[0]->attr['src'],
            date_format(date_create($jsonResult->addDate), 'd.m.Y'),
            date_format(date_create($jsonResult->addDate), 'H:i'),
            $fullName[0],
            implode( array_splice($fullName, 1, count($fullName)-2), ' '),
            trim($document->find(".price_value strong")[0]->plaintext),
            trim(str_replace('  ', ' ', str_replace('/', '', explode('•', trim($document->find(".unstyle dd")[0]->plaintext))[0]))),
            $this->correctVolume($this->findInTable('Двигатель', $document)) . ', КПП: ' . $this->correctKpp($this->findInTable('Коробка передач', $document, 'Механическая')),
            $fullName[count($fullName)-1],
            $this->changeMilage(str_replace([' тыс. км', ' '], '', $this->findInTable('Пробег', $document))),
            $this->checkColor($document->find(".car-color")[0]->attr['style']),
            true,
            $jsonResult->stateData->regionName . ' область, ' . $jsonResult->stateData->name, 
            $url
        );
    }

    private function changeMilage($milage) {
        if((int)($milage) != 0) {
            return $milage . '000';
        } else {
            return $milage;
        }
    }
    private function findInTable($key, $document, $default = 'Не указано') {
        $value = '';
        $elementsTh = $document->find('.label');
        for ($i=0; $i < count($elementsTh); $i++) { 
            if(trim($elementsTh[$i]->innertext) == $key){
                return trim($elementsTh[$i]->next_sibling()->plaintext);
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