<?php namespace WpUnitTools\Traits;


/**
 * 
 */
trait InteractWithHttp
{
    
    
    private $client = null;
    
    
    protected function getHttpClient(){
        
        if( is_null( $this->client ) ){
            $this->client = new \GuzzleHttp\Client();
        }
        
        return $this->client;
    }
    
    
    protected function downloadTo( $url, $destination_path ){

        $res = $this->getHttpClient()->request('GET', $url, [
            'sink' => $destination_path
        ]);
        
        if( $res->getStatusCode() !== 200 ){
            throw new \Exception( sprintf("Cannot download %1$s. Error %2$s - %3$s", $url, $res->getStatusCode(), $res->getReasonPhrase()), 1);
        }
        
        return $res;
    }
    
    protected function download( $url ){
        
        $res = $this->getHttpClient()->request('GET', $url);
        
        if( $res->getStatusCode() !== 200 ){
            throw new \Exception( sprintf("Cannot GET %1$s. Error %2$s - %3$s", $url, $res->getStatusCode(), $res->getReasonPhrase()), 1);
        }
        
        return $res;
    }

    /**
     * Download all the files and sub-folder from a web page that 
     * lists SVN handled file entries
     */
    protected function downloadFromList($listUrl, $destinationPath, $tempListingFile)
    {

        if( !$this->isDir( $destinationPath ) )
        {
            $this->createDir( $destinationPath );
        }

        if(is_null($tempListingFile)){
            $tempListingFile = $destinationPath . '/temporary_listing_cache.html';
        }
        
        if( !$this->isFile($tempListingFile) )
        {
            $this->downloadTo( $listUrl, $tempListingFile );
        }

        preg_match_all( '/^.*li.*href="(.*)".*$/m', file_get_contents( $tempListingFile ), $matches);
        
        if( empty($matches) || !empty($matches) && empty($matches[1]) )
        {
            throw new Exception("Cannot retrieve wordpress phpunit includes list", 20);
        }
        
        $files = array_filter($matches[1], function($el)
        {
            return !empty($el) && $el[0] !== '.';
        });
        
        foreach ($files as $file)
        {
            $this->downloadTo( $listUrl . $file, $destinationPath . $file );
        }

    }
    
    
}
