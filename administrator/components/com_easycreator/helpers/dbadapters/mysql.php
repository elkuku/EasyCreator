<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 16-Jul-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

ecrLoadHelper('dbadapters.adapter');// or throw new Exception('dbAdapter not found');

class dbAdapterMySQL extends dbAdapter
{
    public function __construct()
    {
        parent::__construct();;
    }//function

    public function parseCreate()
    {
        ecrLoadHelper('SQL.Parser');

        //$query = substr($this->query, 7);
        $parser = new SQL_Parser($this->query->processed, 'MySQL');

        $parsed = $parser->parseCreate();

        return $parsed;
    }
}//class
