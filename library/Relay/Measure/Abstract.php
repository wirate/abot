<?php

/**
 * @see Relay_Exception
 */
require_once 'Relay/Exception.php';

abstract class Relay_Measure_Abstract
{
    const OPTIMAL = 'OPTIMAL';

    /**
     * The value.
     *
     * @var string
     */
    protected $_value = null;

    /**
     * The unit the value is represented in.
     *
     * @var string
     */
    protected $_unit = null;

    /**
     * Array of all units supported by this type.
     *
     * @var array
     */
    protected $_units = array();

    /**
     * Default percision to use when rounding values.
     *
     * @var integer
     */
    protected $_precision = 0;

    /**
     * Constructor, create and initialize a object.
     *
     * @param mixed     $value      The value.
     * @param string    $unit       (Optional) The unit the value is in.
     * @param integer   $precision  (Optional) Percision to use when rounding value.
     * @throws Relay_Exception
     * @return void
     */
    public function __construct($value, $unit = null, $precision = null)
    {
        if ($unit === null) {
            $unit = $this->_units['BASE'];
        }

        $this->setValue($value, $unit, $precision);
    }

    /**
     * Get the current unit.
     *
     * @return string
     */
    public function getUnit()
    {
        return $this->_unit;
    }

    /**
     * Set a new unit, and convert the value to this unit.
     *
     * @param string            $unit The unit that should be set.
     * @throws Relay_Exception
     * @return \Relay_Measure_Abstract
     */
    public function setUnit($unit)
    {
        if (!array_key_exists($unit, $this->_units)) {
            throw new Relay_Exception("Unit '$unit' does not exist");
        }

        if ($this->getUnit() !== $unit) {

            // First convert back to base unit.
            if ($this->getUnit() === null) {
                $value = $this->_value;
            } else {
                $value = $this->_value * $this->_units[$this->getUnit()][1];
            }
            
            // Then to expected unit.
            if ($unit === 'BASE') {
                $unit = $this->_units['BASE'];
            } else if ($unit == self::OPTIMAL) {
                $unit = $this->_units['BASE'];
                foreach($this->_units as $k => $v) {

                    if (is_array($v) && $value >= $v[1]) {
                        $unit = $k;
                        break;
                    }
                }
            }

            $value = $value / $this->_units[$unit][1];
            $this->_value = (string) $value;
        }

        $this->_unit = $unit;
        return $this;
    }

    /**
     * Return the value.
     *
     * @param integer $round (Optional) Rounds the value to an given precision.
     * @return string
     */
    public function getValue($round = null)
    {
        $value = $this->_value;

        if ($round === null) {
            $round = $this->getPrecision();
        }

        if (is_numeric($round) && $round > 0) {
            $value = round($value, $round, PHP_ROUND_HALF_ODD);
        }

        return $value;
    }

    /**
     * Set a new value.
     *
     * @param mixed     $value      The value.
     * @param string    $unit       (Optional) The unit the value is in.
     * @param integer   $precision  (Optional) Percision to use when rounding value.
     * @throws Relay_Exception
     * @return \Relay_Measure_Abstract
     */
    public function setValue($value, $unit = null, $precision = null)
    {
        $tmp = $this->_value;
        $this->_value = (string) $value;

        if ($unit === null) {
            $unit = $this->_units['BASE'];
        }
        
        try {
            $this->setUnit($unit);
        } catch(Relay_Exception $e) {
            // Set back the old value before
            // throwing the exception
            $this->_value = $tmp;
            throw $e;
        }

        $this->setPrecision($precision);

        return $this;
    }

    /**
     * Get the default percision.
     *
     * @return type
     */
    public function getPrecision()
    {
        return $this->_precision;
    }

    /**
     * Set default precision to use.
     *
     * @param mixed $value
     * @return void
     */
    public function setPrecision($value)
    {
        $value = (string) $value;
        if (!is_numeric($value) || $value < 0) {
            $value = 0;
        }
        $this->_precision = $value;
    }

    /**
     * Returns a string representation
     *
     * @param  integer $round (Optional) Runds the value to the given precision
     * @return string
     */
    public function toString($round = null)
    {
        $suffix = $this->_units[$this->getUnit()][0];
        return $this->getValue($round) . ' ' . $suffix;
    }

    /**
     * Returns a string representation
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}