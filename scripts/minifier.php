#!/usr/bin/env php
<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers.others
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 15-May-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

$yuiJar = '/home/elkuku/libs/yui/yuicompressor-2.4.7.jar';

$types = array('css', 'js');

$cnt = 0;

$NL = "\n";
echo 'EasyCreator CSS and JavaScript minifier'.$NL;

foreach($types as $type)
{
    echo '** Minifying '.$type.' ...'.$NL;

    $cntA = 0;

    /* @var DirectoryIterator $fileInfo */
    foreach(new DirectoryIterator(dirname(__DIR__).'/administrator/components/com_easycreator/assets/'.$type) as $fileInfo)
    {
        if($fileInfo->isDir())
            continue;

        $parts = (explode('.', $fileInfo->getFilename()));

        if(2 != count($parts))
            continue;

        echo $fileInfo->getFilename().$NL;

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
