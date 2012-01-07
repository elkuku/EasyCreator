<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 07-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Provides operation on files.
 */
class EasyFile
{
    /**
     * Save a file.
     *
     * File name and path set from request.
     *
     * @return string - 'saved' on success / error string
     */
    public static function saveFile()
    {
        $file_path = JRequest::getVar('file_path', NULL);
        $file_name = JRequest::getVar('file_name', NULL);
        $insertstring = JRequest::getVar('c_insertstring', '', 'post', 'string', JREQUEST_ALLOWRAW);

        if( ! $file_path || ! $file_name)
        throw new Exception(jgettext('Empty values in save'));

        if( ! $insertstring)
        throw new Exception(jgettext('Empty content'));

        $file_path = JPath::clean(JPATH_ROOT.DS.$file_path);

        /*
         * as for now.. the file must exist for save !
         */
        if( ! JFile::exists($file_path.DS.$file_name))
        throw new Exception(jgettext('The file must exist for save'));

        if( ! JFile::write($file_path.DS.$file_name, $insertstring))
        throw new Exception(sprintf(jgettext('The file %s could NOT be saved at PATH: %s'), $file_name, $file_path));

        return true;
    }//function

    /**
     * Saves a backup of a file apending a postfix .rXX .
     *
     * @param string $fileName The file name to version
     *
     * @return bool true on success
     */
    public static function saveVersion($fileName)
    {
        if( ! JFile::exists($fileName))
        {
            JError::raiseWarning(100, jgettext('File not found'));

            return false;
        }

        $r = 1;

        $found = false;

        while( ! $found)
        {
            $versionedFileName = $fileName.'.r'.$r;

            if( ! JFile::exists($versionedFileName))
            {
                $found = true;
            }
            else
            {
                $r++;
            }
        }//while

        if( ! JFile::copy($fileName, $versionedFileName))
        {
            JError::raiseWarning(100, sprintf(jgettext('Unable to copy file %s'), $fileName));

            return false;
        }

        return true;
    }//function

    /**
     * Delete a file.
     *
     * File name and path set from request.
     *
     * @return string Message.
     */
    public static function deleteFile()
    {
        $file_path = JRequest::getVar('file_path');
        $file_name = JRequest::getVar('file_name');

        $file_path = JPath::clean(JPATH_ROOT.DS.$file_path);

        if( ! JFile::exists($file_path.DS.$file_name))
        throw new Exception(jgettext('The file does not exist'));

        if( ! JFile::delete($file_path.DS.$file_name))
        throw new Exception(sprintf(jgettext('The file %s could not be deleted at path: %s'), $file_name, $file_path));

        return true;
    }//function
}//class
