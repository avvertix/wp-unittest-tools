<?php namespace WpUnitTools\Traits;

use Symfony\Component\Console\Input\ArrayInput;

/**
 * Call other console commands 
 */
trait InteractWithCommands
{
    
    
    function call( $output, $command_name, array $parameters = [], array $options = [] ){
        
        $command = $this->getApplication()->find( $command_name );

        $arguments = array_merge( array(
            'command' => $command_name
        ), $parameters, $options );

        $greetInput = new ArrayInput($arguments);
        $returnCode = $command->run($greetInput, $output);
        
        return $returnCode;
    }
    
    
    
}
