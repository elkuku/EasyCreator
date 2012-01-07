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

ecrScript('g11n');

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
            foreach($this->languages['all'] as $lang) :
                echo '<th>'.$lang['tag'].'</th>';
            endforeach;
            ?>
        </tr>
    </thead>
    <?php
    $k = 0;
    $item = $this->g11nInfo;

    foreach(array_keys($this->languages) as $scope)
    {
        if($scope == 'all')
        continue;

        if('component' != $this->project->type) :
            if($item->scope
            && $item->scope != $scope)
            continue;
        endif;

        ?>
    <tr class="<?php echo 'row'.$k; ?>">
        <td>
            <?php echo $scope; ?>
        </td>
        <?php
        $sS = '';

        foreach($this->languages[$scope] as $lang) :
            if($sS != $scope) :
                echo '<td>';

//                 if($item->scope != '' && $item->scope != $scope || ! $item->exists) :
//                 else :
                    if($item->templateLink) :
                        if($item->templateExists[$scope]) :
                            $class = 'update';
                            $s = jgettext('Update file');
                        else :
                            $class = 'add';
                            $s = jgettext('Create file');
                        endif;

                        $js = "g11nCreate('g11nCreateTemplate', '', '$scope');";
                        echo '<a class="ecr_button img icon-16-'.$class.'" href="javascript:;" onclick="'.$js.'">'.$s.'</a>';
                    else :
                        echo $item->templateCommands[$scope];
                    endif;
                endif;

                echo '</td>';
                $sS = $scope;
//             endif;

            echo '<td>';

//             if($item->scope != '' && $item->scope != $scope || ! $item->exists) :
//             else :
                if($item->fileStatus[$scope][$lang['tag']]) :
                    $class = 'update';
                    $s = jgettext('Update file');
                else :
                    $class = 'add';
                    $s = jgettext('Create file');
                endif;

                $js = "g11nCreate('g11nUpdateLanguage', '".$lang['tag']."', '$scope');";
                echo '<a class="ecr_button img icon-16-'.$class.'" href="javascript:;" onclick="'.$js.'">'.$s.'</a>';
//             endif;

            echo '</td>';
        endforeach;
        ?>
    </tr>
    <?php
    }//foreach
    $k = 1 - $k;
    ?>
    </table>
</div>

<input type="hidden" name="scope" />
<input type="hidden" name="langTag" />
