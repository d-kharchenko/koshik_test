<?php

namespace Sushi\SushiBundle\Entity;

/**
 * GlSettings
 */
class GlSettings
{
    /**
     * @var string
     */
    private $paramName;

    /**
     * @var string
     */
    private $paramValue;

    /**
     * Get paramName
     *
     * @return string
     */
    public function getParamName()
    {
        return $this->paramName;
    }

    /**
     * Set paramValue
     *
     * @param string $paramValue
     *
     * @return GlSettings
     */
    public function setParamValue($paramValue)
    {
        $this->paramValue = $paramValue;

        return $this;
    }

    /**
     * Get paramValue
     *
     * @return string
     */
    public function getParamValue()
    {
        return $this->paramValue;
    }
    
}
