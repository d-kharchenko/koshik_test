<?php

namespace Sushi\SushiBundle\Entity;

/**
 * Streets
 */
class Streets
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
     * @var string
     */
    private $nameOld;


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
     * @return Streets
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
     * Set nameOld
     *
     * @param string $nameOld
     *
     * @return Streets
     */
    public function setNameOld($nameOld)
    {
        $this->nameOld = $nameOld;

        return $this;
    }

    /**
     * Get nameOld
     *
     * @return string
     */
    public function getNameOld()
    {
        return $this->nameOld;
    }
}
