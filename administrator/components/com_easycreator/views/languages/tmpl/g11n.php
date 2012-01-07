<?php
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 15-Aug-2011
 * @license    GNU/GPL
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

ecrStylesheet('g11n');
?>

<div id="editcell">
	<table class="adminlist">
	<thead>
		<tr>
			<th>
				<?php echo jgettext('Scope'); ?>
			</th>
            <th style="background-color: #ffc;">
                <?php echo jgettext('Template'); ?>
            </th>
			<?php
            foreach($this->languages['all'] as $lang)
            {
                if($lang['tag'] == 'xx-XX')
                continue;


                echo '<th>';
                echo $lang['tag'];
                echo '</th>';
            }//foreach
            ?>
		</tr>
	</thead>
	<?php
    $k = 0;
    $item = $this->g11nInfo;

//         $checked = JHTML::_('grid.id', $i, $item->id);
    $checkDrawn = false;

    foreach($this->languages as $scope => $langs) :
        if('all' == $scope)
        continue;

        if('component' != $this->project->type) :
            if($item->scope
            && $item->scope != $scope)
            continue;
        endif;
        ?>
		<tr class="<?php echo "row$k"; ?>">
			<?php if(0 && $checkDrawn) : ?>
				<td colspan="3">&nbsp;</td>
			<?php else : ?>
			<?php $checkDrawn = true;?>
			<?php endif; ?>
			<td>
				<?php echo $scope; ?>
			</td>
			<?php
                    echo '<td style="text-align: center;">';

                    if($item->templateStatus[$scope])
                    {
                        $s = jgettext('Found');
                        $class = ' found';
                    }
                    else//
                    {
                        $s = jgettext('Not found');
                        $class = ' notfound';
                    }

                    //if($item->scope != '' && $item->scope != $scope || ! $item->exists) :
                    //else :
                        echo '<span class="status '.$class.'" alt="'.$s.'" title="'.$s.'" />';
                    //endif;

                    echo '</td>';

                foreach ($langs as $lang) :
                    if($lang['tag'] == 'xx-XX')
                    continue;

                    echo '<td>';

                    //if($item->scope != '' && $item->scope != $scope || ! $item->exists) :
                    //else :
                        if($item->fileStatus[$scope][$lang['tag']])
                        {
                            $s = jgettext('Found');
                            $class = ' found';
                            echo '<span class="status '.$class.'" alt="'.$s.'" title="'.$s.'" />';

                            if($item->cacheStatus[$scope][$lang['tag']])
                            {
                                $s = jgettext('Cached');
                            }
                            else//
                            {
                                $s = jgettext('Not cached');
                                $class = ' notfound';
                            }

                            echo '<span class="status '.$class.'" alt="'.$s.'" title="'.$s.'" />';
                        }
                        else//
                        {
                            $s = jgettext('Not found');
                            echo '<span class="status notfound" alt="'.$s.'" title="'.$s.'" />';
                        }

                        echo '<br />';

                        //endif;

                    echo '</td>';
                endforeach;
            endforeach;
            ?>
		</tr>
		<?php
        $k = 1 - $k;
    ?>
	</table>
</div>
