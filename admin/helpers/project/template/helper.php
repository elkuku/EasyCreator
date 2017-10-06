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

                if(!$info)
                {
                    continue;
                }

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
     * @param string $basePath
     *
     * @since 0.0.1
     *
     * @return object|boolean stdClass Template info false on invalid template.
     */
    public static function getTemplateInfo($tplType, $tplName, $basePath = ECRPATH_EXTENSIONTEMPLATES)
    {
        if(false == JFile::exists($basePath.DS.$tplType.DS.$tplName.DS.'manifest.xml'))
        {
            return false;
        }

        $xml = EcrProjectHelper::getXML($basePath.DS.$tplType.DS.$tplName.DS.'manifest.xml');

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
     * Install templates.
     *
     * @since 0.0.1
     *
     * @throws Exception
     *
     * @return array with installs and errors.
     */
    public static function installPackage(array $package)
    {
        if($package['type'] != 'ecrextensiontemplate')
        {
            throw new Exception(jgettext('This is not an EasyCreator Extension Template'));
        }

        $result = array();
        $types = (JFolder::folders($package['extractdir']));

        foreach($types as $type)
        {
            JFolder::create(ECRPATH_EXTENSIONTEMPLATES.DS.$type);

            $templates = JFolder::folders($package['extractdir'].DS.$type);

            foreach($templates as $template)
            {
                //-- Check for previous install - no upgrade yet..
                if(JFolder::exists(ECRPATH_EXTENSIONTEMPLATES.DS.$type.DS.$template))
                {
                    $compare = self::compareVersions($type, $template, $package['extractdir']);

                    switch ($compare) {
                        case -1:
                            // Installed is lower
                            $result['installs'][] = "Updated: $type - $template";
                            break;
                        case 0:
                            // Same version
                            $result['errors'][] = "Same version: $type - $template";
                            continue 2;
                            break;
                        case 1:
                            // Uploaded is lower
                            $result['errors'][] = "Installed is newer: $type - $template";
                            continue 2;
                            break;
                    }
                }
                else
                {
                    $result['installs'][] = "Installed: $type - $template";
                }

                //-- Create template dir
                JFolder::create(ECRPATH_EXTENSIONTEMPLATES.DS.$type.DS.$template);

                //-- Create the folders
                $folders = JFolder::folders($package['extractdir'].DS.$type.DS.$template, '.', true, true);

                foreach($folders as $folder)
                {
                    $s = str_replace($package['extractdir'].DS.$type.DS.$template.DS, '', $folder);

                    if(false == JFolder::create(ECRPATH_EXTENSIONTEMPLATES.DS.$type.DS.$template.DS.$s))
                    {
                        throw new Exception(sprintf(jgettext('Can not create folder %s'), $folder));
                    }
                }

                //-- Copy the files
                $files = JFolder::files($package['extractdir'].DS.$type.DS.$template, '.', true, true);

                foreach($files as $file)
                {
                    $s = str_replace($package['extractdir'].DS.$type.DS.$template.DS, '', $file);

                    if(false == JFile::copy($file, ECRPATH_EXTENSIONTEMPLATES.DS.$type.DS.$template.DS.$s))
                    {
                        throw new Exception(jgettext('Can not copy file %s', $s));
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Export templates to a tar.gz package.
     *
     * @param array $exports Index array of templates to export
     * @param string $zipName
     *
     * @return string The name of the archive created.
     * @throws Exception
     * @since 0.0.1
     *
     */
    public static function exportTemplates($exports, $zipName = '')
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

        $xml = new SimpleXMLElement('<extension type="ecrextensiontemplate" version="'.ECR_VERSION.'"/>');

        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->formatOutput = true;

        $domnode = dom_import_simplexml($xml);
        $domnode = $doc->importNode($domnode, true);
        $domnode = $doc->appendChild($domnode);

        $result = $doc->saveXML();

        if(false == JFile::write($tempDir.DS.'manifest.xml', $result))
            throw new Exception(sprintf(jgettext('Unable to write file %s'), $tempDir.DS.'manifest.xml'));

        $files[] = $tempDir.DS.'manifest.xml';

        $fileName = $zipName ? : 'ecr_extension_templates'.date('Ymd_His');
        $fileName .= '.zip';

        if( ! JFolder::create(ECRPATH_EXPORTS.DS.'templates'))
            throw new Exception(sprintf(jgettext('Unable to create the folder %s'), ECRPATH_EXPORTS.DS.'templates'));

        $result = EcrArchive::createZip(ECRPATH_EXPORTS.DS.'templates'.DS.$fileName, $files, $tempDir);

        //-- This means error
        if( ! $result->listContent())
            throw new Exception(jgettext('Error creating archive'));

        return $fileName;
    }

    /**
     * Upload and unpack a package file.
     *
     * @since 0.0.1
     *
     * @throws Exception
     * @return mixed array the package on success | boolean false on error
     */
    public static function installPackageFromUpload()
    {
        //-- Get the uploaded file information
        $userfile = JFactory::getApplication()->input->files->get('install_package', null, 'raw');

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
        JFile::upload($tmp_src, $tmp_dest, false, true);

        //-- Unpack the downloaded package file
        $package = JInstallerHelper::unpack($tmp_dest);

        if(false == $package)
            throw new Exception(jgettext('Unable to find install package'));

        return self::installPackage($package);
    }

    /**
     * Install a package from a WEB repository.
     *
     * @param $url
     *
     * @since 0.0.25.6
     *
     * @throws Exception
     *
     * @return array
     */
    public static function installPackageFromWeb($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $data = curl_exec ($ch);
        $error = curl_error($ch);

        curl_close ($ch);

        if ($error)
        {
            throw new Exception($error);
        }

        $destination = JPATH_ROOT . '/tmp/' . substr($url, strrpos($url, '/') + 1, strlen($url));
        $file = fopen($destination, "w+");
        fputs($file, $data);
        fclose($file);

        //-- Unpack the downloaded package file
        $package = JInstallerHelper::unpack($destination);

        if(false == $package)
        {
            throw new Exception(jgettext('Unable to find install package'));
        }

        return self::installPackage($package);
    }

    /**
     * Get extended replacement information.
     *
     * @since 0.0.1
     *
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

    private static function compareVersions($type, $template, $basePath2)
    {
        $info1 = self::getTemplateInfo($type, $template);
        $info2 = self::getTemplateInfo($type, $template, $basePath2);

        return version_compare($info1->version, $info2->version);
    }
}
