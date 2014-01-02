<?php

/**
 * @see Relay_Measure_Abstract
 */
require_once 'Relay/Measure/Abstract.php';

class Relay_Measure_BinarySI extends Relay_Measure_Abstract
{
    const TERABYTE  = 'TERABYTE';
    const GIGABYTE  = 'GIGABYTE';
    const MEGABYTE  = 'MEGABYTE';
    const KILOBYTE  = 'KILOBYTE';
    const BYTE      = 'BYTE';
    const BIT       = 'BIT';
    const BASE      = 'BYTE';

    protected $_units = array(
        'TERABYTE'      => array('TB', 1000000000000),
        'GIGABYTE'      => array('GB', 1000000000),
        'MEGABYTE'      => array('MB', 1000000),
        'KILOBYTE'      => array('KB', 1000),
        'BYTE'          => array('B',  1),
        'BIT'           => array('b',  0.125),
        'BASE'          => 'BYTE',
        self::OPTIMAL   => 0,
    );
}