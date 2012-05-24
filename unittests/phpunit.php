<?php
/*
 * (c) 2012 Damien Corpataux
 *
 * Licensed under the GNU GPL v3.0 license,
 * accessible at http://www.gnu.org/licenses/gpl-3.0.html
 *
**/

// Requires PHPUnit library and dependancies
$vendors = dirname(__file__).'/vendors';
$phpunit = "{$vendors}/phpunit/";
$fileiterator = "{$vendors}/php-file-iterator/";
$codecoverage = "{$vendors}/php-code-coverage/";
$tokenstream = "{$vendors}/php-token-stream/";
$texttemplate = "{$vendors}/php-text-template/";
$timer = "{$vendors}/php-timer/";
$mockobjects = "{$vendors}/phpunit-mock-objects/";
set_include_path(get_include_path() . PATH_SEPARATOR . $phpunit);
set_include_path(get_include_path() . PATH_SEPARATOR . $fileiterator);
set_include_path(get_include_path() . PATH_SEPARATOR . $codecoverage);
set_include_path(get_include_path() . PATH_SEPARATOR . $tokenstream);
set_include_path(get_include_path() . PATH_SEPARATOR . $texttemplate);
set_include_path(get_include_path() . PATH_SEPARATOR . $timer);
set_include_path(get_include_path() . PATH_SEPARATOR . $mockobjects);
require "{$phpunit}/PHPUnit/Autoload.php";

/**
 * xView-specific PHPUnit Test Class.
 * @package Tests
 */
abstract class xView_PHPUnit_Framework_TestCase extends PHPUnit_Framework_TestCase
{

    function setUp() {
        // Loads xView Library
        require_once(dirname(__file__).'../../lib/View.php');
        // Setup path to default views path
        xView::$path = dirname(__file__).'/views';
    }
}

// PHPUnit autorun
if (PHP_SAPI==='cli') PHPUnit_TextUI_Command::main();