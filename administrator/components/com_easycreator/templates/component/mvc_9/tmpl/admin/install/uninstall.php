<?php
##*HEADER*##

/**
 * The main uninstaller function
 */
function com_uninstall()
{
    echo '<h2>'.JText::sprintf('%s Uninstaller', '_ECR_COM_NAME_');

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
