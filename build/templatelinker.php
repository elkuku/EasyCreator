#!/usr/bin/env php
<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers.Scripts
 * @author     Nikolai Plath {@link https://github.com/elkuku}
 * @author     Created on 08-Oct-2017
 * @license    WTFPL
 */

'cli' == PHP_SAPI || die('This script must be executed from the command line.');

version_compare(PHP_VERSION, '5.3', '>=') || die('This script requires PHP >= 5.3');

ini_set('error_reporting', -1);
ini_set('display_errors', 1);

$repoPath = '/home/test/repos/ecr-templates';
$destDir  = realpath(__DIR__ . '/../admin/templates');

foreach (new DirectoryIterator($repoPath) as $type)
{
	if ($type->isDot()
		|| !$type->isDir()
		|| in_array($type->getBasename(), ['.idea', '.git']))
	{
		continue;
	}

	echo 'Type: ' . $type->getBasename() . "\n";

	foreach (new DirectoryIterator($repoPath . '/' . $type->getBasename()) as $template)
	{
		if ($template->isDot())
		{
			continue;
		}

		echo '  Template: ' . $template->getBasename() . '...';

		$addPpath = '/' . $type->getBasename() . '/' . $template->getBasename();

		if (is_link($destDir . $addPpath))
		{
			echo 'exists' . "\n";
		}
		else
		{
			symlink($repoPath . $addPpath, $destDir . $addPpath);

			if (is_link($destDir . $addPpath))
			{
				echo ' Link created' . "\n";
			}
			else
			{
				echo 'Could not create symlink in ' . $destDir . $addPpath;
			}
		}

		echo "\n\nFINISHED =;)\n";
	}
}
