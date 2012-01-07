<?php
/**
 * @package    EasyCreator
 * @subpackage AutoCodes
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 07-Mar-2010
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Enter description here ...@todo class doccomment.
 *
 */
class AutoCodeAdminViewformTableElementRow
{
    /**
     * EasyTableField
     * @var $field
     */
    private $field;

    /**
     * Gets the HTML code.
     *
     * @param EasyTable $table A EasyTable object
     * @param string $indent Indentation string
     *
     * @return string HTML
     */
    public function getCode(EasyTable $table, $indent = '')
    {
        $a = array();

        foreach($table->getFields() as $field)
        {
            $inputType = 'text';
            $this->field = $field;

            if($field->inputType)
            {
                if(method_exists($this, 'type'.$field->inputType))
                {
                    $inputType = $field->inputType;
                }
                else
                {
                    JError::raiseNotice(100, 'Unknown inputType: '.$field->inputType);
                }
            }

            if($inputType == 'hidden')
            {
                $a[] = $this->{'type'.$inputType}().NL;

                continue;
            }

            $a[] = '<tr>';
            $a[] = '    <td width="100" align="right" class="key">';

            $a[] = '        <label for="label_'.$field->name.'">'
            . "<?php echo JText::_('".$field->label."'); ?>"
            . '</label>';

            $a[] = '    </td>';
            $a[] = '    <td>';

            $a[] = $this->{'type'.$inputType}();
            $a[] = '    </td>';
            $a[] = '</tr>';
        }//foreach

        $ret = $indent.implode(NL.$indent, $a).NL;

        return $ret;
    }//function

    /**
     * Get a text field.
     *
     * @return string
     */
    private function typeText()
    {
        $s = '        <input type="text" class="text_area"'
        .' name="'.$this->field->name.'"'
        .' id="label_'.$this->field->name.'"'
        .' size="32"'
        .' maxlength="250"'
        .' value="<?php echo $this->_ECR_COM_NAME_->'.$this->field->name.'; ?>"'
        .' />';

        return $s;
    }//function

    /**
     * Get a hidden field.
     *
     * @return string
     */
    private function typeHidden()
    {
        $s = '        <input type="hidden"'
        .' name="'.$this->field->name.'"'
        .' id="label_'.$this->field->name.'"'
        .' value="<?php echo $this->_ECR_COM_NAME_->'.$this->field->name.'; ?>"'
        .' />';

        return $s;
    }//function

    /**
     * Get a category field.
     *
     * @return string
     */
    private function typeCategory()
    {
        $s = '        <?php echo $this->lists[\'catid\']; ?>';

        return $s;
    }//function
}//class
