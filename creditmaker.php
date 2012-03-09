<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers.others
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 08-Mar-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

$blackList = array('EMAIL@ADDRESS', 'Nikolai Plath', 'nik-it.de');
$list = array();

/** @var SplFileInfo $fileInfo */
foreach(new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(__DIR__.'/administrator/components/com_easycreator/g11n'))
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

    if(! isset($list[$langTag]))
        $list[$langTag] = array();

    $translators = getTranslators($fileInfo->getRealPath());

    foreach($translators as $translator)
    {
        foreach($blackList as $black)
        {
            if(false !== strpos($translator, $black))
                continue 2;
        }

        if(! in_array($translator, $list[$langTag]))
            $list[$langTag][] = $translator;
    }
}

foreach($list as $langTag => $translators)
{
    echo '<li>';
    $br = '<br />';
    echo '<strong>'.$langTag.'</strong>'.$br;

    $clean = str_replace(array('<', '>'), array('&lt;', '&gt;'), $translators);

    echo implode($br, $clean);

    echo '</li>';
    echo "\n\n";
}

/**
 * @param $file
 *
 * @return array
 */
function getTranslators($file)
{
    $ret = array();

    $f = fopen($file, 'r');
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

        $ret[] = $line;
    }

    fclose($f);

    return $ret;
}
