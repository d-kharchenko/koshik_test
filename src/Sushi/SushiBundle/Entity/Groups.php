<?php

namespace Sushi\SushiBundle\Entity;

/**
 * Groups
 */
class Groups
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
    private $orderGroup;


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
     * @return Groups
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
     * Set orderGroup
     *
     * @param integer $orderGroup
     *
     * @return Groups
     */
    public function setOrderGroup($orderGroup)
    {
        $this->orderGroup = $orderGroup;

        return $this;
    }

    /**
     * Get orderGroup
     *
     * @return integer
     */
    public function getOrderGroup()
    {
        return $this->orderGroup;
    }
}
