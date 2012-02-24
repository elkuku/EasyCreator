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
    $output = shell_exec('git log '.$tag.'..'.$lastTag.' --pretty=format:\'%ad %an: %s\' --date=short');

    $lastTag = $tag;

    $lines = explode("\n", $output);

    $actDate = '';

    foreach($lines as $line)
    {
        $date = substr($line, 0, 10);

        $message = substr($line, 11);

        //-- Remove myself ;)
        $message = str_replace('Nikolai Plath: ', '', $message);

        if($date != $actDate)
        {
            $contents[] = '';
            $contents[] = $date;

            $actDate = $date;
        }

        $contents[] = $message;
    }

    $contents[] = '';
    $contents[] = '---------- '.$tag.' ----------';
}

echo implode("\n", $contents);

echo "\n\n".'finished =;)';
