<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

require_once(dirname(__FILE__).'/../Source/Framework/Startup.php'); // path to source folder where framework and application logic are stored

Startup::launchApplication( dirname(__FILE__).'/../../Settings/' ); // path to settings folder where all connection, helper, and application settings are stored
RuntimeInfo::instance()->handlePageRequest();
