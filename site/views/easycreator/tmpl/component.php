<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Frontent
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 24-Sep-2008
 */

$url = JURI::root().'index.php?option='.$this->selectedProject.'&tmpl=component&print=1';

$params = 'id="comFrame" '
    .'src="'.$url.'" '
    .'width="100%" '
    .'height="500" '
    .'frameborder="0" '
    .'allowtransparency="true" '
    .'scrolling="no" ';
?>
<script type="text/javascript">
    function iFrameHeight() {
        var h = 0;
        if(!document.all) {
            h = document.getElementById('comFrame').contentDocument.height;
            document.getElementById('comFrame').style.height = h + 60 + 'px';
        } else if(document.all) {
            h = document.frames('comFrame').document.body.scrollHeight;
            document.all.blockrandom.style.height = h + 20 + 'px';
        }
    }
</script>

<div style="color: red; border: 1px dashed grey; padding-left: 10px;">Framed !</div>

<table style="width: 100%;">
    <tr valign="top">
        <td>
            <?php easyHTML::projectSelector(); ?>
        </td>
        <td>
            <div style="border: 1px solid red; padding: 5px;">
                <iframe onload="iFrameHeight();" <?php echo $params; ?>>
                    <p><?php echo jgettext('Sorry, your browser cannot display frames'); ?></p>
                </iframe>
            </div>

            <h2><?php echo jgettext('Parameters'); ?></h2>
            <?php //echo $parameter->render(); ?>

        </td>
    </tr>
</table>
