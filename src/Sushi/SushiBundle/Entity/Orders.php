<?php

namespace Sushi\SushiBundle\Entity;

/**
 * Orders
 */
class Orders
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var integer
     */
    private $customerId;

    /**
     * @var string
     */
    private $customerName;

    /**
     * @var string
     */
    private $customerPhone;

    /**
     * @var integer
     */
    private $sticksQty;

    /**
     * @var integer
     */
    private $stickStudQty;

    /**
     * @var integer
     */
    private $delZoneId;

    /**
     * @var string
     */
    private $delPrice;

    /**
     * @var integer
     */
    private $streetId;

    /**
     * @var string
     */
    private $streetName;

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
     * @var string
     */
    private $sum;

    /**
     * @var \DateTime
     */
    private $delTime;

    /**
     * @var boolean
     */
    private $delInTime;
    
    /**
     * @var integer
     */
    private $warehouseId;

    /**
     * @var string
     */
    private $comment;

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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Orders
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set customerId
     *
     * @param integer $customerId
     *
     * @return Orders
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;

        return $this;
    }

    /**
     * Get customerId
     *
     * @return integer
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * Set customerName
     *
     * @param string $customerName
     *
     * @return Orders
     */
    public function setCustomerName($customerName)
    {
        $this->customerName = $customerName;

        return $this;
    }

    /**
     * Get customerName
     *
     * @return string
     */
    public function getCustomerName()
    {
        return $this->customerName;
    }

    /**
     * Set customerPhone
     *
     * @param string $customerPhone
     *
     * @return Orders
     */
    public function setCustomerPhone($customerPhone)
    {
        $this->customerPhone = $customerPhone;

        return $this;
    }

    /**
     * Get customerPhone
     *
     * @return string
     */
    public function getCustomerPhone()
    {
        return $this->customerPhone;
    }

    /**
     * Set sticksQty
     *
     * @param integer $sticksQty
     *
     * @return Orders
     */
    public function setSticksQty($sticksQty)
    {
        $this->sticksQty = $sticksQty;

        return $this;
    }

    /**
     * Get sticksQty
     *
     * @return integer
     */
    public function getSticksQty()
    {
        return $this->sticksQty;
    }

    /**
     * Set stickStudQty
     *
     * @param boolean $stickStudQty
     *
     * @return Orders
     */
    public function setStickStudQty($stickStudQty)
    {
        $this->stickStudQty = $stickStudQty;

        return $this;
    }

    /**
     * Get stickStudQty
     *
     * @return boolean
     */
    public function getStickStudQty()
    {
        return $this->stickStudQty;
    }

    /**
     * Set delZoneId
     *
     * @param integer $delZoneId
     *
     * @return Orders
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
     * Set delPrice
     *
     * @param string $delPrice
     *
     * @return Orders
     */
    public function setDelPrice($delPrice)
    {
        $this->delPrice = $delPrice;

        return $this;
    }

    /**
     * Get delPrice
     *
     * @return string
     */
    public function getDelPrice()
    {
        return $this->delPrice;
    }

    /**
     * Set streetId
     *
     * @param integer $streetId
     *
     * @return Orders
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
     * Set streetName
     *
     * @param string $streetName
     *
     * @return Orders
     */
    public function setStreetName($streetName)
    {
        $this->streetName = $streetName;

        return $this;
    }

    /**
     * Get streetName
     *
     * @return string
     */
    public function getStreetName()
    {
        return $this->streetName;
    }

    /**
     * Set building
     *
     * @param string $building
     *
     * @return Orders
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
     * @return Orders
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
     * @return Orders
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

    /**
     * Set sum
     *
     * @param string $sum
     *
     * @return Orders
     */
    public function setSum($sum)
    {
        $this->sum = $sum;

        return $this;
    }

    /**
     * Get sum
     *
     * @return string
     */
    public function getSum()
    {
        return $this->sum;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Orders
     */
    public function setDelTime($delTime)
    {
        $this->delTime = $delTime;

        return $this;
    }

    /**
     * Get delTime
     *
     * @return \DateTime
     */
    public function getDelTime()
    {
        return $this->delTime;
    }

    /**
     * Set delInTime
     *
     * @param boolean $delInTime
     *
     * @return Orders
     */
    public function setDelInTime($delInTime)
    {
        $this->delInTime = $delInTime;

        return $this;
    }

    /**
     * Get delInTime
     *
     * @return boolean
     */
    public function getDelInTime()
    {
        return $this->delInTime;
    }

    /**
     * Get warehouseId
     *
     * @return integer
     */
    public function getWarehouseId()
    {
        return $this->warehouseId;
    }
    
    /**
     * Set warehouseId
     *
     * @param string $warehouseId
     *
     * @return Orders
     */
    public function setWarehouseId($warehouseId)
    {
        $this->warehouseId = $warehouseId;

        return $this;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Orders
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }
}
