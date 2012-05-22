<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 15-Aug-2011
 * @license    GNU/GPL
 */

ecrScript('g11n');

$item = $this->g11nInfo;
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

        foreach(array_keys($this->languages) as $scope)
        {
            if($scope == 'all')
                continue;

            if('component' != $this->project->type) :
                if($item->scope
                    && $item->scope != $scope
                )
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

                        if($item->templateLink) :
                            if($item->templateExists[$scope]) :
                                $class = 'update';
                                $s = jgettext('Update file');
                            else :
                                $class = 'add';
                                $s = jgettext('Create file');
                            endif;

                            $js = "g11nCreate('g11nCreateTemplate', '', '$scope');";
                            echo '<a class="btn" href="javascript:;" onclick="'.$js.'">'
                                .'<i class="img icon16-'.$class.'"></i>'
                                .$s.'</a>';
                        else :
                            echo $item->templateCommands[$scope];
                        endif;
                    endif;

                    echo '</td>';
                    $sS = $scope;

                    echo '<td>';

                    if($item->fileStatus[$scope][$lang['tag']]) :
                        $class = 'update';
                        $s = jgettext('Update file');
                    else :
                        $class = 'add';
                        $s = jgettext('Create file');
                    endif;

                    $js = "g11nCreate('g11nUpdateLanguage', '".$lang['tag']."', '$scope');";
                    echo '<a class="btn" href="javascript:;" onclick="'.$js.'">'
                        .'<i class="img icon16-'.$class.'"></i>'
                        .$s.'</a>';

                    echo '</td>';
                endforeach;
                ?>
            </tr>
            <?php
            $k = 1 - $k;
        }//foreach
        ?>
    </table>
</div>

<input type="hidden" name="scope"/>
<input type="hidden" name="langTag"/>
