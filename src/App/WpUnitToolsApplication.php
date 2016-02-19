<?php namespace WpUnitTools\App;

use Symfony\Component\Console\Application;
use Symfony\Component\Finder\Finder;
use WpUnitTools\Commands\DownloadWordpressCommand;
use WpUnitTools\Commands\InstallWordpressTestSuiteCommand;
use WpUnitTools\Commands\ConfigureCommand;
use WpUnitTools\Commands\StubsCommand;

use Symfony\Component\Console\Input\InputInterface;

/**
 *
 */
final class WpUnitToolsApplication extends Application {

	
	protected function getDefaultCommands()
    {
        // Keep the core default commands to have the HelpCommand
        // which is used when using the --help option
        $defaultCommands = parent::getDefaultCommands();

        $defaultCommands[] = new DownloadWordpressCommand();
        $defaultCommands[] = new InstallWordpressTestSuiteCommand();
        $defaultCommands[] = new ConfigureCommand;
        $defaultCommands[] = new StubsCommand;
        
        return $defaultCommands;
    }
    
	
	
	


	// Files and folder facility
	
	
	public function isFile($path, $absolute=false){

		return is_file($this->getRealPath($path));
	}
	
	public function isDir($path, $absolute=false){
		return is_dir($this->getRealPath($path));
	}
    
    public function createDir($path){
        
		$created = mkdir($this->getRealPath($path), 0777, true);
        
        if( !$created ){
            throw new \Exception("The folder $path cannot be created.", 500);
        }
        
        
	}
	
	public function getFile($path, $absolute=false){
		return file_get_contents($this->getRealPath($path));
	}
	
	public function getRealPath($path){
		if($path[0] == '.'){
			$pharFile = \Phar::running(false);
			return /*realpath(*/('' === $pharFile ? '' : dirname($pharFile) . DIRECTORY_SEPARATOR) . $path/*)*/;	
		}
		else {
			return $path;
		}
		
	}
	
	
	public function getFilesFromFolder($folder){
		
		$finder = new Finder();
		$finded = $finder->ignoreUnreadableDirs()
			->name('*.pdf')
			->name('*.doc')
			->name('*.docx')
			->name('*.ppt')
			->name('*.pptx')
			->name('*.xls')
			->name('*.xlsx')
			->name('*.jpg')
			->name('*.gif')
			->name('*.png')
			->files()->in($folder);
		
		return array_values(array_map(function($el){
			return $el->getRealpath();
		}, iterator_to_array($finded)));
		
	}
    
    
    public function download( $url, $destination_path, $show_progress = false){
        
    }
    
    
    public function getWordpressCoreDirectory( InputInterface $input ){
        
        $dir = $input->getOption('dir', './tmp/wordpress/');
        if( is_null( $dir ) ){
            $dir = './tmp/wordpress/';
        }
        
        if( !$this->isDir( $dir ) ){
            $this->createDir( $dir );
        }
        
        return $this->getRealPath( $dir );
        
    }
    
    public function getWordpressTestsDirectory( InputInterface $input ){
        
        $dir = $input->getOption('test-dir', './tmp/wordpress-tests-lib/');
        if( is_null( $dir ) ){
            $dir = './tmp/wordpress-tests-lib/';
        }
        
        if( !$this->isDir( $dir ) ){
            $this->createDir( $dir );
        }
        
        return $this->getRealPath( $dir );
        
    }
	
} 