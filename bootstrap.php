<?php

define('RODINSERVER', '127.0.0.1/rodin-server');

// Start the autoload
function RodinClientAutoload($className) {
	include_once($className . '.php');
}

spl_autoload_register('RodinClientAutoload');
