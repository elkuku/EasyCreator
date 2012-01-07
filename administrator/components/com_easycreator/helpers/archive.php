<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath
 * @author     Created on 18-Oct-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Creates archive files.
 *
 * @package    EasyCreator
 */
class EasyArchive
{
    /**
     * Creates a tgz archive.
     *
     * @param string $archive  The name of the archive
     * @param array $files The name of an array of files
     * @param string $compress The compression for the archive
     * @param string $removePath Path to remove within the archive
     *
     * @return Archive_Tar
     */
    public static function createTgz($archive, $files, $compress = 'tar', $removePath = '')
    {
        ecrLoadHelper('pear.archive.Tar');

        $tar = new Archive_Tar($archive, $compress);
        $tar->setErrorHandling(PEAR_ERROR_PRINT);
        $tar->createModify($files, '', $removePath);

        return $tar;
    }//function

    /**
     * Creates a ZIP package.
     *
     * @param string $fileName The zip file name
     * @param array $files Files to include in the archive
     * @param string $removePath Path to remove within the archive
     *
     * @return Archive_Zip
     */
    public static function createZip($fileName, $files, $removePath = '')
    {
        ecrLoadHelper('pear.archive.Zip');

        $archive = new Archive_Zip($fileName);

        return $archive->create($files, array('remove_path' => $removePath));
    }//function
}//class
