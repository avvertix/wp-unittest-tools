<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Tester\ApplicationTester;

use WpUnitTools\App\WpUnitToolsApplication;

class TestConfigureCommand extends \PHPUnit_Framework_TestCase
{

    /**
     * Test if the configure command execute all the steps and create the correct files
     */
    public function testCommandExecution()
    {

        $application = new WpUnitToolsApplication('Wordpress Unit Test Tools', '@package_version@');
        
        $command = $application->find('configure');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'       => $command->getName(),
            'tag' => '4.4.2'
        ));
        
        $this->assertEquals(0, $commandTester->getStatusCode());

        $this->assertRegExp('/Downloading require db patch file/', $commandTester->getDisplay());
        $this->assertRegExp('/Checking out Wordpress 4.4.2 test includes/', $commandTester->getDisplay());
        $this->assertRegExp('/Writing custom settings in wp-tests-config.php/', $commandTester->getDisplay());
        
        $this->assertFileExists('./tmp/wordpress-4.4.2.zip');
        $this->assertFileExists('./tmp/wordpress/license.txt');
        $this->assertFileExists('./tmp/wordpress/includes/includes-4.4.2.html');
        $this->assertFileExists('./tmp/wordpress/wp-tests-config.php');
        
        $test_file = file_get_contents('./tmp/wordpress/wp-tests-config.php');
        
        $this->assertRegExp( '/\'DB_NAME\', \'wptest\'/', $test_file  );
        $this->assertRegExp( '/\'DB_USER\', \'wptest\'/', $test_file  );
        $this->assertRegExp( '/\'DB_PASSWORD\', \'wptest\'/', $test_file  );
        $this->assertRegExp( '/\'DB_HOST\', \'localhost\'/', $test_file  );

    }
}