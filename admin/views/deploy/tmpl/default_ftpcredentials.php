<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 25-Apr-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/* @var JRegistry $options */
$options = $this->project->deployOptions;
?>


<fieldset class="adminform">

    <legend><?php echo jgettext('Credentials'); ?></legend>

    <label for="ftpHost"><?php echo jgettext('Server'); ?></label>
    <input type="text" id="ftpHost" value="<?php echo $options->get('ftp.host'); ?>" />

    <label for="ftpPort"><?php echo jgettext('Port'); ?></label>
    <input type="text" id="ftpPort" value="<?php echo $options->get('ftp.port'); ?>" size="8"/>

    <label for="ftpDirectory"><?php echo jgettext('Base Directory'); ?></label>
    <input type="text" id="ftpDirectory" value="<?php echo $options->get('ftp.basedir'); ?>" size="25"/>

    <label for="ftpDownloads"><?php echo jgettext('Downloads Directory'); ?></label>
    <input type="text" id="ftpDownloads" value="<?php echo $options->get('ftp.downloads'); ?>" size="25"/>

    <label for="ftpUser"><?php echo jgettext('User'); ?></label>
    <input type="text" id="ftpUser" value="<?php echo $options->get('ftp.user'); ?>" size="25"/>

    <label for="ftpPass"><?php echo jgettext('Password'); ?></label>
    <input type="password" id="ftpPass" value="<?php echo $options->get('ftp.pass'); ?>" size="25"/>

</fieldset>

