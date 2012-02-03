<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elkuku
 * Date: 03.02.12
 * Time: 07:22
 */

$output = shell_exec('git tag');

$tags = explode("\n", trim($output));

$tags = array_reverse($tags);

$lastTag = '';

$contents = array();

foreach ($tags as $tag)
{
	$output = shell_exec("git log ".$tag."..".$lastTag." --pretty=format:'%ad %s' --date=short");

	$lastTag = $tag;

	$lines = explode("\n", $output);

	$actDate = '';

	foreach ($lines as $line)
	{
		$date = substr($line, 0, 10);

		$message = substr($line, 11);

		if ($date != $actDate)
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
