<?php
##*HEADER*##

/**
 * This is a file to add template specific chrome to module rendering.  To use it you would
 * set the style attribute for the given module(s) include in your template to use the style
 * for each given modChrome function.
 *
 * eg.  To render a module mod_test in the sliders style, you would use the following include:
 * <jdoc:include type="module" name="test" style="slider" />
 *
 * This gives template designers ultimate control over how modules are rendered.
 *
 * NOTICE: All chrome wrapping methods should be named: modChrome_{STYLE} and take the same
 * two arguments.
 *
 */

/**
 *
 * @param $module object
 * @param $params object
 * @param $attribs array
 */
function modChrome_custom($module, $params, $attribs)
{
    // Überprüfen, ob "headerLevel" gesetzt ist
    if(isset($attribs['headerLevel']))
    {
        $headerLevel = $attribs['headerLevel'];
    }
    else
    {
        // default "3"
        $headerLevel = 3;
    }

    // Überprüfen, ob "class" gesetzt ist
    if(isset($attribs['class']))
    {
        $class = $attribs['class'];
    }
    else
    {
        // default "blue"
        $class = 'blue';
    }

    // umschließendes div mit Modul Klassen Suffix
    echo '<div class="'.$params->get('moduleclass_sfx').'" >';

    // Überprüfen, ob der Titel angezeigt wird
    if($module->showtitle)
    {
        // Titel ausgeben
        echo '<h'.$headerLevel.'>'.$module->title.'</h'.$headerLevel.'>';
    }

    // Content des Moduls ausgeben
    echo '<div class="'.$class.'">';
    echo $module->content;
    echo '</div>';

    // Ende umschließendes div
    echo '</div>';
}//function
