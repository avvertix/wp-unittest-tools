<?php

namespace WpUnitTools\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;

use WpUnitTools\Traits\InteractWithFilesystem;

class StubsCommand extends Command
{
    
    use InteractWithFilesystem;
    
    protected function configure()
    {
        $this
            ->setName('stub')
            ->setAliases( ['stubs'] )
            ->setDescription('Creates the base phpunit.xml and tests/bootstrap.php file for running the tests.')
            ->addArgument(
                'test-folder',
                InputArgument::OPTIONAL,
                'The folder that will contain the unittests',
                './tests'
            )
            ->addOption(
                'plugin',
                null,
                InputOption::VALUE_REQUIRED,
                'The main file of the Wordpress plugin to test.',
                'false'
            )
            ->addOption(
                'phpunit',
                null,
                InputOption::VALUE_REQUIRED,
                'The name of the phpunit configuration file.',
                'phpunit.xml'
            )
            
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $test_folder = $input->getArgument('test-folder');
        
        $plugin = $input->getOption('plugin');
        $phpunit_filename = $input->getOption('phpunit');

        if( !$this->isDir($test_folder) ){
            $this->createDir($test_folder);
        }

        $pharFile = \Phar::running(true);

        $phpunit_stub_path = '' === $pharFile ? './stub/phpunit.xml' : $pharFile . '/stub/phpunit.xml';
        $bootstrap_stub_path = '' === $pharFile ? './stub/bootstrap.php' : $pharFile . '/stub/bootstrap.php' ;
        $exampletest_stub_path = '' === $pharFile ? './stub/example-test-case.php' : $pharFile . '/stub/example-test-case.php' ;
        
        
        $wp_tests_config = file_get_contents( $phpunit_stub_path );
        
        $output->writeln( 'Creating bootstrap.php file...' );
        copy( $bootstrap_stub_path, $test_folder . '/bootstrap.php' );
        
        $output->writeln( 'Creating example test case file (example-test-case.php)...' );
        copy( $exampletest_stub_path, $test_folder . '/example-test-case.php' );
        
        $output->writeln( 'Creating '. $phpunit_filename .' file...' );
        $wp_tests_config = preg_replace( '/({test_folder})/', $test_folder, $wp_tests_config );
        $wp_tests_config = preg_replace( '/({test_folder})/', $test_folder, $wp_tests_config );
        $wp_tests_config = preg_replace( '/({PLUGIN_FILE})/', $plugin, $wp_tests_config );
        
        file_put_contents( './' . $phpunit_filename, $wp_tests_config );
        
        $output->writeln( 'Stub creation completed.' );

    }
    
}
