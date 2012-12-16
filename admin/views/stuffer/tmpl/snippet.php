<?php
/**
 * This will display code snippets coloured by geshi in a popup with format=raw
 *
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 06-Oct-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

jimport('geshi.geshi');

$snip = array();

for($i = $this->startAtLine - 1; $i < $this->endAtLine; $i++)
{
    $snip[] = $this->fileContents[$i];
}//for

$snip = implode("\n", $snip);

//-- Code language for GeSHi
$lang = 'php';

//-- Alternating line colors
$background1 = '#fcfcfc';
$background2 = '#f0f0f0';

//-- Replace tag markers
$snip = str_replace('&lt;', '<', $snip);
$snip = str_replace('&gt;', '>', $snip);

//-- Replace TAB's with spaces
$snip = str_replace("\t", '   ', $snip);

$geshi = new GeSHi($snip, $lang);

$geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS, 2);
$geshi->set_line_style('background: '.$background1.';', 'background: '.$background2.';', true);
$geshi->start_line_numbers_at($this->startAtLine);

echo '<h3>'.substr($this->path, strlen(JPATH_ROOT) + 1).'</h3>';
echo $geshi->parse_code();
