<?php namespace WpUnitTools\Traits;


/**
 * Wordpress related facilities 
 *
 * @uses InteractWithHttp; 
 * @uses InteractWithFilesystem; 
 */
trait InteractWithWordpress
{
    
    use InteractWithHttp, InteractWithFilesystem;
    
    
    function getWordpressVersionInfo( $wp_version = 'latest' ){
        
        if( preg_match( '/^[0-9]+\.[0-9]+(\.[0-9]+)?$/', $wp_version ) ){
            $wp_version_tag = 'tags/' . $wp_version;
        }
        else if( is_null( $wp_version ) || !is_null( $wp_version ) && $wp_version === 'latest') {
            
            if( !$this->isFile( './tmp/wp-version.json' ) ){
                
                // $output->writeln( 'Getting Wordpress latest version details...' );
                
                $this->downloadTo( 'http://api.wordpress.org/core/version-check/1.7/', $this->getRealPath( './tmp/wp-version.json' ) );

            }
            
            $content = file_get_contents( $this->getRealPath( './tmp/wp-version.json' ) );
            
            $wp_upgrade_obj = json_decode( $content );
            
            if( !$wp_upgrade_obj ){
                throw new Exception("Latest WordPress version could not be found", 1);
            }
            
            $wp_version = $wp_upgrade_obj->offers[0]->current;
            $wp_version_tag = 'tags/' . $wp_version;
            
        }
        else {
            throw new \Exception("Unkwnown or wrong Wordpress version $wp_version", 1);
        }
        
        
        $svn_path = 'https://develop.svn.wordpress.org/' . $wp_version_tag .'/';
        
        $res = new \stdClass;
        $res->version = $wp_version;
        $res->tag = $wp_version_tag;
        $res->package = 'https://wordpress.org/wordpress-' . $wp_version .'.zip';
        $res->source_code = $svn_path . $wp_version_tag;
        $res->example_config = $svn_path . '/wp-tests-config-sample.php';
        $res->phpunit_includes_source = $svn_path . '/tests/phpunit/includes/';
        
        return $res; 
    }
    
    
    
}
