<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 22-May-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * Custom action class.
 */
class EcrProjectActionUpdateserver extends EcrProjectAction
{
    protected $type = 'updateserver';

    protected $name = 'Copy to updateserver';

    public $releaseState = 'release';

    public $abort = 0;

    /**
     * Get the input fields
     *
     * @param int $cnt A counter value.
     *
     * @return string
     */
    public function getFields($cnt)
    {
        $html = array();

        $html[] = '<label class="inline" for="fields_'.$cnt.'_releaseState">'.jgettext('Release state').'</label>';

        $html[] = EcrHtmlSelect::releaseStates(
            array(
                'name' => 'fields['.$cnt.'][releaseState]',
                'id' => 'fields_'.$cnt.'_releaseState',
                'selected' => $this->releaseState
            )
        );

        $html[] = '<br />';

        $html[] = $this->getLabel($cnt, 'abort', jgettext('Abort on failure'));
        $html[] = EcrHtmlSelect::yesno('fields['.$cnt.'][abort]', $this->abort);

        return implode("\n", $html);
    }

    /**
     * Perform the action.
     *
     * @param EcrProjectZiper $ziper
     *
     * @return \EcrProjectAction
     */
    public function run(EcrProjectZiper $ziper)
    {
        $project = EcrProjectHelper::getProject();

        $updateserver = new EcrProjectUpdateserver($project);

        $fileList = $ziper->getCreatedFiles();

        if(0 == count($fileList))
            return $this->abort('ERROR: No files to transfer', $ziper);

        $path = ECRPATH_UPDATESERVER.'/'.$project->comName.'/'.$this->releaseState;
        $UrlPath = ECRPATH_UPDATESERVER_URL.'/'.$project->comName.'/'.$this->releaseState;

        $urls = array();

        foreach($fileList as $f)
        {
            $dest = $path.'/'.JFile::getName($f);

            if(false == JFile::copy($f, $dest))
                return $this->abort(sprintf('ERROR: Can not copy the file %s to %s', $f, $dest), $ziper);

            $ziper->logger->log(sprintf('The file<br />%s<br />has been copied to<br />%s', $f, $dest));

            $urls[] = $UrlPath.'/'.JFile::getName($f);
        }

        $release = new EcrProjectUpdateserverRelease;

        $release->state = $this->releaseState;
        $release->downloads = $urls;
        $release->description = 'Bescreibung...';

        $updateserver->addRelease($release);

        return $this;
    }
}
