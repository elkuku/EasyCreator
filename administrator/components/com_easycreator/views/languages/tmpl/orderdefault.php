<?php
/**
 * @package    EasyCreator
 * @subpackage	Views
 * @author		Nikolai Plath (elkuku)
 * @author		Created on 11-Sep-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die(';)');

JHTML::_('behavior.mootools');

ecrScript('lang_orderdefault');
ecrStylesheet('lang_orderdefault');

$isHeader = true;
$started = false;
?>
<div class="ecr_code">

<?php
foreach($this->default_language as $lang)
{
    if($lang->key == '#' || $lang->key == ';')
    {
        $dString = $lang->value;

        if($isHeader)
        {
            echo '<input type="hidden" name="langfile[]" value="'.$dString.'" />';
            echo '<span style="color: green;">'.$dString.'</span><br />';
        }
        else
        {
            $dfString = '<span style="color: orange;">'.$dString.'</span>';
        }
    }
    else
    {
        $isHeader = false;

        if( ! $started)
        {
            ?>
            <ul id="orderMe" style="list-style-type: none;">
            <?php
$started = true;
        }

        if($lang->value)
        {
            $dString = $lang->key.'='.$lang->value;

            $dfString = '<span style="color: blue;">'.$lang->key.'</span>';
            $dfString .= '<strong style="color: red;"> = </strong>';
            $dfString .= EcrHtml::cleanHTML($lang->value);
        }
        else
       {
            $dString = '';
$dfString = '&nbsp;';
        }
    }

    if( ! $isHeader)
    {
        ?>
        <li class="handle">
            <?php
            echo '<input type="hidden" name="langfile[]" value="'.$dString.'" />';
            echo $dfString;
            ?>
</li>
    <?php
    }
}//foreach
?>
</ul>

</div>
<p>
<input name="newElement" id="newElement" type="text" value=""/>
<span class="btn img icon16-add" onclick="addElement();"><?php echo jgettext('Add Comment'); ?></span>
</p>
<div id="msg"></div>

<script type="text/javascript" language="javascript">
	var sortList = new Sortables($('orderMe'));
</script>
