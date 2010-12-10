<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 07-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

jimport('joomla.application.component.view');

/**
 * Enter description here ...@todo class comment.
 *
 */
class EasyCreatorViewZiper extends JView
{
    /**
     * Standard display method.
     *
     * @param string $tpl The name of the template file to parse;
     *
     * @return void
     */
    public function display($tpl = null)
    {
        ecrScript('ziper');

        $task = JRequest::getCmd('task');
        $this->ecr_project = JRequest::getCmd('ecr_project');

        //--Get the project
        try
       {
            $this->project = EasyProjectHelper::getProject();
        }
        catch(Exception $e)
        {
            $m =(JDEBUG || ECR_DEBUG) ? nl2br($e) : $e->getMessage();

            ecrHTML::displayMessage($m, 'error');

            ecrHTML::easyFormEnd();

            return;
        }//try

        //-- Draw h1 header
        ecrHTML::header(jgettext('Component ZIPer'), $this->project);

        $this->zip_dir_path = ECRPATH_BUILDS.DS.$this->ecr_project;

        if( ! is_dir($this->zip_dir_path))
        {
            //--no zip dir - no zip files..
            $this->zip_dir_path = false;
        }

        if(in_array($task, get_class_methods($this)))
        {
            //--Execute the task
            $this->$task();
        }

        parent::display($tpl);

        ecrHTML::easyFormEnd();
    }//function

    /**
     * Zipper view.
     *
     * @return void
     */
    private function ziper()
    {
        $this->setLayout('ziper');
    }//function

    /**
     * Zips the project.
     *
     * @return void
     */
    private function ziperzip()
    {
        ecrLoadHelper('ziper');

        $this->buildopts = JRequest::getVar('buildopts', array());

        $this->EasyZiper = new EasyZIPer();

        $this->setLayout('ziperresult');
    }//function

    /**
     * Deletes a zip file.
     *
     * @return void
     */
    private function delete()
    {
        $this->setLayout('ziper');
    }//function

    /**
     * Draw the archive.
     *
     * @todo hell... move this SH*T
     *
     * @return string
     */
    protected function drawArchive()
    {
        echo '<h1>'.jgettext('Archive').'</h1>';

        $this->zip_dir_path = ECRPATH_BUILDS.DS.$this->project->comName;
#var_dump($this->project);
        if( ! JFolder::exists($this->zip_dir_path))
        {
            ecrHTML::displayMessage(jgettext('Archive is empty'));

            $this->zip_dir_path = false;

            return '';
        }

        $folders = JFolder::folders($this->zip_dir_path);
        $base_href = JURI::Root().'administrator/components/com_easycreator/builds/'.$this->project->comName;

        rsort($folders);

        foreach($folders as $folder)
        {
            echo '<div style="background-color: #B2CCE5; font-size: 1.3em; font-weight: bold; padding-left: 1em;">';
            echo $this->project->comName.' '.$folder;
            echo '</div>';

            $base_path = $this->zip_dir_path.DS.$folder;
            $files = JFolder::files($base_path.DS);

            if( ! count($files))
            {
                echo '<strong style="color: red;">'.jgettext('No ZIP files found').'</strong>';

                continue;
            }
            ?>
            <div id="ajaxMessage"></div>
            <div id="ajaxDebug"></div>
<table class="adminlist" cellspacing="5">
	<tbody>
		<tr style="background-color: #eee;">
			<th><?php echo jgettext('File'); ?></th>
			<th><?php echo jgettext('Modified'); ?></th>
			<th><?php echo jgettext('Size'); ?></th>
			<th colspan="2" align="center"><?php echo jgettext('Action'); ?></th>
		</tr>
		<?php
        $k = 0;

        foreach($files as $file)
        {
            $info = lstat($base_path.DS.$file);
            $date = JFactory::getDate($info[9]);
            $href = $base_href.'/'.$folder.'/'.$file;
            $fsize = $info[7];

            $js_delete = '';
            $js_delete .= " document.adminForm.file_name.value='".$file."';";
            $p = str_replace(JPATH_ROOT.DS, '', $base_path);
            $p = str_replace('\\', '/', $p);
            $js_delete .= " document.adminForm.file_path.value='".$p."';";
            $js_delete .= " submitbutton('delete');";

            $js_delete = ' onclick="'.$js_delete.'"';

            ?>
		<tr id="row<?php echo $file; ?>"
		class="<?php echo 'row'.$k; ?>">
			<td><?php echo $file; ?></td>
			<td><?php echo $date->toFormat(); ?></td>
			<td><?php echo ecrHTML::byte_convert($fsize); ?></td>
			<td width="2%"><a href="<?php echo $href; ?>"
				style="padding-left: 20px; height: 14px;"
				class="ecr_button img icon-16-save hasEasyTip"
				title="<?php echo jgettext('Download'); ?>::"> </a></td>
			<td width="2%">
			<div style="padding-left: 20px; height: 14px;"
				class="ecr_button img icon-16-delete hasEasyTip"
				title="<?php echo jgettext('Delete'); ?>::"
				onclick="deleteZipFile(<?php echo "'$p', '$file'"?>);"></div>
			</td>
		</tr>
		<?php
        $k = 1 - $k;
        }//foreach
        ?>
	</tbody>
</table>
		<?php
        }//foreach
    }//function
}//class
