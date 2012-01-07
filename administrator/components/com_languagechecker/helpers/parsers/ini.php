<?php
// @codingStandardsIgnoreStart - for now




class JLanguageCheckerParserIni {

    public function parse($fileName) {
        $fileName = JPath::clean($fileName);

        if( ! file_exists($fileName))
        {
            return array();//@todo throw exception
        }

        $lines = explode("\n", JFile::read($fileName));

        if( ! $lines)
        {
            return array();//@todo throw exception
        }

        $fileInfo = new JObject();
        $fileInfo->fileName = $fileName;
        $fileInfo->format = '';
        $fileInfo->mode = '';
        $fileInfo->strings = array();

        $strings = array();

        foreach ($lines as $lineNo => $line)
        {
            $line = trim($line);

            if( ! $line) continue;

            if(strpos($line, ';') === 0) continue;

            $pos = strpos($line, '=');

            if( ! $pos) continue;

            $key = trim(substr($line, 0, $pos));
            $value = trim(substr($line, $pos + 1));

            $t = new stdClass();

            $t->string = $this->stripQuotes($value);

         #   $t->isCore =($component) ? false : true;
            $t->lines = array();
            $t->lines[] = $lineNo + 1;

            $t->isUsed = false;

            $strings[$key] = $t;
        }//foreach

        if( ! $strings) JFactory::getApplication()->enqueueMessage('No strings found :(', 'error');

        $fileInfo->strings = $strings;

        return $fileInfo;
    }//function

    /**
     * Remove leading and trailing quotes from a string.
     *
     * @param string $string
     *
     * @return string
     */
    private function stripQuotes($string)
    {
        if(strpos($string, '"') === 0)
        {
            $string = substr($string, 1);
        }

        if(strrpos($string, '"') == strlen($string) - 1)
        {
            $string = substr($string, 0, strlen($string) - 1);
        }

        return $string;
    }//function

}//class
