<?php
##*HEADER*##

/**
 * _ECR_COM_NAME_ Main installer
 */
function com_install()
{
    echo '<h2>'.JText::sprintf('%s Installer', '_ECR_COM_NAME_').'</h2>';
##ECR_MD5CHECK##

    /*
     * Custom install function
     *
     * If something goes wrong..
     */

    // return false;

    /*
     * otherwise...
     */

    return true;
}//function
##ECR_MD5CHECK_FNC##
