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

class DownloadWordpressCommand extends Command
{
    
    use InteractWithWordpress;
    
    
    protected function configure()
    {
        $this
            ->setName('downloadwp')
            ->setAliases( ['wp'] )
            ->setDescription('Downloads the Wordpress version.')
            ->addArgument(
                'tag',
                InputArgument::OPTIONAL,
                'The Wordpress version tag (e.g. 4.4.2). Default value is latest, so the last Wordpress version will be downloaded',
                'latest'
            )
            ->addOption(
                'dir',
                null,
                InputOption::VALUE_REQUIRED,
                'The directory in which the wordpress version will be downloaded.'
            )
            
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $wp_obj = $this->getWordpressVersionInfo( $input->getArgument('tag') );
        
        $wp_core_dir = $this->getWordpressCoreDirectory( $input );
        
        
        $wp_version = $wp_obj->version;
        $wp_version_tag = $wp_obj->tag;

        
        $client = new \GuzzleHttp\Client();
        
        if( !$this->isFile('./tmp/wordpress-'.$wp_version.'.zip' ) ){
            $output->writeln( 'Downloading Wordpress '. $wp_version .'...' );
        
            $this->downloadTo( $wp_obj->package, $this->getRealPath( './tmp/wordpress-'.$wp_version.'.zip' ) );
        }
        
        // if( $res->getStatusCode() !== 200 ){
        //     throw new \Exception("Cannot retrieve Wordpress $wp_version distributable. Error " . $res->getStatusCode() . ' - ' . $res->getReasonPhrase(), 1);
        // }

        $output->writeln( 'Extracting Wordpress '. $wp_version .'...' );

        $zip = new \ZipArchive;
        if ($zip->open( $this->getRealPath( './tmp/wordpress.zip' ) ) === TRUE) {
            $zip->extractTo( $this->getRealPath( './tmp/') );
            $zip->close();
        } else {
            throw new \Exception("Cannot extract the downloaded wordpress archive ", 1);
        }
        
        $output->writeln( 'Downloading require db patch file...' );
        
        $this->downloadTo( 
            'https://raw.github.com/markoheijnen/wp-mysqli/master/db.php', 
            $this->getRealPath( './tmp/wordpress/wp-content/db.php' ) );
        
        $output->writeln( 'Wordpress '. $wp_version .' download completed' );
        
        return 0;
    }
}