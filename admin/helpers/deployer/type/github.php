<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 25-Apr-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * GitHub deployer class.
 */
class EcrDeployerTypeGithub extends EcrDeployer
{
    /**
     * @static
     * @return array|mixed
     * @throws Exception
     */
    public function getPackageList()
    {
        $input = JFactory::getApplication()->input;

        JLog::add('| << '.jgettext('Obtaining download list ...'));

        $downloads = $this->github->downloads->getList($input->get('owner'), $input->get('repo'));

        if( ! $downloads)
            return array();

        foreach($downloads as & $download)
        {
            $download->fileName = $download->id;
        }

        return $downloads;
    }

    /**
     * @static
     * @return int|mixed
     */
    public function deployPackage()
    {
        $input = JFactory::getApplication()->input;

        $files = $input->get('file', array(), 'array');

        foreach($files as $file)
        {
            JLog::add('| >> '.sprintf(jgettext('Uploading %s ...'), JFile::getName($file)));

            $this->github->downloads->add($input->get('owner'), $input->get('repo'), $file);
        }

        return count($files);
    }

    /**
     * @static
     * @throws Exception
     * @return mixed|void
     */
    public function deployFiles()
    {
        throw new Exception(__METHOD__.' - This method is not supported in the adapter');
    }

    /**
     * @static
     * @return mixed
     * @throws Exception
     */
    public function deletePackage()
    {
        $input = JFactory::getApplication()->input;

        $id = $input->getInt('id');

        JLog::add('| -- '.sprintf('Deleting %s ...', $id));

        $this->github->downloads->delete($input->get('owner'), $input->get('repo'), $id);

        return;
    }

    /**
     * @throws Exception
     * @return mixed|void
     */
    protected function connect()
    {
        $credentials = new stdClass;

        /* @var JInput $input */
        $input = JFactory::getApplication()->input;

        $credentials->user = $input->get('user');
        $credentials->pass = $input->get('pass');

        $config = new JRegistry;

        $config->set('api.username', $credentials->user);
        $config->set('api.password', $credentials->pass);

        JLog::add('| ^^ '.sprintf(jgettext('Connecting to %s ...'), 'GitHub'));

        $this->github = new EcrGithub($config);

        $this->credentials = $credentials;
    }
}
