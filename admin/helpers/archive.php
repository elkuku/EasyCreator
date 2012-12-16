<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath
 * @author     Created on 18-Oct-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * Creates archive files.
 *
 * @package    EasyCreator
 */
class EcrArchive
{
    /**
     * Creates a tgz archive.
     *
     * @param string $archive  The name of the archive
     * @param array $files The name of an array of files
     * @param string $compress The compression for the archive
     * @param string $removePath Path to remove within the archive
     *
     * @return EcrPearArchiveTar
     */
    public static function createTgz($archive, $files, $compress = 'tar', $removePath = '')
    {
        $tar = new EcrPearArchiveTar($archive, $compress);
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
     * @return EcrPearArchiveZip
     */
    public static function createZip($fileName, $files, $removePath = '')
    {
        $archive = new EcrPearArchiveZip($fileName);

        return $archive->create($files, array('remove_path' => $removePath));
    }//function
}//class
