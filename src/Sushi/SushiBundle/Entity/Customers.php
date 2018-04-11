<?php

namespace Sushi\SushiBundle\Entity;

/**
 * Customers
 */
class Customers
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    private $delZoneId;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var integer
     */
    private $streetId;

    /**
     * @var string
     */
    private $building;

    /**
     * @var string
     */
    private $porch;

    /**
     * @var string
     */
    private $app;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Customers
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set delZoneId
     *
     * @param integer $delZoneId
     *
     * @return Customers
     */
    public function setDelZoneId($delZoneId)
    {
        $this->delZoneId = $delZoneId;

        return $this;
    }

    /**
     * Get delZoneId
     *
     * @return integer
     */
    public function getDelZoneId()
    {
        return $this->delZoneId;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Customers
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set streetId
     *
     * @param integer $streetId
     *
     * @return Customers
     */
    public function setStreetId($streetId)
    {
        $this->streetId = $streetId;

        return $this;
    }

    /**
     * Get streetId
     *
     * @return integer
     */
    public function getStreetId()
    {
        return $this->streetId;
    }

    /**
     * Set building
     *
     * @param string $building
     *
     * @return Customers
     */
    public function setBuilding($building)
    {
        $this->building = $building;

        return $this;
    }

    /**
     * Get building
     *
     * @return string
     */
    public function getBuilding()
    {
        return $this->building;
    }

    /**
     * Set porch
     *
     * @param string $porch
     *
     * @return Customers
     */
    public function setPorch($porch)
    {
        $this->porch = $porch;

        return $this;
    }

    /**
     * Get porch
     *
     * @return string
     */
    public function getPorch()
    {
        return $this->porch;
    }

    /**
     * Set app
     *
     * @param string $app
     *
     * @return Customers
     */
    public function setApp($app)
    {
        $this->app = $app;

        return $this;
    }

    /**
     * Get app
     *
     * @return string
     */
    public function getApp()
    {
        return $this->app;
    }
}
