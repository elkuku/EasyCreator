<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 25-Apr-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- @Joomla!-compat 2.5
jimport('joomla.client.ftp');

/**
 * FTP class.
 */
class EcrFtp extends JClientFtp
{
    /**
     * Returns the global FTP connector object, only creating it
     * if it doesn't already exist.
     *
     * You may optionally specify a username and password in the parameters. If you do so,
     * you may not login() again with different credentials using the same object.
     * If you do not use this option, you must quit() the current connection when you
     * are done, to free it for use by others.
     *
     * @param   string  $host     Host to connect to
     * @param   string  $port     Port to connect to
     * @param   array   $options  Array with any of these options:
     *                            type=>[FTP_AUTOASCII|FTP_ASCII|FTP_BINARY], timeout=>(int)
     * @param   string  $user     Username to use for a connection
     * @param   string  $pass     Password to use for a connection
     *
     * @return  JFTP    The FTP Client object.
     *
     * @since   11.1
     */
    public static function getClient($host = '127.0.0.1', $port = '21', $options = null, $user = null, $pass = null)
    {
        //-- Avoid all this mess by declaring JFtp::getInstance() as "static" !!

        $options = array();

        $x = new JClientFtp($options);

        return $x->getInstance($host, $port, $options, $user, $pass);
    }

    /**
     * Method to store a file to the FTP server
     *
     * @param   string  $local   Path to local file to store on the FTP server
     * @param   string  $remote  FTP path to file to create
     *
     * @throws Exception
     *
     * @return  boolean  True if successful
     */
    public function store($local, $remote = null)
    {
        //-- Avoid all this mess by throwing appropriate exceptions !!

        if(false === parent::store($local, $remote))
            throw new Exception(JError::getError());
    }
}
