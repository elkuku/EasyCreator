<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath
 * @author     Created on 17-Oct-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * EasyCreator template helper.
 *
 * @package    EasyCreator
 */
class EcrProjectTemplateHelper
{
    /**
     * Gets a list of installed templates.
     *
     * @return array Indexed array with template type as key ans folder name as value
     */
    public static function getTemplateList()
    {
        static $list = array();

        if(count($list))
            return $list;

        $types = JFolder::folders(ECRPATH_EXTENSIONTEMPLATES);

        foreach($types as $tplType)
        {
            if($tplType == 'parts'
                || $tplType == 'std'
                || $tplType == 'autocodes'
            )
                continue;

            $templates = JFolder::folders(ECRPATH_EXTENSIONTEMPLATES.DS.$tplType);

            foreach($templates as $tplName)
            {
                $info = self::getTemplateInfo($tplType, $tplName);

                if(false == $info)
                    continue;

                $list[$tplType][$info->folder] = $info;
            }
        }

        return $list;
    }

    /**
     * Gets Information about a specific template.
     *
     * @param string $tplType Template type
     * @param string $tplName Template name
     *
     * @return object stdClass Template info.
     */
    public static function getTemplateInfo($tplType, $tplName)
    {
        if(false == JFile::exists(ECRPATH_EXTENSIONTEMPLATES.DS.$tplType.DS.$tplName.DS.'manifest.xml'))
            return false;

        $xml = EcrProjectHelper::getXML(ECRPATH_EXTENSIONTEMPLATES.DS.$tplType.DS.$tplName.DS.'manifest.xml');

        $info = new stdClass;

        $info->folder = $tplName;
        $info->name = (string)$xml->name;
        $info->description = jgettext((string)$xml->description);
        $info->version = (string)$xml->version;
        $info->jVersion = (string)$xml->jVersion;
        $info->phpVersion = (string)$xml->phpVersion;
        $info->dbTables = (string)$xml->dbTables;
        $info->author = (string)$xml->author;
        $info->authorUrl = (string)$xml->authorUrl;
        $info->complements = array();

        if(isset($xml->complements->complement))
        {
            foreach($xml->complements->complement as $complement)
            {
                $c = new stdClass;

                $c->folder = (string)$complement->folder;
                $c->version = (string)$complement->version;
                $c->targetDir = (string)$complement->targetDir;

                $info->complements[] = $c;
            }
        }

        $info->info = '';
        $info->info .= jgettext(ucfirst($tplType)).' '.$info->name.' '.$info->version.'::'.$info->description;
        $info->info .= ($info->author) ? '<br /><span style=\'color: blue;\'>Author:</span> '.$info->author : '';
        $info->info .= '<br /><strong>Joomla!:</strong> '.$info->jVersion;
        $info->info .= '<br /><strong>PHP:</strong> '.$info->phpVersion;
        $info->info .= ($info->dbTables)
            ? '<br /><span style=\'color: orange;\'>dbTables:</span> '.$info->dbTables
            : '';
        $info->info .= '<br />ECR Folder: '.$info->folder;

        return $info;
    }

    /**
     * Export templates to a tar.gz package.
     *
     * @param array $exports Index array of templates to export
     *
     * @throws Exception
     * @return boolean true on success
     */
    public static function exportTemplates($exports)
    {
        $tempDir = JFactory::getConfig()->get('tmp_path').DS.uniqid('templateexport');

        $files = array();

        foreach($exports as $type => $folders)
        {
            foreach($folders as $folder)
            {
                $fileList = JFolder::files(ECRPATH_EXTENSIONTEMPLATES.DS.$type.DS.$folder, '.', true, true);

                foreach($fileList as $path)
                {
                    $path = str_replace(ECRPATH_EXTENSIONTEMPLATES.DS, '', $path);

                    if(false == JFolder::exists(dirname($tempDir.DS.$path)))
                        JFolder::create(dirname($tempDir.DS.$path));

                    if(false == JFile::copy(ECRPATH_EXTENSIONTEMPLATES.DS.$path, $tempDir.DS.$path))
                        throw new Exception(sprintf(jgettext('Unable to copy the file %s to %s')
                            , ECRPATH_EXTENSIONTEMPLATES.DS.$path, $tempDir.DS.$path));

                    $files[] = $tempDir.DS.$path;
                }
            }
        }

        $xml = new SimpleXMLElement('<install type="ecrextensiontemplate" version="'.ECR_VERSION.'"/>');

        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->formatOutput = true;

        $domnode = dom_import_simplexml($xml);
        $domnode = $doc->importNode($domnode, true);
        $domnode = $doc->appendChild($domnode);

        $result = $doc->saveXML();

        if(false == JFile::write($tempDir.DS.'manifest.xml', $result))
            throw new Exception(sprintf(jgettext('Unable to write file %s'), $tempDir.DS.'manifest.xml'));

        $files[] = $tempDir.DS.'manifest.xml';

        $fileName = 'ecr_extension_templates'.date('Ymd_His').'.tar.gz';

        if( ! JFolder::create(ECRPATH_EXPORTS.DS.'templates'))
            throw new Exception(sprintf(jgettext('Unable to create the folder %s'), ECRPATH_EXPORTS.DS.'templates'));

        $result = EcrArchive::createTgz(ECRPATH_EXPORTS.DS.'templates'.DS.$fileName, $files, 'gz', $tempDir);

        //-- This means error
        if( ! $result->listContent())
            throw new Exception(jgettext('Error creating archive'));

        return true;
    }

    /**
     * Install templates.
     *
     * @throws Exception
     * @return boolean true on success
     */
    public static function installTemplates()
    {
        jimport('joomla.installer.helper');

        $package = self::getPackageFromUpload();

        if(false == $package)
            throw new Exception(jgettext('Unable to find install package'));

        if($package['type'] != 'ecrextensiontemplate')
            throw new Exception(jgettext('This is not an EasyCreator Extension Template'));

        $types = (JFolder::folders($package['extractdir']));

        foreach($types as $type)
        {
            JFolder::create(ECRPATH_EXTENSIONTEMPLATES.DS.$type);
            $templates = JFolder::folders($package['extractdir'].DS.$type);

            foreach($templates as $template)
            {
                //-- Check for previous install - no upgrade yet..
                if(JFolder::exists(ECRPATH_EXTENSIONTEMPLATES.DS.$type.DS.$template))
                    throw new Exception(sprintf(jgettext('The template %s is already installed'), $type.' - '.$template));

                //-- Create template dir
                JFolder::create(ECRPATH_EXTENSIONTEMPLATES.DS.$type.DS.$template);

                //-- Create the folders
                $folders = JFolder::folders($package['extractdir'].DS.$type.DS.$template, '.', true, true);

                foreach($folders as $folder)
                {
                    $s = str_replace($package['extractdir'].DS.$type.DS.$template.DS, '', $folder);

                    if(false == JFolder::create(ECRPATH_EXTENSIONTEMPLATES.DS.$type.DS.$template.DS.$s))
                        throw new Exception(sprintf(jgettext('Can not create folder %s'), $folder));
                }

                //-- Copy the files
                $files = JFolder::files($package['extractdir'].DS.$type.DS.$template, '.', true, true);

                foreach($files as $file)
                {
                    $s = str_replace($package['extractdir'].DS.$type.DS.$template.DS, '', $file);

                    if(false == JFile::copy($file, ECRPATH_EXTENSIONTEMPLATES.DS.$type.DS.$template.DS.$s))
                        throw new Exception(jgettext('Can not copy file %s', $s));
                }
            }
        }

        return true;
    }

    /**
     * Upload and unpack a package file.
     *
     * @throws Exception
     * @return mixed array the package on success | boolean false on error
     */
    private static function getPackageFromUpload()
    {
        //-- Get the uploaded file information
        $userfile = JFactory::getApplication()->input->files->get('install_package', null, 'array');

        //-- If there is no uploaded file, we have a problem...
        if(false == is_array($userfile))
            throw new Exception(jgettext('No file selected'));

        //-- Check if there was a problem uploading the file.
        if($userfile['error'] || $userfile['size'] < 1)
            throw new Exception(jgettext('Invalid package'));

        //-- Build the appropriate paths
        $tmp_src = $userfile['tmp_name'];
        $tmp_dest = JFactory::getConfig()->get('tmp_path').DS.$userfile['name'];

        //-- Move uploaded file
        JFile::upload($tmp_src, $tmp_dest);

        //-- Unpack the downloaded package file
        $package = JInstallerHelper::unpack($tmp_dest);

        return $package;
    }

    /**
     * Get extended replacement information.
     *
     * @static
     * @return array
     */
    public static function getReplacementInfo()
    {
        $reflector = new ReflectionClass('EcrProjectReplacement');

        $info = array();

        $blacks = array('customs', 'priorities');

        foreach($reflector->getProperties() as $property)
        {
            if(in_array((string)$property->getName(), $blacks))
                continue;

            $comment = $property->getDocComment();

            $comment = str_replace('@var string', '', $comment);
            $comment = trim($comment, '/*\n ');
            $comment = trim($comment);
            $comment = trim($comment, '* ');

            $info[$property->getName()] = $comment;
        }

        return $info;
    }
}//class
