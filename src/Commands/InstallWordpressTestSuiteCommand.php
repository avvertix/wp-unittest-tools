<?php

namespace WpUnitTools\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;

use WpUnitTools\Traits\InteractWithWordpress;

class InstallWordpressTestSuiteCommand extends Command
{
    
    use InteractWithWordpress;
    
    protected function configure()
    {
        $this
            ->setName('installwp')
            ->setAliases( ['wp-testsuite'] )
            ->setDescription('Downloads and configure the Wordpress test suite necessary for plugin unittests.')
            ->addArgument(
                'tag',
                InputArgument::OPTIONAL,
                'The Wordpress version tag (e.g. 4.4.2). Default value is latest, so the last Wordpress version will be downloaded',
                'latest'
            )
            ->addOption(
                'test-dir',
                null,
                InputOption::VALUE_REQUIRED,
                'The directory in which the wordpress version will be downloaded.'
            )
            
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $wp_obj = $this->getWordpressVersionInfo( $input->getArgument('tag') );
        
        $wp_version = $wp_obj->version;
        
        $wp_tests_dir = $this->getWordpressTestsDirectory( $input );

            
        $output->writeln( 'Checking out Wordpress '. $wp_version .' test includes...' );
        
        
        $includes_file = $this->getRealPath( './tmp/includes'. $wp_version .'.html' );
        $includes_directory = $this->getRealPath( './tmp/includes/' );
        
        if( !$this->isDir( $includes_directory ) ){
            $this->createDir( $includes_directory );
        }
        
        if( !$this->isFile($includes_file) ){
            $this->downloadTo( $wp_obj->phpunit_includes_source, $includes_file );
        }
        
        preg_match_all( '/^.*li.*href="(.*)".*$/m', file_get_contents( $includes_file ), $matches);
        
        if( empty($matches) || !empty($matches) && empty($matches[1]) ){
            throw new Exception("Cannot retrieve wordpress phpunit includes list", 20);
        }
        
        $files = array_filter($matches[1], function($el){
            return !empty($el) && $el[0] !== '.';
        });
        
        // TODO: this will be the case to use a ProgressBar
        
        foreach ($files as $file) {
            $this->downloadTo( $wp_obj->phpunit_includes_source . $file, $includes_directory . $file );
        }

        
        // if( !$this->getApplication()->isFile($wp_tests_dir . '/wp-tests-config.php') ){ }
        
        $output->writeln( 'Downloading wp-tests-config.php file...' );

        $this->downloadTo( $wp_obj->example_config, $wp_tests_dir . '/wp-tests-config.php' );
        
        
        $output->writeln( 'Wordpress '. $wp_version .' Test Stuite configuration completed' );
        
        return 0;
    }
}