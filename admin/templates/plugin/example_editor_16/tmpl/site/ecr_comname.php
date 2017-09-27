<?php
##*HEADER*##

jimport('joomla.plugin.plugin');

/**
 * ECR_COM_NAME Editor plugin.
 *
 * @package    ECR_COM_NAME
 * @subpackage Plugin
 */
class plgEditorECR_COM_NAME extends JPlugin
{
    /**
     * Method to handle the onInitEditor event.
     *  - Initialises the Editor
     *
     * @return string JavaScript Initialization string
     */
    public function onInit()
    {
        $txt =	"<script type=\"text/javascript\">
					function insertAtCursor(myField, myValue) {
						if (document.selection) {
							// IE support
							myField.focus();
							sel = document.selection.createRange();
							sel.text = myValue;
						} else if (myField.selectionStart || myField.selectionStart == '0') {
							// MOZILLA/NETSCAPE support
							var startPos = myField.selectionStart;
							var endPos = myField.selectionEnd;
							myField.value = myField.value.substring(0, startPos)
								+ myValue
								+ myField.value.substring(endPos, myField.value.length);
						} else {
							myField.value += myValue;
						}
					}
				</script>";

        return $txt;
    }

    /**
     * Copy editor content to form field.
     *
     * Not applicable in this editor.
     *
     * @return	void
     */
    public function onSave()
    {
        return;
    }

    /**
     * Get the editor content.
     *
     * @param	string	$id		The id of the editor field.
     *
     * @return	string
     */
    public function onGetContent($id)
    {
        return "document.getElementById('$id').value;\n";
    }

    /**
     * Set the editor content.
     *
     * @param	string	$id		The id of the editor field.
     * @param	string	$html	The content to set.
     *
     * @return	string
     */
    public function onSetContent($id, $html)
    {
        return "document.getElementById('$id').value = $html;\n";
    }

    /**
     * @param	string	$id
     *
     * @return	string
     */
    public function onGetInsertMethod($id)
    {
        static $done = false;

        // Do this only once.
        if( ! $done)
        {
            $doc = JFactory::getDocument();
            $js = "\tfunction jInsertEditorText(text, editor) {
				insertAtCursor(document.getElementById(editor), text);
			}";
            $doc->addScriptDeclaration($js);
        }

        return true;
    }

    /**
     * Display the editor area.
     *
     * @param	string	$name		The control name.
     * @param	string	$html		The contents of the text area.
     * @param	string	$width		The width of the text area (px or %).
     * @param	string	$height		The height of the text area (px or %).
     * @param	int		$col		The number of columns for the textarea.
     * @param	int		$row		The number of rows for the textarea.
     * @param	boolean	$buttons	True and the editor buttons will be displayed.
     * @param	string	$id			An optional ID for the textarea (note: since 1.6). If not supplied the name is used.
     * @param	string	$asset
     * @param	object	$author
     * @param	array	$params		Associative array of editor parameters.
     *
     * @return	string
     */
    public function onDisplay($name, $content, $width, $height, $col, $row
    , $buttons = true, $id = null, $asset = null, $author = null, $params = array())
    {
        if(empty($id))
        {
            $id = $name;
        }

        // Only add "px" to width and height if they are not given as a percentage
        if(is_numeric($width))
        {
            $width .= 'px';
        }

        if(is_numeric($height))
        {
            $height .= 'px';
        }

        $buttons = $this->_displayButtons($id, $buttons, $asset, $author);
        $editor  = "<textarea name=\"$name\" id=\"$id\" cols=\"$col\" rows=\"$row\""
        ." style=\"width: $width; height: $height;\">$content</textarea>".$buttons;

        return $editor;
    }

    private function _displayButtons($name, $buttons, $asset, $author)
    {
        // Load modal popup behavior
        JHtml::_('behavior.modal', 'a.modal-button');

        $args['name'] = $name;
        $args['event'] = 'onGetInsertMethod';

        $return = '';
        $results[] = $this->update($args);

        foreach($results as $result)
        {
            if(is_string($result) && trim($result))
            {
                $return .= $result;
            }
        }

        if(is_array($buttons)
        || (is_bool($buttons) && $buttons))
        {
            $results = $this->_subject->getButtons($name, $buttons, $asset, $author);

            // This will allow plugins to attach buttons or change the behavior on the fly using AJAX
            $return .= "\n<div id=\"editor-xtd-buttons\">\n";

            foreach($results as $button)
            {
                // Results should be an object
                if($button->get('name'))
                {
                    $modal		= ($button->get('modal')) ? 'class="modal-button"' : null;
                    $href		= ($button->get('link')) ? 'href="'.JURI::base().$button->get('link').'"' : null;
                    $onclick	= ($button->get('onclick')) ? 'onclick="'.$button->get('onclick').'"' : null;
                    $title      = ($button->get('title')) ? $button->get('title') : $button->get('text');

                    $return .= "<div class=\"button2-left\"><div class=\"".$button->get('name')."\">"
                    ."<a ".$modal." title=\"".$title."\" ".$href." ".$onclick." rel=\"".$button->get('options')."\">"
                    .$button->get('text')."</a></div></div>\n";
                }
            }

            $return .= "</div>\n";
        }

        return $return;
    }
}
