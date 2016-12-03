<?php namespace WpUnitTools;

/**
 * Holds the Wordpress version information
 */
final class WordpressVersion
{

    /**
     * Wordpress SVN template url
     */
    const WORDPRESS_SVN = 'https://develop.svn.wordpress.org/%1$s/';

    /**
     * Wordpress release zip template url
     */
    const WORDPRESS_ZIP = 'https://wordpress.org/wordpress-%1$s.zip';

    /**
     * Wordpress version check api url
     */
    const WORDPRESS_API_URL = 'http://api.wordpress.org/core/version-check/1.7/';

    /**
     * Local wp-version.json file path. 
     * 
     * Used to store retrieved Wordpress version information
     */
    const WP_VERSION_PATH = './tmp/wp-version.json';

    /**
     * @var string
     */
    private $version;
    
    /**
     * @var string
     */
    private $tag;
    
    /**
     * @var string
     */
    private $package;
    
    /**
     * @var string
     */
    private $source_code;
    
    /**
     * @var string
     */
    private $example_config;
    
    /**
     * @var string
     */
    private $phpunit_includes_source;
    
    function __construct($version, $tag)
    {
        $svn_path = sprintf(self::WORDPRESS_SVN, $tag);

        $this->version = $version;
        $this->tag = $tag;
        $this->package = sprintf(self::WORDPRESS_ZIP, $version);
        $this->source_code = $svn_path . $tag;
        $this->example_config = $svn_path . '/wp-tests-config-sample.php';
        $this->phpunit_includes_source = $svn_path . '/tests/phpunit/includes/';
    }

    public function version()
    {
        return $this->version;
    }

    public function tag()
    {
        return $this->tag;
    }

    public function package()
    {
        return $this->package;
    }

    public function sourceCodeUrl()
    {
        return $this->source_code;
    }

    public function exampleConfigUrl()
    {
        return $this->example_config;
    }

    public function phpunitIncludesUrl()
    {
        return $this->phpunit_includes_source;
    }
}
