<?php

namespace Lightools\Ares;

/**
 * @author Jan Nedbal
 */
class Company {

    /**
     * @var int
     */
    private $identification;

    /**
     * @var string
     */
    private $companyname;

    /**
     * @var int
     */
    private $vatNumber;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $cityPart;

    /**
     * @var string
     */
    private $cityDistrict;

    /**
     * @var string
     */
    private $street;

    /**
     * @var string
     */
    private $houseNumber;

    /**
     * @var string
     */
    private $postalCode;

    /**
     * @param string $identification
     * @param string $companyname
     * @param string $vatNumber
     * @param string $city
     * @param string $cityPart
     * @param string $cityDistrict
     * @param string $street
     * @param string $houseNumber
     * @param string $postalCode
     */
    public function __construct($identification, $companyname, $vatNumber, $city, $cityPart, $cityDistrict, $street, $houseNumber, $postalCode) {
        $this->identification = $identification;
        $this->companyname = $companyname;
        $this->vatNumber = $vatNumber;
        $this->city = $city;
        $this->cityPart = $cityPart;
        $this->cityDistrict = $cityDistrict;
        $this->street = $street;
        $this->houseNumber = $houseNumber;
        $this->postalCode = $postalCode;
    }

    /**
     * @return string
     */
    public function getIdentification() {
        return $this->identification;
    }

    /**
     * @return string
     */
    public function getCompanyname() {
        return $this->companyname;
    }

    /**
     * @return string
     */
    public function getVatNumber() {
        return $this->vatNumber;
    }

    /**
     * e.g. Praha
     * @return string
     */
    public function getCity() {
        return $this->city;
    }

    /**
     * e.g. Michle
     * @return string
     */
    public function getCityPart() {
        return $this->cityPart;
    }

    /**
     * e.g. Praha 4
     * @return string
     */
    public function getCityDistrict() {
        return $this->cityDistrict;
    }

    /**
     * Street without house number
     * @return string
     */
    public function getStreet() {
        return $this->street;
    }

    /**
     * Complete house number, e.g. 15/6a
     * @return string
     */
    public function getHouseNumber() {
        return $this->houseNumber;
    }

    /**
     * @return string
     */
    public function getPostalCode() {
        return $this->postalCode;
    }

}
