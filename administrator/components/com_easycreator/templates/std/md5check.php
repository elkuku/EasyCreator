<?php
/**
 * Checks an extension with a given MD5 checksum file.
 *
 * @param string $path Path to md5 file
 * @param array $extensionPaths Indexed array: First folder in md5 file path as key - extension path as value
 *
 * @return array Array of errors
 */
function checkMD5File($path, $extensionPaths)
{
    jimport('joomla.filesystem.file');

    $lines = explode("\n", JFile::read($path));

    $errors = array();

    $errors[0] = 0;//counter..

    foreach($lines as $line)
    {
        if( ! trim($line))
        continue;

        list($md5, $file) = explode(' ', $line);

        $parts = explode(DS, $file);

        if( ! array_key_exists($parts[0], $extensionPaths))
        continue;

        $path = $extensionPaths[$parts[0]].DS.substr($file, strlen($parts[0]) + 1);

        echo (JDEBUG) ? $path.'...' : '';

        $errors[0] ++;

        if( ! JFile::exists($path))
        {
            $errors[] = JText::sprintf('File not found: %s', $path);
            echo (JDEBUG) ? 'not found<br />' : '';

            continue;
        }

        if(md5_file($path) != $md5)
        {
            $errors[] = JText::sprintf('MD5 check failed on file: %s', $path);
            echo (JDEBUG) ? 'md5 check failed<br />' : '';

            continue;
        }

        echo (JDEBUG) ? 'OK<br />' : '';
    }//foreach

    return $errors;
}//function
