<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 07-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * Provides operation on files.
 */
class EcrFile extends JFile
{
    /**
     * Save a file.
     *
     * File name and path set from request.
     *
     * @throws Exception
     * @return string - 'saved' on success / error string
     */
    public static function saveFile()
    {
        $input = JFactory::getApplication()->input;

        $file_path = $input->getPath('file_path', NULL);
        $file_name = $input->getPath('file_name', NULL);
        $insertstring = $input->get('c_insertstring', '', 'raw');

        if( ! $file_path || ! $file_name)
            throw new Exception(jgettext('Empty values in save'));

        if( ! $insertstring)
            throw new Exception(jgettext('Empty content'));

        $file_path = JPath::clean(JPATH_ROOT.DS.$file_path);

        /*
         * as for now.. the file must exist for save !
         */
        if( ! self::exists($file_path.DS.$file_name))
            throw new Exception(jgettext('The file must exist for save'));

        if( ! self::write($file_path.DS.$file_name, $insertstring))
            throw new Exception(sprintf(jgettext('The file %s could NOT be saved at PATH: %s'), $file_name, $file_path));

        return true;
    }

    /**
     * Saves a backup of a file apending a postfix .rXX .
     *
     * @param string $fileName The file name to version
     *
     * @return bool true on success
     */
    public static function saveVersion($fileName)
    {
        if( ! self::exists($fileName))
        {
            JFactory::getApplication()->enqueueMessage(jgettext('File not found'), 'error');

            return false;
        }

        $r = 1;

        $found = false;
        $versionedFileName = $fileName;

        while( ! $found)
        {
            $versionedFileName = $fileName.'.r'.$r;

            if( ! self::exists($versionedFileName))
            {
                $found = true;
            }
            else
            {
                $r ++;
            }
        }

        if( ! self::copy($fileName, $versionedFileName))
        {
            JFactory::getApplication()->enqueueMessage(
                sprintf(jgettext('Unable to copy file %s'), $fileName), 'error');

            return false;
        }

        return true;
    }

    /**
     * Delete a file.
     *
     * File name and path set from request.
     *
     * @throws Exception
     * @return string Message.
     */
    public static function deleteFile()
    {
        $input = JFactory::getApplication()->input;

        $file_path = $input->getPath('file_path');
        $file_name = $input->getPath('file_name');

        $file_path = JPath::clean(JPATH_ROOT.DS.$file_path);

        if( ! self::exists($file_path.DS.$file_name))
            throw new Exception(jgettext('The file does not exist'));

        if( ! self::delete($file_path.DS.$file_name))
            throw new Exception(sprintf(jgettext('The file %s could not be deleted at path: %s'), $file_name, $file_path));

        return true;
    }
}
