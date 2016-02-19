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
    
    
}
