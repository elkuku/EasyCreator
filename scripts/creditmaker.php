<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers.Scripts
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 08-Mar-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

$blackList = array('EMAIL@ADDRESS', 'Nikolai Plath', 'nik-it.de');
$list = array();

//-- They did not get caught by the machine...
$list['fr-FR'] = array('crony, 2008');
$list['pl-PL'] = array('keran, 2008');
$list['zh-CN'] = array('baijianpeng, 2008');

/** @var SplFileInfo $fileInfo */
foreach(new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(dirname(__DIR__).'/administrator/components/com_easycreator/g11n'))
        as $fileInfo
)
{
    if($fileInfo->isDir()
        || 'pot' == $fileInfo->getExtension()
    )
        continue;

    $path = $fileInfo->getPath();

    $parts = explode('/', $path);

    $langTag = array_pop($parts);

    isset($list[$langTag]) || $list[$langTag] = array();

    $translators = array();

    $f = fopen($fileInfo->getRealPath(), 'r');

    $line = '#';

    while($line)
    {
        $line = fgets($f, 1000);

        if(0 !== strpos($line, '#'))
        {
            $line = '';
            continue;
        }

        if(false == strpos($line, '<'))
            continue;

        $line = trim($line, "# \n");

        $translators[] = $line;
    }

    fclose($f);

    foreach($translators as $translator)
    {
        foreach($blackList as $black)
        {
            if(false !== strpos($translator, $black))
                continue 2;
        }

        in_array($translator, $list[$langTag]) || $list[$langTag][] = $translator;
    }
}

ksort($list);

foreach($list as $langTag => $translators)
{
    $clean = str_replace(array('<', '>'), array('&lt;', '&gt;'), $translators);

    echo '<dt>'.$langTag.'</dt>';
    echo '<dd>'.implode('<br />', $clean).'</dd>';

    echo "\n";
}

echo 'Finished'."\n";
