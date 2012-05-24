<?php
/*
 * (c) 2012 Damien Corpataux
 *
 * Licensed under the GNU GPL v3.0 license,
 * accessible at http://www.gnu.org/licenses/gpl-3.0.html
 *
**/

class ProcessTests extends xView_PHPUnit_Framework_TestCase {

    function test_tag_php() {
        $result = xView::render('tag_php');
        $this->assertSame('This is a PHP tag test', $result);
    }

    function test_tag_echo() {
        $result = xView::render('tag_echo');
        $this->assertSame('This is a ECHO tag test', $result);
    }
}