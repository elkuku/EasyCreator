<?php
/**
 * User: elkuku
 * Date: 08.06.12
 * Time: 08:33
 */

/**
 * Update server manager class.
 */
class EcrProjectUpdateserver
{
    /**
     * @var EcrProjectBase
     */
    private $project = null;

    private $localPath = '';

    private $serverUrl = '';

    private $serverTitle = '';

    /**
     * Constructor.
     *
     * @param EcrProjectBase $project
     *
     * @throws DomainException
     */
    public function __construct(EcrProjectBase $project)
    {
        $this->project = $project;

        $comParams = JComponentHelper::getComponent('com_easycreator')->params;

        $this->localPath = $comParams->get('local_updateserver_dir');
        $this->serverUrl = $comParams->get('updateserver_url');
        $this->serverTitle = $comParams->get('updateserver_title');

        $this->releaseUrl = ($comParams->get('updateserver_release_url')) ? : $this->serverUrl;
        $this->developmentUrl = ($comParams->get('updateserver_development_url')) ? : $this->serverUrl;

        //-- Check the base directory
        if(false == JFolder::exists(ECRPATH_UPDATESERVER))
        {
            if(false == JFolder::copy(JPATH_COMPONENT_ADMINISTRATOR.'/data/updateserver', ECRPATH_UPDATESERVER))
                throw new DomainException(sprintf('%s - Can not create the update server directory: %s'
                    , __METHOD__, ECRPATH_UPDATESERVER));

            EcrHtml::message(sprintf(
                jgettext('The update server directory has been created in: %s')
                , ECRPATH_UPDATESERVER));
        }

        //-- Check the extension directory
        $base = ECRPATH_UPDATESERVER.'/'.$this->project->comName;

        if(false == JFolder::exists($base))
        {
            if(false == JFolder::create($base))
                throw new DomainException(sprintf('%s - Can not create the extension update server directory: %s'
                    , __METHOD__, $base));

            EcrHtml::message(sprintf(
                jgettext('The update server extension directory has been created in: %s')
                , $base));

            JFolder::create($base.'/release');
            JFolder::create($base.'/development');

            JFile::copy(ECRPATH_UPDATESERVER.'/index.html', $base.'/index.html');
            JFile::copy(ECRPATH_UPDATESERVER.'/template_extension.html', $base.'/template.html');
            JFile::copy(ECRPATH_UPDATESERVER.'/updateserver.css', $base.'/updateserver.css');
            JFile::copy(ECRPATH_UPDATESERVER.'/favicon.ico', $base.'/favicon.ico');

            /* @var SimpleXMLElement $xml */
            $xml = EcrProjectHelper::getXML('<updates/>', false);
            $xml->addChild('name', $this->project->name);

            $buffer = $xml->asFormattedXML();

            //-- @todo: The file name "extension.xml" is the J! default - customize.
            JFile::write($base.'/extension.xml', $buffer);
            JFile::write($base.'/development.xml', $buffer);

            $feed = new EcrProjectFeed;
            $feed->title = $this->serverTitle.' - '.$this->project->name.' - Release Feed';
            $feed->link = $this->releaseUrl.'/release-feed.xml';
            $feed->id = $feed->link;
            $feed->author = 'XX-Author';
            $feed->updated = date(DATE_ATOM);

            $buffer = $feed->printPretty();

            JFile::write($base.'/release-feed.xml', $buffer);

            $feed->title = $this->serverTitle.' - '.$this->project->name.' - Development Feed';
            $feed->link = $this->developmentUrl.'/development-feed.xml';
            $feed->id = $feed->link;

            $buffer = $feed->printPretty();

            JFile::write($base.'/development-feed.xml', $buffer);

            //-- Copy the Joomla! manifest
            JFile::copy(
                $this->project->getJoomlaManifestPath().'/'.$this->project->getJoomlaManifestName()
                , $base.'/manifest.xml'
            );

            $this->update();
        }
    }

    /**
     * @throws DomainException
     */
    public function create()
    {
        $this->update();
    }

    /**
     * Update the update server information.
     */
    public function update()
    {
        $folders = JFolder::folders(ECRPATH_UPDATESERVER);
        $extensions = array();

        /* @var SimpleXMLElement $xml */
        $xml = EcrProjectHelper::getXML('<server/>', false);

        $xml->addChild('title', $this->serverTitle);
        $xml->addChild('url', $this->serverUrl);

        $extensionsElement = $xml->addChild('extensions');

        foreach($folders as $folder)
        {
            $xmlExt = EcrProjectHelper::getXML(ECRPATH_UPDATESERVER.'/'.$folder.'/manifest.xml');

            if($xmlExt)
            {
                $e = $extensionsElement->addChild('extension');
                $e->addChild('name', $xmlExt->name);
                $e->addChild('element', $folder);

                //@todo: more extension infos

                $extensions[$folder] = $xmlExt;
            }
        }

        $buffer = $xml->asFormattedXML();

        JFile::write(ECRPATH_UPDATESERVER.'/server.xml', $buffer);

        $this->updateExtensionIndex();

        $this->updateMainIndex($extensions);
    }

    /**
     * @param array $extensions
     */
    private function updateMainIndex(array $extensions)
    {
        $template = JFile::read(ECRPATH_UPDATESERVER.'/template.html');

        $html = array();

        $html[] = '<h2>Extensions</h2>';

        $html[] = '<ul>';

        foreach($extensions as $folder => $eXml)
        {
            $html[] = '<li><a href="'.$folder.'">'.$eXml->name.'</a></li>';
        }

        $html[] = '</ul>';

        $template = $this->replaceTemplate($template);

        $template = str_replace('<div id="ECR_EXTENSION_LIST"/>', implode("\n", $html), $template);

        if(ECR_DEBUG)
        {
            $debug = array();

            $path = 'server.xml';

            $debug[] = '<div class="path">'.$path.'</div>';

            if(JFile::exists(ECRPATH_UPDATESERVER.'/'.$path))
            {
                $xml = EcrProjectHelper::getXML(ECRPATH_UPDATESERVER.'/'.$path);

                $debug[] = '<pre class="code">'.htmlentities($xml->asFormattedXML()).'</pre>';
            }
            else
            {
                $debug[] = 'NOT FOUND<br />';
            }

            $replacement = '<div class="debug"><h3 class="debug">Debug</h3>'.implode("\n", $debug).'</div>';

            $template = str_replace('<div id="debug"/>', $replacement, $template);
        }

        JFile::write(ECRPATH_UPDATESERVER.'/index.html', $template);
    }

    /**
     * Update the extension index.html.
     */
    private function updateExtensionIndex()
    {
        $base = ECRPATH_UPDATESERVER.'/'.$this->project->comName;

        $template = JFile::read($base.'/template.html');

        $html = array();

        $folders = JFolder::folders($base);

        $cnt = 0;

        foreach($folders as $folder)
        {
            $files = JFolder::files($base.'/'.$folder);

            if(0 == count($files))
                continue;

            $cnt ++;

            $html[] = '<h2>'.ucfirst($folder).'</h2>';

            $html[] = '<a class="feedLink" href="'.$folder.'-feed.xml">RSS Feed</a>';

            $html[] = '<ul>';

            foreach($files as $file)
            {
                $html[] = '<li><a href="'.$folder.'/'.$file.'">'.$file.'</a></li>';
            }

            $html[] = '</ul>';
        }

        if(0 == $cnt)
            $html[] = '<p style="color: orange;">The server is empty :(</p>';

        $template = $this->replaceTemplate($template);

        $template = str_replace('<div id="ECR_EXTENSION_LIST"/>', implode("\n", $html), $template);

        //$replacement = '<div class="debug">'.implode("\n", $debug).'</div>';
        $replacement = $this->getDebug();

        $template = str_replace('<div id="debug"/>', $replacement, $template);

        JFile::write($base.'/index.html', $template);
    }

    /**
     * @param EcrProjectUpdateserverRelease $release
     *
     * @throws DomainException
     * @return \EcrProjectUpdateserver
     */
    public function addRelease(EcrProjectUpdateserverRelease $release)
    {
        $base = ECRPATH_UPDATESERVER.'/'.$this->project->comName;

        //-- Update the feed.xml
        $item = new EcrProjectFeedItem;

        $title = JFile::stripExt(JFile::getName($release->downloads[0]));

        $item->title = $title;
        $item->link = $release->downloads[0];

        $item->id = $release->downloads[0];

        $item->summary = $release->description;

        $item->summary = '<br /><b>Downloads</b>';

        foreach($release->downloads as $download)
        {
            $item->summary .= '<br /><a href="'.$download.'">'.JFile::getName($download).'</a>';
        }

        $item->content = $item->summary;

        $path = $base.'/'.$release->state.'-feed.xml';

        $feed = new EcrProjectFeed($path);
        $feed->addItem($item);

        $buffer = $feed->printPretty();

        JFile::write($path, $buffer);

        //-- Update the update.xml
        $fileName = ('release' == $release->state) ? 'extension.xml' : $release->state.'.xml';

        $path = $base.'/'.$fileName;

        if(false == JFile::exists($path))
            throw new DomainException(sprintf('%s - File not found: %s', __METHOD__, $path));

        $xml = EcrProjectHelper::getXML($path);

        $updateElement = $xml->addChild('update');

        $updateElement->addChild('name', $this->project->name);
        $updateElement->addChild('description', $this->project->description);
        $updateElement->addChild('element', $this->project->comName);
        $updateElement->addChild('type', $this->project->type);
        $updateElement->addChild('version', $this->project->version);
        $updateElement->addChild('infourl', $this->project->authorUrl);

        //-- Unsupported :(
        $updateElement->addChild('releasedate', date('d-M-Y'));

        $downloadsElement = $updateElement->addChild('downloads');

        foreach($release->downloads as $download)
        {
            $d = $downloadsElement->addChild('downloadurl', $download);
            $d->addAttribute('type', 'full');
            $d->addAttribute('format', JFile::getExt($download));
        }

        $tagsElement = $updateElement->addChild('tags', $this->project->name);
        $tagsElement->addChild('tag', $release->state);

        $updateElement->addChild('maintainer', $this->project->author);
        $updateElement->addChild('maintainerurl', $this->project->authorUrl);

        $targetElement = $updateElement->addChild('targetplatform');
        $targetElement->addAttribute('name', 'joomla');
        $targetElement->addAttribute('version', $this->project->JCompat);

        $buffer = $xml->asFormattedXML();

        JFile::write($path, $buffer);

        $this->updateExtensionIndex();

        return $this;
    }

    /**
     * Replace vqariables in template files.
     *
     * @param string $template
     *
     * @return string
     */
    private function replaceTemplate($template)
    {
        $template = str_replace('ECR_CHANNEL_TITLE', $this->serverTitle, $template);
        $template = str_replace('ECR_CHANNEL_URL', $this->serverUrl, $template);
        $template = str_replace('ECR_EXTENSION_TITLE', $this->project->name, $template);
        $template = str_replace('ECR_EXTENSION_NAME', $this->project->comName, $template);

        $template = str_replace('ECR_RELEASE_URL', $this->releaseUrl, $template);
        $template = str_replace('ECR_DEVELOPMENT_URL', $this->developmentUrl, $template);

        return $template;
    }

    /**
     * @return string
     */
    private function getDebug()
    {
        if(false == ECR_DEBUG)
            return '';

        $debug = array();

        $base = ECRPATH_UPDATESERVER.'/'.$this->project->comName;

        $debug[] = '<h3 class="debug">Debug</h3>';

        foreach(JFolder::folders($base) as $state)
        {
            $path = ('release' == $state) ? 'extension' : $state;

            $debug[] = '<div class="path">'.$path.'.xml</div>';

            if(JFile::exists($base.'/'.$path.'.xml'))
            {
                $xml = EcrProjectHelper::getXML($base.'/'.$path.'.xml');

                $debug[] = '<pre class="code">'.htmlentities($xml->asFormattedXML()).'</pre>';
            }
            else
            {
                $debug[] = 'NOT FOUND<br />';
            }

            $path = $state.'-feed';

            $debug[] = '<div class="path">'.$path.'.xml</div>';

            if(JFile::exists($base.'/'.$path.'.xml'))
            {
                $xml = EcrProjectHelper::getXML($base.'/'.$path.'.xml');

                $debug[] = '<pre class="code">'.htmlentities($xml->asFormattedXML()).'</pre>';
            }
            else
            {
                $debug[] = 'NOT FOUND<br />';
            }
        }

        return '<div class="debug">'.implode("\n", $debug).'</div>';
    }
}
