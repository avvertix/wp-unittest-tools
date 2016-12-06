<?php namespace WpUnitTools\Traits;

use WpUnitTools\WordpressVersion;

/**
 * Wordpress related facilities 
 *
 * @uses InteractWithHttp 
 * @uses InteractWithFilesystem 
 * @uses WpUnitTools\WordpressVersion 
 */
trait InteractWithWordpress
{
    
    use InteractWithHttp, InteractWithFilesystem;

    /**
     * Get the Wordpress version information
     *
     * @param string $wp_version The wordpress version formatted as `MAJOR.MINOR.PATCH` or `latest`. Default `latest`
     * @return WordpressVersion the Wordpress version details
     */
    function getWordpressVersionInfo( $wp_version = 'latest' )
    {
        
        if( preg_match( '/^[0-9]+\.[0-9]+(\.[0-9]+)?$/', $wp_version ) )
        {
            $wp_version_tag = 'tags/' . $wp_version;
        }
        else if( is_null( $wp_version ) || !is_null( $wp_version ) && $wp_version === 'latest')
        {
            
            if( !$this->isFile( WordpressVersion::WP_VERSION_PATH ) )
            {
                
                $this->downloadTo( WordpressVersion::WORDPRESS_API_URL, $this->getRealPath( WordpressVersion::WP_VERSION_PATH ) );

            }
            
            $content = file_get_contents( $this->getRealPath( WordpressVersion::WP_VERSION_PATH ) );
            
            $wp_upgrade_obj = json_decode( $content );
            
            if( !$wp_upgrade_obj )
            {
                throw new Exception("Latest WordPress version could not be found", 1);
            }
            
            $wp_version = $wp_upgrade_obj->offers[0]->current;
            $wp_version_tag = 'tags/' . $wp_version;
            
        }
        else 
        {
            throw new \Exception("Unkwnown or wrong Wordpress version $wp_version", 1);
        }
        
        $res = new WordpressVersion($wp_version, $wp_version_tag);
        
        return $res; 
    }


    /**
     * Download the Wordpress includes for testing 
     *
     * @param WordpressVersion $wp the Wordpress version details
     * @param string $testsDirectory the local directory that contains the tests
     */
    protected function downloadWordpressTestIncludes(WordpressVersion $wp, $testsDirectory)
    {

        $wp_version = $wp->version();

        $includes_file = $this->getRealPath( $testsDirectory . 'includes/includes-'. $wp_version .'.html' );
        $includes_directory = $this->getRealPath( $testsDirectory . 'includes/' );

        $this->downloadFromList($wp->phpunitIncludesUrl(), $includes_directory, $includes_file);

    }
    
    
    
}
