<?php

declare(strict_types=1);

namespace MadeByMouses\StorePickup\Api\Data;

interface LocationInterface
{
    const LOCATION_ID = 'location_id';
    const IS_ACTIVE = 'is_active';
    const IDENTIFIER = 'identifier';
    const LABEL = 'label';
    const COMPANY = 'company';
    const STREET = 'street';
    const POSTCODE = 'postcode';
    const CITY = 'city';
    const COUNTRY_ID = 'country_id';
    const REGION = 'region';
    const REGION_ID = 'region_id';
    const TELEPHONE = 'telephone';
    const PRICE = 'price';

    /**
     * @return string|null
     */
    public function getLocationId();

    /**
     * @return int|bool
     */
    public function getIsActive();

    /**
     * @return string|null
     */
    public function getIdentifier();


    /**
     * @return string|null
     */
    public function getLabel();

    /**
     * @return string|null
     */
    public function getCompany();

    /**
     * @return string|null
     */
    public function getStreet();

    /**
     * @return string|null
     */
    public function getPostcode();

    /**
     * @return string|null
     */
    public function getCity();

    /**
     * @return string|null
     */
    public function getCountryId();

    /**
     * @return string|null
     */
    public function getRegion();

    /**
     * @return int|null
     */
    public function getRegionId();

    /**
     * @return string|null
     */
    public function getTelephone();

    /**
     * @return float|null
     */
    public function getPrice();

    /**
     * @param string $locationId
     * @return \MadeByMouses\StorePickup\Api\Data\LocationInterface
     */
    public function setLocationId($locationId);

    /**
     * @param int|bool $isActive
     * @return \MadeByMouses\StorePickup\Api\Data\LocationInterface
     */
    public function setIsActive($isActive);

    /**
     * @param string $identifier
     * @return \MadeByMouses\StorePickup\Api\Data\LocationInterface
     */
    public function setIdentifier($identifier);

    /**
     * @param string $label
     * @return \MadeByMouses\StorePickup\Api\Data\LocationInterface
     */
    public function setLabel($label);

    /**
     * @param string $company
     * @return \MadeByMouses\StorePickup\Api\Data\LocationInterface
     */
    public function setCompany($company);

    /**
     * @param string $street
     * @return \MadeByMouses\StorePickup\Api\Data\LocationInterface
     */
    public function setStreet($street);

    /**
     * @param string $postcode
     * @return \MadeByMouses\StorePickup\Api\Data\LocationInterface
     */
    public function setPostcode($postcode);

    /**
     * @param string $city
     * @return \MadeByMouses\StorePickup\Api\Data\LocationInterface
     */
    public function setCity($city);

    /**
     * @param string $countryId
     * @return \MadeByMouses\StorePickup\Api\Data\LocationInterface
     */
    public function setCountryId($countryId);

    /**
     * @param string $region
     * @return \MadeByMouses\StorePickup\Api\Data\LocationInterface
     */
    public function setRegion($region);

    /**
     * @param int $regionId
     * @return \MadeByMouses\StorePickup\Api\Data\LocationInterface
     */
    public function setRegionId($regionId);

    /**
     * @param string $telephone
     * @return \MadeByMouses\StorePickup\Api\Data\LocationInterface
     */
    public function setTelephone($telephone);

    /**
     * @param float $price
     * @return \MadeByMouses\StorePickup\Api\Data\LocationInterface
     */
    public function setPrice($price);
}
