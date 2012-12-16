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

        list($md5, $subPath) = explode(' ', $line);

        $pos = strpos($subPath, '@');

        $path = $subPath;

        $file = '';

        if($pos !== false)// lines containing a @ must be compressed..
        {
            $compressed = substr($subPath, 0, $pos);
            $file = substr($subPath, $pos + 1);
            $path = decompress($compressed).'/'.$file;
        }

        $parts = explode(DS, $path);

        if( ! array_key_exists($parts[0], $extensionPaths))
        continue;

        $path = $extensionPaths[$parts[0]].DS.substr($path, strlen($parts[0]) + 1);

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

/**
 * Decompress a KuKuKompress compressed path
 *
 * @param string $path
 *
 * @return string decompressed path
 */
function decompress($path)
{
    static $previous = '';

    if( ! $previous) //-- Init
    {
        $previous = $path;

        return $previous;
    }

    $decompressed = $previous;//-- Same as previous path - maximun compression :)

    if($path != '=') //-- Different path - too bad..
    {
        $pos = strpos($path, '|');//-- Separates previous path info from new path

        if($pos)
        {
            $command = substr($path, 0, $pos);

            $c = count(explode('-', $command)) - 1;

            $parts = explode('/', $previous);

            $decompressed = '';

            for($i = 0; $i < $c; $i++)
            {
                $decompressed .= $parts[$i].'/';
            }//for

            $addPath = substr($path, $pos + 1);

            $decompressed .= $addPath;

            $decompressed = trim($decompressed, '/');

            $previous = $decompressed;

            return $decompressed;
        }

        $decompressed = $path;
    }

    $decompressed = trim($decompressed, '/');

    $previous = $decompressed;

    return $decompressed;
}//function
