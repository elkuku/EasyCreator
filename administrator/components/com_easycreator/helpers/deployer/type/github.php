<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elkuku
 * Date: 24.04.12
 * Time: 11:43
 * To change this template use File | Settings | File Templates.
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

        if(! $downloads)
            return array();

        foreach($downloads as & $download)
        {
            $download->fileName = $download->id;
        }

        return $downloads;
    }

    /**
     * @static
     * @throws Exception
     * @return int
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
