<?php
##*HEADER*##

jimport('joomla.plugin.plugin');

/**
 * Authentication Plugin.
 *
 * @package    _ECR_COM_NAME_
 * @subpackage Plugin
 */
class plgAuthentication_ECR_COM_NAME_ extends JPlugin
{
    /**
     * Constructor.
     *
     * For php4 compatability we must not use the __constructor as a constructor for plugins
     * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
     * This causes problems with cross-referencing necessary for the observer design pattern.
     *
     * @param	object	$subject	The object to observe
     * @param	array	$config		An array that holds the plugin configuration
     * @since	1.5
     */
    function plgAuthentication_ECR_COM_NAME_(& $subject, $config)
    {
        parent::__construct($subject, $config);
    }//function

    /**
     * This method should handle any authentication and report back to the subject
     *
     * @access	public
     * @param	array	$credentials	Array holding the user credentials
     * @param	array	$options		Array of extra options
     * @param	object	$response		Authentication response object
     * @return	boolean
     * @since	1.5
     */
    function onAuthenticate($credentials, $options, &$response)
    {
        /*
         * Here you would do whatever you need for an authentication routine with the credentials
         *
         * In this example the mixed variable $return would be set to false
         * if the authentication routine fails or an integer userid of the authenticated
         * user if the routine passes
         */
        $success = true;

        if($success)
        {
            $response->status			= JAUTHENTICATE_STATUS_SUCCESS;
            $response->error_message	= '';
            // You may also define other variables:
            /*
            $yourUser					= YourClass::getUser( $credentials );
            $response->email			= $yourUser->email;
            $response->fullname			= $yourUser->name;
            */
            return true;
        }
        else
        {
            $response->status			= JAUTHENTICATE_STATUS_FAILURE;
            $response->error_message	= 'Could not authenticate';

            return false;
        }
    }//function
}//class
