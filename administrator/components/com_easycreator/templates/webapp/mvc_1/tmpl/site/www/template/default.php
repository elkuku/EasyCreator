<?php
##*HEADER*##
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>_ECR_COM_NAME_</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <link href="<?= JURI::root(true); ?>/template/css/bootstrap.css" rel="stylesheet">
    <link href="<?= JURI::root(true); ?>/template/css/bootstrap-responsive.css" rel="stylesheet">
    <link href="<?= JURI::root(true); ?>/template/css/custom.css" rel="stylesheet">

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

            <a class="brand" href="#">_ECR_COM_NAME_</a>

            <div class="nav-collapse">
                <ul class="nav">
                    <li class="active"><a href="<?= JURI::root(); ?>">Home</a></li>
                    <li><a href="<?= JURI::root(); ?>?do=list">_ECR_COM_NAME_ List</a></li>
                    <li><a href="<?= JURI::root(); ?>?do=_ECR_LOWER_COM_NAME_">New _ECR_COM_NAME_</a></li>
                    <li><a href="<?= JURI::root(); ?>?do=log">Log</a></li>
                </ul>
            </div>
            <!--/.nav-collapse -->
        </div>
    </div>
</div>

<div class="container">
    <!-- ApplicationOutput -->

    <hr>

    <footer>
        <p>_ECR_COM_NAME_ is powered by <a href="http://joomla.org">Joomla!</a></p>
    </footer>
</div>
<!-- /container -->

</body>
</html>
