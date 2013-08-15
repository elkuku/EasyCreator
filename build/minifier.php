#!/usr/bin/env php
<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers.Scripts
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 15-May-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

$yuiJar = '/home/elkuku/libs/yui/yuicompressor-2.4.7.jar';

$baseDir = dirname(__DIR__).'/media';

$types = array('admin/css', 'admin/js', 'site/css', 'site/js');

$cnt = 0;

$NL = "\n";

echo $NL;
echo 'EasyCreator CSS and JavaScript minifier'.$NL;
echo '======================================='.$NL.$NL;

if(0)
{
    echo '******* DEACTIVATED ********'.$NL;

    return;
}

foreach($types as $type)
{
    echo '** Minifying '.$type.' ...'.$NL;

    $cntA = 0;

    /* @var DirectoryIterator $fileInfo */
    foreach(new DirectoryIterator($baseDir.'/'.$type) as $fileInfo)
    {
        if($fileInfo->isDir())
            continue;

        $parts = (explode('.', $fileInfo->getFilename()));

        if(2 != count($parts))
            continue;

        echo $fileInfo->getFilename().'... '; //.$NL;

        $path = $fileInfo->getRealPath();

        $outFile = $fileInfo->getPath().'/'.$parts[0].'.min.'.$parts[1];

        if(file_exists($outFile))
            unlink($outFile);

        $cmd = 'java -jar '.$yuiJar.' -o '.$outFile.' '.$path;

        passthru($cmd);

        $cntA ++;
    }

    echo sprintf('%d files have been minified', $cntA).$NL;

    $cnt += $cntA;
}

echo $NL.sprintf('Finished. Total: %d files', $cnt).$NL;
