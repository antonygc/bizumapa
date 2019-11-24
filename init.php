<?php
// define the autoloader
require_once 'lib/adianti/core/AdiantiCoreLoader.php';
require_once 'app/control/custom/CustomApplicationUtils.php';

spl_autoload_register(array('Adianti\Core\AdiantiCoreLoader', 'autoload'));
Adianti\Core\AdiantiCoreLoader::loadClassMap();

$loader = require 'vendor/autoload.php';
$loader->register();

// read configurations
$ini = parse_ini_file('app/config/application.ini', true);
date_default_timezone_set($ini['general']['timezone']);
AdiantiCoreTranslator::setLanguage( $ini['general']['language'] );
ApplicationTranslator::setLanguage( $ini['general']['language'] );
AdiantiApplicationConfig::load($ini);

// define constants
define('APPLICATION_NAME', $ini['general']['application']);
define('OS', strtoupper(substr(PHP_OS, 0, 3)));
define('PATH', dirname(__FILE__));
define('LANG', $ini['general']['language']);

define('DEFAULT_DB', $ini['custom']['default_db']);
define('API_KEY', 	 $ini['custom']['api_key']);
define('ENC_KEY',    $ini['custom']['enc_key']);
define('PLAN_ID',    $ini['custom']['plan_id']);

if (version_compare(PHP_VERSION, '5.5.0') == -1)
{
    die(AdiantiCoreTranslator::translate('The minimum version required for PHP is ^1', '5.5.0'));
}
