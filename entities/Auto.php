<?php

namespace entity;

/**
 * Class Auto Сущность объявления о продаже автомобиля
 * @package entity
 */
class Auto {
    public $autoPhoto = "";
    public $publicationDate = "";
    public $publicationTime = "";
    public $carMake = "";
    public $car = "";
    public $price = "";
    public $autoType = "";
    public $engineCapacity = "";
    public $yearOfIssue = "";
    public $mileage = "";
    public $colorHex = "";
    public $userType = "";
    public $address = "";
    public $link = "";


    public function __construct($autoPhoto, $publicationDate, $publicationTime, $carMake, $car, $price, $autoType,
        $engineCapacity, $yearOfIssue, $mileage, $colorHex, $userType, /*$phone,*/ $address, $link) {

        /**/$this->autoPhoto = $autoPhoto;
        /**/$this->publicationDate = $publicationDate;
        /**/$this->publicationTime = $publicationTime;

        /**/$this->carMake = $carMake;
        /**/$this->car = $car;
        /**/$this->price = $price;

        /**/$this->autoType = $autoType;
        /**/$this->engineCapacity = $engineCapacity;
        /**/$this->yearOfIssue = $yearOfIssue;

        /**/$this->mileage = $mileage;
        /**/$this->colorHex = $colorHex;
        $this->userType = $userType;

        /**/$this->address = $address;
        /**/$this->link = $link;
    }


}