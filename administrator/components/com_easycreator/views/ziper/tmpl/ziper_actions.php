<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 27-May-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

?>
<div class="infoHeader img icon24-actions">
    <?php echo jgettext('Build actions'); ?>
</div>

<?php
if(0 == count($this->preset->actions)) :
    echo '<p>'.jgettext('No build actions defined').'</p>';

    return;
endif;

$event = '';
?>

<ul class="unstyled buildActions" id="actionList">
    <?php foreach($this->preset->actions as $i => $action): ?>
    <?php
    if('' == $event || $event != $action->event) :
        $event = $action->event;
        echo '<li><strong>'.ucfirst($event).'</strong></li>';
    endif;
    ?>
    <li>
        <input type="checkbox" name="actions[]" id="action_<?php echo $i; ?>"
               value="<?php echo $i; ?>" checked="checked"/>
        <label class="inline" for="action_<?php echo $i; ?>">
            <?php echo $action->type; ?>
        </label>
        <?php
        if('script' == $action->type) :
            $s = (strlen($action->script) > 30)
                ? '<span class="hasTip" title="'.$action->script.'">...'
                    .substr($action->script, strlen($action->script) - 30)
                    .'</span>'
                : $action->script;

            echo '<code class="scriptName">'.$s.'</code>';
        endif;
        ?>
    </li>
    <?php endforeach; ?>
</ul>
