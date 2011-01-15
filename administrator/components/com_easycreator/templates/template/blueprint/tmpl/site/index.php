<?php
##*HEADER*##

?>
<?php echo '<?xml version="1.0" encoding="utf-8"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>"
lang="<?php echo $this->language; ?>"
dir="<?php echo $this->direction; ?>" >
    <head>
        <jdoc:include type="head" />
        <link rel="stylesheet" href="<?php echo $this->baseurl.'/templates/'
        .$this->template.'/css/template.css'; ?>" type="text/css" />
    </head>
    <body>
        <div id="container">

            <div id="header">
                <h1 class="sitename">
                    <?php
                        if ($this->params->get('title')) :
                            echo $this->params->get('title');
                        else :
                            echo JFactory::getApplication()->getCfg('sitename'); //Seitenname ausgeben
                        endif;
                    ?>
                </h1>
            </div>

            <div id="main">
                <div id="content">
                    <?php if ($this->getBuffer('message')) : ?>
                        <!-- Das Error Div wird nur eingebunden, wenn eine Nachricht existiert  -->
                        <div class="error">
                            <h2><?php echo JText::_('Message'); ?></h2>
                            <jdoc:include type="message" />
                        </div>
                    <?php endif; ?>

                <jdoc:include type="component" />
                </div>

                <div id="menu">
                    <jdoc:include type="modules" name="menu" style="xhtml" />

                    <!-- Legacy 'left' position - please rename -->
                    <jdoc:include type="modules" name="left" style="xhtml" />
                </div>
            </div>
        </div>
    </body>
</html>
