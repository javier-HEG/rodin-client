<?php

// Start the autoload
function RodinClientAutoload($className) {
	include_once($className . '.php');
}

spl_autoload_register('RodinClientAutoload');
