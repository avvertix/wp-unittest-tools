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
use WpUnitTools\Traits\InteractWithCommands;

class ConfigureCommand extends Command
{
    
    use InteractWithWordpress, InteractWithCommands;
    
    protected function configure()
    {
        $this
            ->setName('configure')
            ->setDescription('Configure the testing environment.')
            ->addArgument(
                'tag',
                InputArgument::OPTIONAL,
                'The Wordpress version tag (e.g. 4.4.2). Default value is latest, so the last Wordpress version will be downloaded',
                'latest'
            )
            // ->addOption(
            //     'dir',
            //     null,
            //     InputOption::VALUE_REQUIRED,
            //     'The directory in which the wordpress version will be downloaded.'
            // )
            // ->addOption(
            //     'test-dir',
            //     null,
            //     InputOption::VALUE_REQUIRED,
            //     'The directory in which the wordpress version will be downloaded.'
            // )
            ->addOption(
                'user',
                null,
                InputOption::VALUE_REQUIRED,
                'The database username.',
                'wptest'
            )
            ->addOption(
                'pass',
                null,
                InputOption::VALUE_REQUIRED,
                'The database password.',
                'wptest'
            )
            ->addOption(
                'db',
                null,
                InputOption::VALUE_REQUIRED,
                'The database name.',
                'wptest'
            )
            ->addOption(
                'host',
                null,
                InputOption::VALUE_REQUIRED,
                'The database host.',
                'localhost'
            )
            
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $wp_version = $input->getArgument('tag');
        
        $wp_tests_dir = $this->getWordpressTestsDirectory( $input );
        
        $db_name = $input->getOption('db');
        $db_user = $input->getOption('user');
        $db_pass = $input->getOption('pass');
        $db_host = $input->getOption('host');
        
        // call downloadwp
        
        $this->call( $output, 'downloadwp', ['tag' => $wp_version]);
        
        // call installwp
        
        $this->call( $output, 'installwp', ['tag' => $wp_version]);

        // change database settings
        
        $wp_config_file = $wp_tests_dir . '/wp-tests-config.php';
        
        $output->writeln( 'Writing custom settings in wp-tests-config.php file...' );
        
        $wp_tests_config = file_get_contents( $wp_config_file );
        
        $wp_tests_config = preg_replace( '/(youremptytestdbnamehere)/', $db_name, $wp_tests_config );
        $wp_tests_config = preg_replace( '/(yourusernamehere)/', $db_user, $wp_tests_config );
        $wp_tests_config = preg_replace( '/(yourpasswordhere)/', $db_pass, $wp_tests_config );
        $wp_tests_config = preg_replace( '/(localhost)/', $db_host, $wp_tests_config );
        
        file_put_contents( $wp_config_file, $wp_tests_config );
        
        $output->writeln( 'Configuration completed.' );
        
        $output->writeln( 'Wordpress config file stored in: ' . $wp_config_file );

    }
    
}
