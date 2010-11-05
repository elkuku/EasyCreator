<?php
##*HEADER*##

//--Include MooTools
JHTML::_('behavior.modal');
jimport('joomla.html.pane');

$document =& JFactory::getDocument();

//--Add javascript to document
$js = "
var assetsBase = '".JURI::root()."components/_ECR_COM_COM_NAME_/assets';
var _LOADING_ = '".JText::_('Loading')."';
";
$document->addScriptDeclaration($js);
$document->addScript(JURI::root(true).'/components/_ECR_COM_COM_NAME_/assets/js/demo_ajax.js');
$document->addScript(JURI::root(true).'/components/_ECR_COM_COM_NAME_/assets/js/demo_sortables.js');
$document->addScript(JURI::root(true).'/components/_ECR_COM_COM_NAME_/assets/js/demo_tooltip.js');

//-- Add css
$document->addStyleSheet('components/_ECR_COM_COM_NAME_/assets/css/demo_accordion.css');
$document->addStyleSheet('components/_ECR_COM_COM_NAME_/assets/css/demo_sortables.css');
$document->addStyleSheet('components/_ECR_COM_COM_NAME_/assets/css/demo_tooltip.css');

echo '<h1>MooTools TestPage</h1>';

echo '<h2>Accordion</h2>';

$pane =& JPane::getInstance('sliders');

echo $pane->startPane("yourname-pane");

foreach($this->greetings as $greeting)
{
    echo $pane->startPanel($greeting->greeting, $greeting->greeting.'-page');

    echo '<h4>'.$greeting->greeting.'</h4>';
    echo 'Lorem Ipsum Weltschmerz... ;)';

    echo $pane->endPanel();
}//foreach

echo $pane->endPane();
?>

<h2>AJAX</h2>

<a id="ajaxLink" style="cursor: pointer;"><?php echo JText::_('Get an ajax random greeting'); ?></a>

<!-- This div will contain the response from our ajax call -->
<h2 id="fieldsContainer"></h2>

<!-- This div will hold our response message -->
<div id="consoleContainer"></div>

<h2>Lightbox</h2>

<p>
	<!-- By giving the link a class="modal" and adding the modal.js (see above) -->
	<!-- A Javascript script will be run and convert all those links to be opened in alightbox -->
	Please read the <a href="index.php?option=com_content&view=article&id=5&tmpl=component" class="modal">License</a>.
</p>
<p>
	<!-- You can specify the size of the lightbox -->
	Come and visit <a rel="{handler: 'iframe', size: {x: 970, y: 550}}"
	href="http://www.joomla.org" class="modal" target="_blank">Joomla.org</a>.
</p>

<h2>Sortables</h2>

Drag &amp; Drop these items:
<div>
	<!-- Mootools will make this <ol> sortable with it's ID -->
	<ol id="mySortablesDemo" class="myList">
		<?php
        for($i = 1; $i < 5; $i++)
        {
            echo '<li>Item #'.$i.'</li>';
        }//for
        ?>
	</ol>
</div>

<h2>Tooltips</h2>

<!-- This is our custom class: zoomTip -->
Demo of <span style="color: blue;" class="zoomTip" title="My custom tooltip::Hello world ;)
<img src='images/powered_by.png' />">custom formatted tooltips</span>.

<hr />