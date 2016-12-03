<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Tester\ApplicationTester;

use WpUnitTools\App\WpUnitToolsApplication;

class TestStubsCommand extends \PHPUnit_Framework_TestCase
{

    /**
     * Test if the stubs command create the correct files
     */
    public function testCommandExecution()
    {

        $application = new WpUnitToolsApplication('Wordpress Unit Test Tools', '@package_version@');
        
        $command = $application->find('stub');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'       => $command->getName(),
            'test-folder' => './plugin-tests',
            '--phpunit'     => 'plugin-phpunit.xml',
        ));
        
        $this->assertEquals(0, $commandTester->getStatusCode());

        $this->assertRegExp('/Creating bootstrap.php/', $commandTester->getDisplay());
        $this->assertRegExp('/Creating example test case/', $commandTester->getDisplay());
        $this->assertRegExp('/Creating plugin-phpunit.xml file/', $commandTester->getDisplay());
        
        $this->assertFileExists('./plugin-tests/bootstrap.php');
        $this->assertFileExists('./plugin-tests/example-test-case.php');
        $this->assertFileExists('./plugin-phpunit.xml');
        
        $phpunit_file = file_get_contents('./plugin-phpunit.xml');
        
        $this->assertRegExp('/.*bootstrap=\".\/plugin-tests\/bootstrap.php\".*/', $phpunit_file);
        $this->assertRegExp('/suffix=\"\.php\"\>.\/plugin-tests\<\/directory/', $phpunit_file);

    }
}