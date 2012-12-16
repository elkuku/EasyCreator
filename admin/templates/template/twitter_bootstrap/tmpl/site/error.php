<?php
##*HEADER*##

if( ! isset($this->error)) :
    $this->error = JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
    $this->debug = false;
endif;

?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>"
      dir="<?php echo $this->direction; ?>">
<head>
    <title><?php echo $this->error->getCode().' - '.$this->title; ?></title>
    <link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/system/css/error.css" type="text/css"/>
</head>

<body>

<div class="error">
    <div id="outline">
        <div id="errorboxoutline">
            <div id="errorboxheader"><?php echo $this->error->getCode().' - '.$this->error->getMessage(); ?></div>
            <div id="errorboxbody">
                <p><strong><?php echo JText::_('JERROR_LAYOUT_NOT_ABLE_TO_VISIT'); ?></strong></p>
                <ol>
                    <li><?php echo JText::_('JERROR_LAYOUT_AN_OUT_OF_DATE_BOOKMARK_FAVOURITE'); ?></li>
                    <li><?php echo JText::_('JERROR_LAYOUT_SEARCH_ENGINE_OUT_OF_DATE_LISTING'); ?></li>
                    <li><?php echo JText::_('JERROR_LAYOUT_MIS_TYPED_ADDRESS'); ?></li>
                    <li><?php echo JText::_('JERROR_LAYOUT_YOU_HAVE_NO_ACCESS_TO_THIS_PAGE'); ?></li>
                    <li><?php echo JText::_('JERROR_LAYOUT_REQUESTED_RESOURCE_WAS_NOT_FOUND'); ?></li>
                    <li><?php echo JText::_('JERROR_LAYOUT_ERROR_HAS_OCCURRED_WHILE_PROCESSING_YOUR_REQUEST'); ?></li>
                </ol>
                <p><strong><?php echo JText::_('JERROR_LAYOUT_PLEASE_TRY_ONE_OF_THE_FOLLOWING_PAGES'); ?></strong></p>

                <ul>
                    <li><a href="<?php echo $this->baseurl; ?>/index.php"
                           title="<?php echo JText::_('JERROR_LAYOUT_GO_TO_THE_HOME_PAGE'); ?>">
                        <?php echo JText::_('JERROR_LAYOUT_HOME_PAGE'); ?></a></li>
                    <li><a href="<?php echo $this->baseurl; ?>/index.php?option=com_search"
                           title="<?php echo JText::_('JERROR_LAYOUT_SEARCH_PAGE'); ?>">
                        <?php echo JText::_('JERROR_LAYOUT_SEARCH_PAGE'); ?></a></li>

                </ul>

                <p><?php echo JText::_('JERROR_LAYOUT_PLEASE_CONTACT_THE_SYSTEM_ADMINISTRATOR'); ?>.</p>

                <div id="techinfo">
                    <p><?php echo $this->error->getMessage(); ?></p>

                    <p>
                        <?php
                        if($this->debug) :
                            echo $this->renderBacktrace();
                        endif;
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
