<?php

/**
 * @see Relay_Measure_Abstract
 */
require_once 'Relay/Measure/Abstract.php';

class Relay_Measure_Binary extends Relay_Measure_Abstract
{
    const TERABYTE  = 'TERABYTE';
    const GIGABYTE  = 'GIGABYTE';
    const MEGABYTE  = 'MEGABYTE';
    const KILOBYTE  = 'KILOBYTE';
    const BYTE      = 'BYTE';
    const BIT       = 'BIT';
    const BASE      = 'BYTE';

    protected $_units = array(
        'TERABYTE'      => array('TiB', 1099511627776),
        'GIGABYTE'      => array('GiB', 1073741824),
        'MEGABYTE'      => array('MiB', 1048576),
        'KILOBYTE'      => array('KiB', 1024),
        'BYTE'          => array('B',   1),
        'BIT'           => array('b',   0.125),
        'BASE'          => 'BYTE',
        self::OPTIMAL   => 0,
    );
}