<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage ProjectTypes
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 29-Mar-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * EasyCreator project type package.
 */
class EasyProjectPackage extends EasyProject
{
    /**
     * Project type.
     *
     * @var string
     */
    public $type = 'package';

    /**
     * Project prefix.
     *
     * @var string
     */
    public $prefix = 'pkg_';

    /**
     * Project elements.
     *
     * @var string
     */
    public $elements = array();

    /**
     * Get the path for the Joomla! XML manifest file.
     *
     * @return string
     */
    public function getJoomlaManifestPath()
    {
        return JPATH_MANIFESTS.DS.'packages';
    }//function

    /**
     * Get a Joomla! manifest XML file name.
     *
     * @return string File name.
     */
    public function getJoomlaManifestName()
    {
        return $this->comName.'.xml';
    }//function

    /**
     * Get a file name for a EasyCreator setup XML file.
     *
     * @return string
     */
    public function getEcrXmlFileName()
    {
        return $this->comName.'.xml';
    }//function

    /**
     * Get a common file name.
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->comName;
    }//function

    /**
     * Find all files and folders belonging to the project.
     *
     * @todo changes in 1.6
     *
     * @return array
     */
    public function findCopies()
    {
        return array();
    }//function
}//class
