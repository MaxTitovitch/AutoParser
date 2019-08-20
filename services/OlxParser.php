<?php

namespace Parsing;

use entity\Auto;
use Sunra\PhpSimple\HtmlDomParser;

use Sunra\PhpSimpl;

class OlxParser {
    private $url;
    private $lastAuto;
    private $quantity;
    private $exitFlag = false;

    private $autos = [];

    private $mounts = ['января', 'февраля', 'марта', 'мая', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];

    public function __construct($url, $lastAuto, $quantity) {
        $this->url = $url;
        $this->lastAuto = $lastAuto;
        $this->quantity = $quantity;
    }

    public function getAutos(){
        $id = 1;
        while (!$this->exitFlag){
            $this->getAutosPage($this->url . ($id == 1 ? '' : '?page=' . $id));
            $id++;
        }
        return $this->autos;
    }

    private function getAutosPage($url){
        $document = HtmlDomParser::str_get_html(Parser::parse($url));
        $rows = $document->find('#offers_table tbody')[0]->find('> .wrap');
        for ($i = 0; $i < count($rows); $i++) {
            $pageLink = $rows[$i]->find('.marginright5')[0]->attr['href'];
            if(!strcasecmp($pageLink, $this->lastAuto) OR $this->quantity <= 0) {
                $this->exitFlag = true;
                break;
            }
            $auto =  $this->getData($pageLink);
            if($auto) {
                $this->autos[] = $auto;
                $this->quantity--;
            }
        }
    }

    private function getData($url){        
        try {
            $document = HtmlDomParser::str_get_html(Parser::parse($url));
            $timeVal = $document->find(".offer-titlebox__details em")[0];
            if($timeVal != false)
                $timeVal->find('a,small')[0]->outertext = "";

            $dateTime = $this->getDateTime((string)$document->find(".offer-titlebox__details em")[0]->innertext);
            // echo $this->findInTable('Тип кузова', $document) . '<hr>';
            return new Auto(
                $document->find(".bigImage")[0]->attr['src'],
                $dateTime[0],
                $dateTime[1],
                $this->findInTable('Марка', $document),
                $this->findInTable('Модель', $document),
                str_replace('.', '', trim($document->find('#offeractions strong')[0]->plaintext)),
                str_replace('  ', ' ', str_replace('/', '', $this->findInTable('Тип кузова', $document))),
                $this->findInTable('Объем двигателя', $document) . ', КПП: ' . $this->findInTable('Коробка передач', $document, 'Механическая'),
                $this->findInTable('Год выпуска', $document),
                str_replace(['км', ' '], '', $this->findInTable('Пробег', $document)),
                Parser::getColor($this->findInTable('Цвет', $document)),
                $this->findInTable('Объявление от', $document, 'Частного лица') == 'Частного лица' ? true : false,
                $document->find('.show-map-link')[0]->plaintext, 
                $url
            );
        } catch(Exception $e){
            return null;
        }
    }

    private function findInTable($key, $document, $default = 'Не указано') {
        $value = '';
        $elementsTh = $document->find('.details th');
        // echo count($elementsTh);
        for ($i=0; $i < count($elementsTh); $i++) { 
            if(trim($elementsTh[$i]->innertext) == $key){
                return trim($elementsTh[$i]->next_sibling()->plaintext);
            }
        }
        return $default;
    }

    private function getDateTime($string) {
        $date = $this->formateDate(explode (',', $string)[1]);        
        preg_match('/([0-1]\d|2[0-3])(:[0-5]\d)/', $string, $time);        
        if(strlen($date) == 9) $date = '0'.$date;
        return [$date, $time[0]];
    }

    private function formateDate($date) {
        $date = str_replace(' ', '', $date);
        for ($i=1; $i < count($this->mounts)+1; $i++) { 
            if($i < 10) {
                $date = str_replace($this->mounts[$i], '.0' . $i . '.', $date);
            } else {
                $date = str_replace($this->mounts[$i], '.' . $i. '.', $date);
            }
        }
        return $date;
    }
}