<?php
/**
 * @version SVN: $Id: db_mysql.php 298 2010-12-17 04:41:07Z elkuku $
 * @package    g11n
 * @subpackage Storage handler
 * @author     Nikolai Plath {@link http://nik-it.de}
 * @author     Created on 19-Sep-2010
 * @license    GNU/GPL
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Enter description here ...
 *
 * @package    g11n
 */
class g11nStorageDB extends g11nStorage
{
    /**
     * Stores the strings into a storage.
     *
     * Should be moved..
     *
     * @param string $extension E.g. joomla, com_weblinks, com_easycreator etc.
     * @param string $lang E.g. de-DE, es-ES etc.
     * @param string $fileName File name of the original (ini) file.
     *
     * @return boolean true on success.
     * @throws Exception
     */
    protected function store($extension, $lang, $fileName)
    {
        if(self::$storage == 'off')
        return false;

        $profiler = JProfiler::getInstance('LangDebug');//@@debug
        $profiler->mark('store: '.$extension);//@@debug

        $strings = self::parseFile($fileName);

        switch(self::$storage)
        {
            case 'db':
                $jsonString = json_encode($strings);

                $query = $this->db->getQuery(true);

                $query->insert('`#__language_strings`');
                $query->set('extension = '.$this->db->quote('system'));
                $query->set('lang = '.$this->db->quote($lang));
                $query->set('scope = '.$this->db->quote($this->scope));

                // to quote or not to quote..
//                #$query->set("strings = '".($encoded))."'";
                $query->set('strings = '.$this->db->quote($jsonString));

                $this->db->setQuery($query);

                $this->db->query();

                if($this->db->getError())
                {
                    $this->setError($this->db->getError());

                    $profiler->mark('<span style="color: red;">store db failed **********</span>: '
                    .$extension);//@@debug

                    return false;
                }

                $profiler->mark('store query: '.htmlentities($query));//@@debug

                break;

            default:
                throw new g11nException('Undefined storage: '.self::$storage);//@Do_NOT_Translate

                break;
        }//switch

        $profiler->mark('store SUCCESS ++++: '.$extension);//@@debug

        return true;
    }//function

    /**
     * Retrieve the storage content.
     *
     * @param string $extension Extension
     * @param string $lang Language
     * @param string $fileName The file name
     *
     * @return boolean
     */
    protected function retrieve($extension, $lang, $fileName)
    {
        if(self::$storage == 'off')
        return false;

        $profiler = JProfiler::getInstance('LangDebug');//@@debug
        $profiler->mark('start: '.$extension);//@@debug

        $this->query->clear('where');

        //we will construct a string instead of calling a function three times..
        //		$wheres = 'WHERE extension = '.$this->db->quote($extension)
        //		. ' AND lang = '.$this->db->quote($lang)
        //		. ' AND scope = '.$this->db->quote($this->scope);
        //		$this->query->where($wheres);//that does not work, gives endless recursion..2do.. :(

        $this->query->where('extension = '.$this->db->quote($extension));
        $this->query->where('lang = '.$this->db->quote($lang));
        $this->query->where('scope = '.$this->db->quote($this->scope));

        $this->db->setQuery($this->query);

        $e = $this->db->loadObject();

        if(empty($e->strings))
        {
            $profiler->mark('<span style="color: red;">langload db failed ****</span>'
            .$this->query);//@@debug

            $this->setError($this->db->getError());

            return false;
        }

        $strings = json_decode($e->strings, true);

        $profiler->mark('<span style="color: green;">*Loaded db*</span>');//@@debug

        $this->strings = array_merge($this->strings, $strings);

        // language overrides
        $this->strings = array_merge($this->strings, $this->override);

        $this->paths[$extension][$fileName] = true;

        return true;
    }//function
}//class
