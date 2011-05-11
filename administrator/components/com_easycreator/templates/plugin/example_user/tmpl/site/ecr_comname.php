<?php
##*HEADER*##

jimport('joomla.plugin.plugin');

/**
 * User Plugin.
 *
 * @package    _ECR_COM_NAME_
 * @subpackage Plugin
 */
class plgUser_ECR_COM_NAME_ extends JPlugin
{
    /**
     * Constructor
     *
     * For php4 compatability we must not use the __constructor as a constructor for plugins
     * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
     * This causes problems with cross-referencing necessary for the observer design pattern.
     *
     * @param object $subject The object to observe
     * @param 	array  $config  An array that holds the plugin configuration
     * @since 1.5
     */
    function plgUser_ECR_COM_NAME_(& $subject, $config)
    {
        parent::__construct($subject, $config);
    }//function

    /**
     * Example store user method
     *
     * Method is called before user data is stored in the database
     *
     * @param 	array		holds the old user data
     * @param 	boolean		true if a new user is stored
     */
    function onBeforeStoreUser($user, $isnew)
    {
    }//function

    /**
     * Example store user method
     *
     * Method is called after user data is stored in the database
     *
     * @param 	array		holds the new user data
     * @param 	boolean		true if a new user is stored
     * @param	boolean		true if user was succesfully stored in the database
     * @param	string		message
     */
    function onAfterStoreUser($user, $isnew, $success, $msg)
    {
        // convert the user parameters passed to the event
        // to a format the external application

        $args = array();
        $args['username']	= $user['username'];
        $args['email'] 		= $user['email'];
        $args['fullname']	= $user['name'];
        $args['password']	= $user['password'];

        if($isnew)
        {
            // Call a function in the external app to create the user
            // ThirdPartyApp::createUser($user['id'], $args);
        }
        else
        {
            // Call a function in the external app to update the user
            // ThirdPartyApp::updateUser($user['id'], $args);
        }
    }//function

    /**
     * Example store user method
     *
     * Method is called before user data is deleted from the database
     *
     * @param 	array		holds the user data
     */
    function onBeforeDeleteUser($user)
    {
    }//function

    /**
     * Example store user method
     *
     * Method is called after user data is deleted from the database
     *
     * @param 	array		holds the user data
     * @param	boolean		true if user was succesfully stored in the database
     * @param	string		message
     */
    function onAfterDeleteUser($user, $success, $msg)
    {
        // only the $user['id'] exists and carries valid information

        // Call a function in the external app to delete the user
        // ThirdPartyApp::deleteUser($user['id']);
    }//function

    /**
     * This method should handle any login logic and report back to the subject
     *
     * @access	public
     * @param 	array 	holds the user data
     * @param 	array    extra options
     * @return	boolean	True on success
     * @since	1.5
     */
    function onLoginUser($user, $options)
    {
        // Initialize variables
        $success = false;

        // Here you would do whatever you need for a login routine with the credentials
        //
        // Remember, this is not the authentication routine as that is done separately.
        // The most common use of this routine would be logging the user into a third party
        // application.
        //
        // In this example the boolean variable $success would be set to true
        // if the login routine succeeds

        // ThirdPartyApp::loginUser($user['username'], $user['password']);

        return $success;
    }//function

    /**
     * This method should handle any logout logic and report back to the subject
     *
     * @access public
     * @param array holds the user data
     * @return boolean True on success
     * @since 1.5
     */
    function onLogoutUser($user)
    {
        // Initialize variables
        $success = false;

        // Here you would do whatever you need for a logout routine with the credentials
        //
        // In this example the boolean variable $success would be set to true
        // if the logout routine succeeds

        // ThirdPartyApp::loginUser($user['username'], $user['password']);

        return $success;
    }//function
}//class
