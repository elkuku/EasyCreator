<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers.others
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 03-Feb-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

$output = shell_exec('git tag');

$tags = explode("\n", trim($output));

$tags = array_reverse($tags);

$lastTag = '';

$contents = array();

foreach($tags as $tag)
{
    $output = shell_exec('git log '.$tag.'..'.$lastTag.' --pretty=format:\'%h %ad %an: %s\' --date=short');

    $lastTag = $tag;

    $lines = explode("\n", $output);

    $actDate = '';

    foreach($lines as $line)
    {
        $sha = substr($line, 0, 8);
        $date = substr($line, 8, 10);
        $message = substr($line, 19);

        //-- Remove myself =;)
        $message = str_replace('Nikolai Plath: ', '', $message);
        $message = str_replace('elkuku: ', '', $message);

        if($date != $actDate)
        {
            $contents[] = '';
            $contents[] = $date;

            $actDate = $date;
        }

        $contents[] = $sha.$message;
    }

    $contents[] = '';
    $contents[] = '---------- '.$tag.' ----------';

    /*
    $tagInfo = explode("\n", shell_exec('git show '.$tag));

    foreach($tagInfo as $line)
    {
        if(0 === strpos($line, 'Date'))
        {
            $contents[] = $line;

            break;
        }
    }
    */
}

echo implode("\n", $contents);

echo "\n\n".'finished =;)';
