<?php
##*HEADER*##

$application = JFactory::getApplication();
$templateParams	= $application->getTemplate(true)->params;

?>
<?php echo '<?xml version="1.0" encoding="utf-8"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>"
lang="<?php echo $this->language; ?>"
dir="<?php echo $this->direction; ?>" >
    <head>
        <jdoc:include type="head" />
        <link rel="stylesheet"
         href="<?php echo $this->baseurl.'/templates/'.$this->template.'/css/template.css'; ?>"
         type="text/css" />
    </head>
    <body>
        <div id="container">

            <div id="header">
                <h1 class="sitename">
                    <?php
                        echo $application->getCfg('sitename');
                        echo '<br />';
                        echo $templateParams->get('sitetitle');
                        echo '<br />';
                        echo $templateParams->get('sitedescription');
                    ?>
                </h1>
            </div>

            <div id="main">
                <div id="content">
                    <div class="error">
                        <jdoc:include type="message" />
                    </div>

                    <jdoc:include type="component" />
                </div>

                <div id="menu">
                    <jdoc:include type="modules" name="position-1" style="xhtml" />
                    <jdoc:include type="modules" name="position-2" style="xhtml" />
                </div>
            </div>
        </div>
    </body>
</html>
