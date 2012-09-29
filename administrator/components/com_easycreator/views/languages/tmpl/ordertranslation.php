<?php
/**
 * @package    EasyCreator
 * @subpackage	Views
 * @author		Nikolai Plath (elkuku)
 * @author		Created on 11-Sep-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

if( ! $this->sel_language)
{
    EcrHtml::message(jgettext('Please choose a language'));

    return;
}

$cutAfter = JFactory::getApplication()->input->getInt('cut_after', 30);
?>

<table>
	<tr valign="top">
		<td>
			<?php drawTable($this->default_language, jgettext('Default language'), $cutAfter, false); ?>
		</td>
		<td>
			<?php drawTable($this->translated_language, jgettext('Translated language'), $cutAfter, false); ?>
		</td>
		<td>
			<?php drawTable($this->corrected_language, jgettext('Corrected language'), $cutAfter, true); ?>
		</td>
	</tr>
</table>

<?php
function drawTable($langFile, $title, $cutAfter, $displayFormField)
{
    ?>
	<table class="adminlist">
		<tr>
			<th colspan="2"><?php echo $title; ?></th>
		</tr>
		<tr>
			<th><?php echo jgettext('Key'); ?></th>
			<th><?php echo jgettext('Value'); ?></th>
		</tr>
		<?php
        $k = 0;

        foreach($langFile as $line)
        {
             ?>
			<tr class="row<?php echo $k; ?>">
			<?php
            if($line->key == '#')
            {
            ?>
				<td nowrap="nowrap" colspan="2" style="background-color: orange;">
					<?php echo displayCuttedTT($line->value, $cutAfter * 2);

                    if($displayFormField)
                    {
                    ?>
						<input type="hidden" name="langfile[]" value="<?php echo $line->value; ?>" />
						<?php
                    }
                    ?>
				</td>
				<?php
            }
            else
           {
            ?>
				<td nowrap="nowrap">
					<?php echo displayCuttedTT($line->key, $cutAfter); ?>
				</td>
				<td nowrap="nowrap">
					<?php echo displayCuttedTT($line->value, $cutAfter);

                    if($displayFormField)
                    {
                        $val =($line->value) ? $line->key.'='.$line->value : '';
                        ?>
						<input type="hidden" name="langfile[]" value="<?php echo $val; ?>" />
						<?php
                    } ?>
				</td>
			</tr>
			<?php
            }

            $k = 1 - $k;
        }//foreach
        ?>
	</table>
	<?php
}//function

/**
 * displays a span class="hasTip" which will be rendered as a moootools tooltip
 *
 * @param string $string the (html) string to display
 * @param integer $cutAfter numer of letters to display
 * @return html span with string as title
 */
function displayCuttedTT($string, $cutAfter)
{
    $ret = EcrHtml::cleanHTML(substr($string, 0, $cutAfter));

    if(strlen($string) > strlen($ret))
    {
        $ret .= '<span class="hasTip" style="border-bottom: 1px dotted orange" title="'.$string.'">...</span>';
    }

    return $ret;
}//function
