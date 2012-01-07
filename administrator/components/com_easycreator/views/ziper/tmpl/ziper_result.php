<?php
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 07-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

if( ! $this->zipResult)
return;
?>

<div class="ecr_floatbox">
<?php if( ! $this->zipResult->errors): ?>
	<h1 class="img icon-16-check_ok" style="color: green; text-align: center;">
		<?php echo jgettext('Your ZIPfile has been created sucessfully'); ?>
	</h1>
    <?php if(count($this->zipResult->downloadLinks)) : ?>
        <ul class="downloadLinks">
        <?php foreach($this->zipResult->downloadLinks as $link) : ?>
            <li>
            	<h2><a href="<?php echo $link; ?>"><?php echo JFile::getName(JPath::clean($link)); ?></a></h2>
            </li>
        <?php endforeach; ?>

        </ul>

    <?php else : ?>
        <p><?php echo jgettext('No download available'); ?></p>
    <?php endif; ?>

<?php else:
    ecrHTML::displayMessage(jgettext('Your ZIPfile has NOT been created'), 'error');
    echo '<h2>'.jgettext('Errors').'</h2>';
    echo '<ul><li>'.implode('</li><li>', $errors).'</li></ul>';
endif; ?>
</div>

<?php if($this->zipResult->log): ?>
<div class="ecr_floatbox">
    <div class="ecr_codebox_header" style="font-size: 1.4em;" onclick="toggleDiv('ecr_logdisplay');">
        <?php echo jgettext('Log File'); ?>
    </div>
    <div id="ecr_logdisplay" style="display: none;">
        <?php echo $this->zipResult->log; ?>
    </div>
</div>
<?php endif;
