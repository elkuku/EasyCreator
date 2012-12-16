<?php
//-- This file does not meet the KuKuKodingStandards =;(
// @codingStandardsIgnoreStart

/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | Copyright (c) 2003-2004 John Griffin                                 |
// +----------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or        |
// | modify it under the terms of the GNU Lesser General Public           |
// | License as published by the Free Software Foundation; either         |
// | version 2.1 of the License, or (at your option) any later version.   |
// |                                                                      |
// | This library is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU    |
// | Lesser General Public License for more details.                      |
// |                                                                      |
// | You should have received a copy of the GNU Lesser General Public     |
// | License along with this library; if not, write to the Free Software  |
// | Foundation, Inc., 59 Temple Place, Suite 330,Boston,MA 02111-1307 USA|
// +----------------------------------------------------------------------+
// | Authors: John Griffin <jgriffin316@netscape.net>                     |
// +----------------------------------------------------------------------+
//

require_once 'PEAR.php';

/**
 * A SQL parse tree compiler.
 *
 * @author  John Griffin <jgriffin316@netscape.net>
 * @version 0.1
 * @access  public
 * @package EcrSqlParser
 */
class SQL_Parser_Compiler
{
    var $tree;

// {{{ function SQL_Parser_Compiler($array = null)
    function SQL_Parser_Compiler($array = null)
    {
        $this->tree = $array;
    }
// }}}

//    {{{ function getWhereValue ($arg)
    function getWhereValue ($arg)
    {
        switch ($arg['type']) {
            case 'ident':
            case 'real_val':
            case 'int_val':
            case 'null':
                $value = $arg['value'];
                break;
            case 'text_val':
                $value = '\''.$arg['value'].'\'';
                break;
            case 'subclause':
                $value = '('.$this->compileSearchClause($arg['value']).')';
                break;
            default:
                return PEAR::raiseError('Unknown type: '.$arg['type']);
        }
        return $value;
    }
//    }}}

//    {{{ function getParams($arg)
    function getParams($arg)
    {
        $types = count($arg['type']);
        for ($i = 0; $i < $types; $i++) {
            switch ($arg['type'][$i]) {
                case 'ident':
                case 'real_val':
                case 'int_val':
                case 'null':
                    $value[] = $arg['value'][$i];
                    break;
                case 'text_val':
                    $value[] = '\''.$arg['value'][$i].'\'';
                    break;
                default:
                    return PEAR::raiseError('Unknown type: '.$arg['type'][$i]);
            }
        }
        $value ='('.implode(', ', $value).')';
        return $value;
    }
//    }}}

//    {{{ function compileFunctionOpts($arg)
    function compileFunctionOpts($arg)
    {
        $types = count($arg['type']);
        for ($i = 0; $i < $types; $i++) {
            switch ($arg['type'][$i]) {
                case 'ident':
                case 'real_val':
                case 'int_val':
                case 'null':
                    $value[] = $arg['arg'][$i];
                    break;
                case 'text_val':
                    $value[] = '\''.$arg['arg'][$i].'\'';
                    break;
                default:
                    return PEAR::raiseError('Unknown type: '.$arg['type'][$i]);
            }
        }
        $value = implode(', ', $value);
        return $value;
    }
//    }}}

//    {{{ function compileSearchClause
    function compileSearchClause($where_clause)
    {
        $value = '';
        if (isset ($where_clause['arg_1']['value'])) {
            $value = $this->getWhereValue ($where_clause['arg_1']);
            if (PEAR::isError($value)) {
                return $value;
            }
            $sql = $value;
        } else {
            $value = $this->compileSearchClause($where_clause['arg_1']);
            if (PEAR::isError($value)) {
                return $value;
            }
            $sql = $value;
        }
        if (isset ($where_clause['op'])) {
            if ($where_clause['op'] == 'in') {
                $value = $this->getParams($where_clause['arg_2']);
                if (PEAR::isError($value)) {
                    return $value;
                }
                if (isset($where_clause['neg'])) {
                    $sql .= ' not';
                }
                $sql .= ' '.$where_clause['op'].' '.$value;
            } elseif ($where_clause['op'] == 'is') {
                $value = isset ($where_clause['neg']) ? 'not null' : 'null';
                $sql .= ' is '.$value;
            } else {
                $sql .= ' '.$where_clause['op'].' ';
                if (isset ($where_clause['arg_2']['value'])) {
                    $value = $this->getWhereValue ($where_clause['arg_2']);
                    if (PEAR::isError($value)) {
                        return $value;
                    }
                    $sql .= $value;
                } else {
                    $value = $this->compileSearchClause($where_clause['arg_2']);
                    if (PEAR::isError($value)) {
                        return $value;
                    }
                    $sql .= $value;
                }
            }
        }
        return $sql;
    }
//    }}}

//    {{{ function compileSelect()
    function compileSelect()
    {
        // save the command and set quantifiers
        $sql = 'select ';
        if (isset($this->tree['set_quantifier'])) {
            $sql .= $this->tree['set_quantifier'].' ';
        }

        // save the column names and set functions
        $cols = count($this->tree['column_names']);
        for ($i = 0; $i < $cols; $i++) {
            $column = $this->tree['column_names'][$i];
            if ($this->tree['column_aliases'][$i] != '') {
                $column .= ' as '.$this->tree['column_aliases'][$i];
            }
            $column_names[] = $column;
        }

        $funcs = count($this->tree['set_function']);
        for ($i = 0; $i < $funcs; $i++) {
            $column = $this->tree['set_function'][$i]['name'].'(';
            if (isset ($this->tree['set_function'][$i]['distinct'])) {
                $column .= 'distinct ';
            }
            if (isset ($this->tree['set_function'][$i]['arg'])) {
                $column .= $this->compileFunctionOpts($this->tree['set_function'][$i]);
            }
            $column .= ')';
            if ($this->tree['set_function'][$i]['alias'] != '') {
                $column .= ' as '.$this->tree['set_function'][$i]['alias'];
            }
            $column_names[] = $column;
        }
        if (isset($column_names)) {
            $sql .= implode (", ", $column_names);
        }

        // save the tables
        $sql .= ' from ';
        $c_tables = count($this->tree['table_names']);
        for ($i = 0; $i < $c_tables; $i++) {
            $sql .= $this->tree['table_names'][$i];
            if ($this->tree['table_aliases'][$i] != '') {
                $sql .= ' as '.$this->tree['table_aliases'][$i];
            }
            if ($this->tree['table_join_clause'][$i] != '') {
                $search_string = $this->compileSearchClause ($this->tree['table_join_clause'][$i]);
                if (PEAR::isError($search_string)) {
                    return $search_string;
                }
                $sql .= ' on '.$search_string;
            }
            if (isset($this->tree['table_join'][$i])) {
                $sql .= ' '.$this->tree['table_join'][$i].' ';
            }
        }

        // save the where clause
        if (isset($this->tree['where_clause'])) {
            $search_string = $this->compileSearchClause ($this->tree['where_clause']);
            if (PEAR::isError($search_string)) {
                return $search_string;
            }
            $sql .= ' where '.$search_string;
        }

        // save the group by clause
        if (isset ($this->tree['group_by'])) {
            $sql .= ' group by '.implode(', ', $this->tree['group_by']);
        }

        // save the order by clause
        if (isset ($this->tree['sort_order'])) {
            foreach ($this->tree['sort_order'] as $key => $value) {
                $sort_order[] = $key.' '.$value;
            }
            $sql .= ' order by '.implode(', ', $sort_order);
        }

        // save the limit clause
        if (isset ($this->tree['limit_clause'])) {
            $sql .= ' limit '.$this->tree['limit_clause']['start'].','.$this->tree['limit_clause']['length'];
        }

        return $sql;
    }
//    }}}

//    {{{ function compileUpdate()
    function compileUpdate()
    {
        $sql = 'update '.implode(', ', $this->tree['table_names']);

        // save the set clause
        $cols = count($this->tree['column_names']);
        for ($i = 0; $i < $cols; $i++) {
            $set_columns[] = $this->tree['column_names'][$i].' = '.$this->getWhereValue($this->tree['values'][$i]);
        }
        $sql .= ' set '.implode (', ', $set_columns);

        // save the where clause
        if (isset($this->tree['where_clause'])) {
            $search_string = $this->compileSearchClause ($this->tree['where_clause']);
            if (PEAR::isError($search_string)) {
                return $search_string;
            }
            $sql .= ' where '.$search_string;
        }
        return $sql;
    }
//    }}}

//    {{{ function compileDelete()
    function compileDelete()
    {
        $sql = 'delete from '.implode(', ', $this->tree['table_names']);

        // save the where clause
        if (isset($this->tree['where_clause'])) {
            $search_string = $this->compileSearchClause ($this->tree['where_clause']);
            if (PEAR::isError($search_string)) {
                return $search_string;
            }
            $sql .= ' where '.$search_string;
        }
        return $sql;
    }
//    }}}

//    {{{ function compileInsert()
    function compileInsert()
    {
        $sql = 'insert into '.$this->tree['table_names'][0].' ('.
                implode(', ', $this->tree['column_names']).') values (';

        $c_vals = count($this->tree['values']);
        for ($i = 0; $i < $c_vals; $i++) {
            $value = $this->getWhereValue ($this->tree['values'][$i]);
            if (PEAR::isError($value)) {
                return $value;
            }
            $value_array[] = $value;
        }
        $sql .= implode(', ', $value_array).')';
        return $sql;
    }
//    }}}

//    {{{ function compile($array = null)
    function compile($array = null)
    {
        $this->tree = $array;

        switch ($this->tree['command']) {
            case 'select':
                return $this->compileSelect();
                break;
            case 'update':
                return $this->compileUpdate();
                break;
            case 'delete':
                return $this->compileDelete();
                break;
            case 'insert':
                return $this->compileInsert();
                break;
            case 'create':
            case 'drop':
            case 'alter':
            default:
                return PEAR::raiseError('Unknown action: '.$this->tree['command']);
        }    // switch ($this->_tree["command"])

    }
//    }}}
}
