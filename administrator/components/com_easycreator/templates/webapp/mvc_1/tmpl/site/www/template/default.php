<?php
##*HEADER*##
$do = JFactory::getApplication()->input->get('do');
$debug = JFactory::getApplication()->get('debug');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>ECR_COM_NAME</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <link href="<?= JURI::root(true); ?>/template/css/bootstrap.css" rel="stylesheet">
    <link href="<?= JURI::root(true); ?>/template/css/bootstrap-responsive.css" rel="stylesheet">
    <link href="<?= JURI::root(true); ?>/template/css/ECR_LOWER_COM_NAME.css" rel="stylesheet">

    <script src="<?= JURI::root(true); ?>/template/js/ECR_LOWER_COM_NAME.js" type="text/javascript"></script>

    <link rel="shortcut icon" href="<?= JURI::root(true); ?>/template/img/favicon.ico">
</head>

<body>

<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>

            <a class="brand" href="#">ECR_COM_NAME</a>

            <div class="nav-collapse">
                <ul class="nav">
                    <? $active = ('' == $do) ? ' active' : '' ?>
                    <li class="<?= $active ?>"><a href="<?= JURI::root(); ?>">Home</a></li>
                    <? $active = ('list' == $do) ? ' active' : '' ?>
                    <li class="<?= $active ?>"><a href="<?= JURI::root(); ?>?do=list">ECR_COM_NAME List</a></li>
                    <? $active = ('ECR_LOWER_COM_NAME' == $do) ? ' active' : '' ?>
                    <li class="<?= $active ?>"><a href="<?= JURI::root(); ?>?do=ECR_LOWER_COM_NAME">New ECR_COM_NAME</a></li>
                    <? $active = ('log' == $do) ? ' active' : '' ?>
                    <li class="<?= $active ?>"><a href="<?= JURI::root(); ?>?do=log">Log</a></li>
                </ul>
            </div>
            <?php if($debug) : ?>
            <span class="label label-important">Debug</span>
            <?php endif; ?>
            <!--/.nav-collapse -->
        </div>
    </div>
</div>

<div class="container">
    <!--ApplicationMessage-->
    <!--ApplicationOutput-->

    <?php if($debug) : ?>
    <pre><!--ApplicationDebug--></pre>
    <?php endif; ?>
    <hr>

    <footer>
        <p>ECR_COM_NAME is powered by <a href="http://joomla.org">Joomla!</a></p>
    </footer>
</div>
<!-- /container -->

</body>
</html>
