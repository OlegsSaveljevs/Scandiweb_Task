<?php
/**
 * @category Scandiweb
 * @package Scandiweb\ScandiwebTest
 * @author Olegs Saveljevs <olegs.saveljevs@scandiweb.com>
 * @copyright Copyright (c) 2025 Scandiweb, Ltd (https://scandiweb.com)
 * @license   http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

use Magento\Framework\Component\ComponentRegistrar;
ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Scandiweb_ScandiwebTest',
    __DIR__
);