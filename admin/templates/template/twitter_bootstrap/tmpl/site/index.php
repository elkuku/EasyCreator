<?php
##*HEADER*##

$application = JFactory::getApplication();
$templateParams = $application->getTemplate(true)->params;
$baseLink = $this->baseurl.'/templates/'.$this->template;
$min = '';//(JDEBUG) ? '.min' : '';

?>
<?php echo '<?xml version="1.0" encoding="utf-8"?'.'>'; ?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>"
      dir="<?php echo $this->direction; ?>" >
    <head>
        <jdoc:include type="head" />
        <link rel="stylesheet" href="<?php echo $baseLink.'/css/bootstrap'.$min.'.css'; ?>" type="text/css" />
        <link rel="stylesheet" href="<?php echo $baseLink.'/css/bootstrap-responsive'.$min.'.css'; ?>" type="text/css" />
        <link rel="stylesheet" href="<?php echo $baseLink.'/css/template'.$min.'.css'; ?>" type="text/css" />
    </head>
    <body>

    <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container-fluid">
                <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <a class="brand" href="#"><?php echo $templateParams->get('sitetitle'); ?></a>
                <div class="btn-group pull-right">
                    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="icon-user"></i> Username
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="#">Profile</a></li>
                        <li class="divider"></li>
                        <li><a href="#">Sign Out</a></li>
                    </ul>
                </div>
                <div class="nav-collapse">
                    <ul class="nav">
                        <li class="active"><a href="#">Home</a></li>
                        <li><a href="#about">About</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                    <jdoc:include type="modules" name="position-1" />

                </div><!--/.nav-collapse -->
            </div>
        </div>
    </div>

    <div class="error">
        <jdoc:include type="message" />
    </div>

    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span3">
                <div class="well sidebar-nav">
                    <ul class="nav nav-list">
                        <li class="nav-header">Sidebar</li>
                        <li class="active"><a href="#">Link</a></li>
                        <li><a href="#">Link</a></li>
                        <li><a href="#">Link</a></li>
                        <li><a href="#">Link</a></li>
                        <li class="nav-header">Sidebar</li>
                        <li><a href="#">Link</a></li>
                        <li><a href="#">Link</a></li>
                        <li><a href="#">Link</a></li>
                        <li><a href="#">Link</a></li>
                        <li><a href="#">Link</a></li>
                        <li><a href="#">Link</a></li>
                        <li class="nav-header">Sidebar</li>
                        <li><a href="#">Link</a></li>
                        <li><a href="#">Link</a></li>
                        <li><a href="#">Link</a></li>
                    </ul>

                    <jdoc:include type="modules" name="position-2" />
                    <jdoc:include type="modules" name="position-3" />
                    <jdoc:include type="modules" name="position-4" />

                </div><!--/.well -->
            </div><!--/span-->

            <div class="span9">
                <div class="hero-unit">
                    <jdoc:include type="modules" name="position-5" />
                    <h1>Hello, world!</h1>
                    <p>This is a template for a simple marketing or informational website. It includes a large callout called the hero unit and three supporting pieces of content. Use it as a starting point to create something more unique.</p>
                    <p><a class="btn btn-primary btn-large">Learn more &raquo;</a></p>
                </div>

                <div class="">
                    <jdoc:include type="component" />
                </div>

                <div class="row-fluid">
                    <div class="span4">
                        <jdoc:include type="modules" name="position-6" />
                        <h2>Heading</h2>
                        <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
                        <p><a class="btn" href="#">View details &raquo;</a></p>
                    </div><!--/span-->
                    <div class="span4">
                        <jdoc:include type="modules" name="position-7" />
                        <h2>Heading</h2>
                        <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
                        <p><a class="btn" href="#">View details &raquo;</a></p>
                    </div><!--/span-->
                    <div class="span4">
                        <jdoc:include type="modules" name="position-8" />
                        <h2>Heading</h2>
                        <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
                        <p><a class="btn" href="#">View details &raquo;</a></p>
                    </div><!--/span-->
                </div><!--/row-->
                <div class="row-fluid">
                    <div class="span4">
                        <jdoc:include type="modules" name="position-9" />
                        <h2>Heading</h2>
                        <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
                        <p><a class="btn" href="#">View details &raquo;</a></p>
                    </div><!--/span-->
                    <div class="span4">
                        <h2>Heading</h2>
                        <jdoc:include type="modules" name="position-10" />
                        <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
                        <p><a class="btn" href="#">View details &raquo;</a></p>
                    </div><!--/span-->
                    <div class="span4">
                        <jdoc:include type="modules" name="position-11" />
                        <h2>Heading</h2>
                        <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
                        <p><a class="btn" href="#">View details &raquo;</a></p>
                    </div><!--/span-->
                </div><!--/row-->
            </div><!--/span-->
        </div><!--/row-->

        <hr>

        <footer>
            <jdoc:include type="modules" name="position-12" />
            <p>&copy; Company 2012</p>
        </footer>

    </div><!--/.fluid-container-->

    </body>
</html>
