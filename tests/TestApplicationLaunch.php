<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Tester\ApplicationTester;

use WpUnitTools\App\WpUnitToolsApplication;

class TestApplicationLaunch extends \PHPUnit_Framework_TestCase
{

    /**
     * Only test if the app can shows the default command output when launched
     */
    public function testAppLaunch()
    {

        $application = new WpUnitToolsApplication('Wordpress Unit Test Tools', '@package_version@');
        $application->setAutoExit(false);
        
        
        $tester = new ApplicationTester( $application );
        
        $tester->run([]);
        
        $this->assertEquals(0, $tester->getStatusCode());
        
        $this->assertRegExp('/Wordpress Unit Test Tools version @package_version@/', $tester->getDisplay());

    }
}