<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

//-- This file does not meet the KuKuKodingStandards =;(
// @codingStandardsIgnoreStart

/**
 *
 * PHP versions 5
 *
 * LICENSE: This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; If not, see <http://www.gnu.org/licenses/>.
 *
 * @todo      Refactor sentinel conditions to show flow
 * @todo      Document EBNF of what each major block is actually doing
 * @todo      Document getToken/pushBack assumptions of each major block
 * @todo      Refactor into Expression classes, keeping the Tokenizer the same,
 *            outputting the same parse tree
 * @todo      we need to remember spaces, this is esential to determine whether
 *            it is a function call: "function("
 *            or just some expression: "ident ("
 * @category  Database
 * @package   EcrSqlParser
 * @author    Erich Enke <erich.Enke@gmail.com>
 * @author    Brent Cook <busterbcook@yahoo.com>
 * @author    Jason Pell <jasonpell@hotmail.com>
 * @author    Lauren Matheson <inan@canada.com>
 * @author    John Griffin <jgriffin316@netscape.net>
 * @copyright 2002-2004 Brent Cook
 *            2005 Erich Enke
 * @license   http://www.gnu.org/licenses/lgpl.html GNU Lesser GPL 3
 * @link      http://pear.php.net/package/EcrSqlParser
 * @since     File available since Release 0.1.0
 */

/**
 *
 */
require_once dirname(__FILE__) . '/Parser/Lexer.php';

/**
 * A sql parser
 *
 * @category  Database
 * @package   EcrSqlParser
 * @author    Brent Cook <busterbcook@yahoo.com>
 * @copyright 2002-2004 Brent Cook
 *            2005 Erich Enke
 * @license   http://www.gnu.org/licenses/lgpl.html GNU Lesser GPL 3
 * @version   Devel: 0.5
 * @link      http://pear.php.net/package/EcrSqlParser
 * @since     File available since Release 0.1.0
 */
class EcrSqlParser
{
    /**
     * @var    SQL_Parser_Lexer
     * @access public
     */
    public $lexer;

    /**
     * @var    string
     * @access public
     */
    public $token;

    /**
     * @var    array
     * @access public
     */
    public $functions = array();

    /**
     * @var    array
     * @access public
     */
    public $types = array();

    /**
     * @var    array
     * @access public
     */
    public $symbols = array();

    /**
     * @var    array
     * @access public
     */
    public $operators = array();

    /**
     * @var    array
     * @access public
     */
    public $synonyms = array();

    /**
     * @var    array
     * @access public
     */
    public $lexeropts = array();

    /**
     * @var    array
     * @access public
     */
    public $parseropts = array();

    /**
     * @var    array
     * @access public
     */
    public $comments = array();

    /**
     * @var    array
     * @access public
     */
    public $quotes = array();

    /**
     * @var    array
     * @access public
     */
    static public $dialects = array(
        'ANSI',
        'MySQL',
    );

    public $notes = array();

    /**
     *
     */
    const DIALECT_ANSI = 'ANSI';

    /**
     *
     */
    const DIALECT_MYSQL = 'MySQL';

    // {{{ function EcrSqlParser($string = null, $dialect = 'ANSI')
    /**
    * Constructor
    *
    * @param string $string the SQL query to parse
    * @param string $dialect the SQL dialect
    * @uses  EcrSqlParser::setDialect()
    * @uses  EcrSqlParser::$lexer      W to create it
    * @uses  EcrSqlParser::$symbols    R
    * @uses  EcrSqlParser::$lexeropts  R
    * @uses  SQL_Parser_Lexer        to create an Object
    * @uses  SQL_Parser_Lexer::$symbols W to set it
    * @uses  is_string()
    */
    public function __construct($string = null, $dialect = 'ANSI')
    {
        $this->setDialect($dialect);

        if (is_string($string)) {
            $this->initLexer($string);
        }
    }
    // }}}

    function initLexer($string)
    {
        // Initialize the Lexer with a 3-level look-back buffer
        $this->lexer = new SQL_Parser_Lexer($string, 3, $this->lexeropts);
        $this->lexer->symbols  =& $this->symbols;
        $this->lexer->comments =& $this->comments;
        $this->lexer->quotes   =& $this->quotes;
    }

    // {{{ function setDialect($dialect)
    /**
    * loads SQL dialect specific data
    *
    * @param string $dialect the SQL dialect to use
    * @return mixed true on success, otherwise Error
    * @uses  in_array()
    * @uses  EcrSqlParser::$dialects   R
    * @uses  EcrSqlParser::$types      W to set it
    * @uses  EcrSqlParser::$functions  W to set it
    * @uses  EcrSqlParser::$operators  W to set it
    * @uses  EcrSqlParser::$commands   W to set it
    * @uses  EcrSqlParser::$synonyms   W to set it
    * @uses  EcrSqlParser::$symbols    W to set it
    * @uses  EcrSqlParser::$lexeropts  W to set it
    * @uses  EcrSqlParser::$parseropts W to set it
    * @uses  EcrSqlParser::raiseError()
    */
    public function setDialect($dialect)
    {
        if (! in_array($dialect, EcrSqlParser::$dialects)) {
            throw new Exception('Unknown SQL dialect:' . $dialect);
        }

        include dirname(__FILE__). '/Parser/Dialect/' . $dialect . '.php';
        $this->types      = array_flip($dialect['types']);
        $this->functions  = array_flip($dialect['functions']);
        $this->operators  = array_flip($dialect['operators']);
        $this->commands   = array_flip($dialect['commands']);
        $this->synonyms   = $dialect['synonyms'];
        $this->symbols    = array_merge(
        $this->types,
        $this->functions,
        $this->operators,
        $this->commands,
        array_flip($dialect['reserved']),
        array_flip($dialect['conjunctions']));
        $this->lexeropts  = $dialect['lexeropts'];
        $this->parseropts = $dialect['parseropts'];
        $this->comments   = $dialect['comments'];
        $this->quotes     = $dialect['quotes'];

        return true;
    }
    // }}}

    // {{{ getParams(&$values, &$types)
    /**
    * extracts parameters from a function call
    *
    * this function should be called if an opening brace is found,
    * so the first call to $this->getTok() will return first param
    * or the closing )
    *
    * @param array   &$values to set it
    * @param array   &$types  to set it
    * @param integer $i       position
    * @return mixed true on success, otherwise Error
    * @uses  EcrSqlParser::$token  R
    * @uses  EcrSqlParser::$lexer  R
    * @uses  EcrSqlParser::getTok()
    * @uses  EcrSqlParser::isVal()
    * @uses  EcrSqlParser::raiseError()
    * @uses  SQL_Parser_Lexer::$tokText R
    */
    public function getParams(&$values, &$types, $i = 0)
    {
        $values = array();
        $types  = array();

        // the first opening brace is already fetched
        // function(
        $open_braces = 1;

        while ($open_braces > 0) {
            $this->getTok();

            if ($this->token === ')') {
                $open_braces--;
            } elseif ($this->token === '(') {
                $open_braces++;
            } elseif ($this->token === ',') {
                $i++;
            } elseif (isset($values[$i])) {
                $values[$i] .= '' . $this->lexer->tokText;
                $types[$i]  .= $this->token;
            } else {
                $values[$i] = $this->lexer->tokText;
                $types[$i]  = $this->token;
            }
        }

        return true;
    }
    // }}}

    // {{{ raiseError($message)
    /**
    *
    * @param string $message error message
    * @return Error
    * @uses  is_null()
    * @uses  substr()
    * @uses  strlen()
    * @uses  str_repeat()
    * @uses  abs()
    * @uses  EcrSqlParser::$lexer      R
    * @uses  EcrSqlParser::$token      R
    * @uses  SQL_Parser_Lexer::$string   R
    * @uses  SQL_Parser_Lexer::$lineBegin R
    * @uses  SQL_Parser_Lexer::$stringLen R
    * @uses  SQL_Parser_Lexer::$lineNo   R
    * @uses  SQL_Parser_Lexer::$tokText  R
    * @uses  SQL_Parser_Lexer::$tokPtr   R
    */
    public function raiseError($message)
    {
        $end = 0;
        if ($this->lexer->string != '') {
            while ($this->lexer->lineBegin + $end < $this->lexer->stringLen
            && $this->lexer->string{$this->lexer->lineBegin + $end} != "\n") {
                $end++;
            }
        }

        $message = 'Parse error: ' . $message . ' on line ' .
        ($this->lexer->lineNo + 1) . "\n";
        $message .= substr($this->lexer->string, $this->lexer->lineBegin, $end);
        $message .= "\n";
        $length   = is_null($this->token) ? 0 : strlen($this->lexer->tokText);
        $message .= str_repeat(' ', abs($this->lexer->tokPtr -
        $this->lexer->lineBegin - $length)) . "^";
        $message .= ' found: "' . $this->lexer->tokText . '"';

        throw new Exception($message);
    }
    // }}}

    // {{{ isType()
    /**
    * Returns true if current token is a variable type name, otherwise false
    *
    * @uses  EcrSqlParser::$types  R
    * @uses  EcrSqlParser::$token  R
    * @return  boolean  true if current token is a variable type name
    */
    public function isType()
    {
        return isset($this->types[$this->token]);
    }
    // }}}

    // {{{ isVal()
    /**
    * Returns true if current token is a value, otherwise false
    *
    * @uses  EcrSqlParser::$token  R
    * @return  boolean  true if current token is a value
    */
    public function isVal()
    {
        return ($this->token == 'real_val' ||
        $this->token == 'int_val' ||
        $this->token == 'text_val' ||
        $this->token == 'null');
    }
    // }}}

    // {{{ isFunc()
    /**
    * Returns true if current token is a function, otherwise false
    *
    * @uses  EcrSqlParser::$token  R
    * @uses  EcrSqlParser::$functions R
    * @return  boolean  true if current token is a function
    */
    public function isFunc()
    {
        return isset($this->functions[$this->token]);
    }
    // }}}

    // {{{ isCommand()
    /**
    * Returns true if current token is a command, otherwise false
    *
    * @uses  EcrSqlParser::$token  R
    * @uses  EcrSqlParser::$commands R
    * @return  boolean  true if current token is a command
    */
    public function isCommand()
    {
        return isset($this->commands[$this->token]);
    }
    // }}}

    // {{{ isReserved()
    /**
    * Returns true if current token is a reserved word, otherwise false
    *
    * @uses  EcrSqlParser::$token  R
    * @uses  EcrSqlParser::$symbols R
    * @return  boolean  true if current token is a reserved word
    */
    public function isReserved()
    {
        return isset($this->symbols[$this->token]);
    }
    // }}}

    // {{{ isOperator()
    /**
    * Returns true if current token is an operator, otherwise false
    *
    * @uses  EcrSqlParser::$token  R
    * @uses  EcrSqlParser::$operators R
    * @return  boolean  true if current token is an operator
    */
    public function isOperator()
    {
        return isset($this->operators[$this->token]);
    }
    // }}}

    // {{{ getTok()
    /**
    * retrieves next token
    *
    * @uses  EcrSqlParser::$token  W to set it
    * @uses  EcrSqlParser::$lexer  R
    * @uses  SQL_Parser_Lexer::lex()
    * @return void
    */
    public function getTok()
    {
        $this->token = $this->lexer->lex();
        //echo $this->token . "\t" . $this->lexer->tokText . "\n";
    }
    // }}}

    // {{{ &parseFieldOptions()
    /**
    * Parses field/column options, usually  for an CREATE or ALTER TABLE statement
    *
    * @uses  EcrSqlParser::$token
    * @uses  EcrSqlParser::getTok()
    * @uses  EcrSqlParser::raiseError()
    * @uses  EcrSqlParser::$lexer
    * @uses  SQL_Parser_Lexer::$tokText
    * @uses  SQL_Parser_Lexer::unget()
    * @uses  EcrSqlParser::isVal()
    * @uses  EcrSqlParser::isFunc()
    * @uses  EcrSqlParser::parseFunctionOpts()
    * @uses  EcrSqlParser::parseCondition()
    * @return  array   parsed field options
    */
    public function parseFieldOptions()
    {
        // parse field options
        $namedConstraint = false;
        $options         = array();
        while ($this->token != ',' && $this->token != ')' && $this->token != null ) {
            $option    = $this->token;
            $haveValue = true;
            switch ($option) {
                case 'constraint':
                    $this->getTok();
                    if ($this->token != 'ident') {
                        $this->raiseError('Expected a constraint name');
                    }
                    $constraintName = $this->lexer->tokText;
                    $namedConstraint = true;
                    $haveValue = false;
                    $this->getTok();
                    break;
                case 'default':
                    $this->getTok();
                    if ($this->isVal()) {
                        $constraintOpts = array(
                            'type' => 'default_value',
                            'value' => $this->lexer->tokText,
                        );
                        $this->getTok();
                    } elseif ($this->isFunc()) {
                        $results = $this->parseFunctionOpts();
                        if (false === $results) {
                            return $results;
                        }
                        $results['type'] = 'default_function';
                        $constraintOpts  = $results;
                    } else {
                        $this->raiseError('Expected default value');
                    }

                    break;
                case 'primary':
                    $this->getTok();
                    if ($this->token != 'key') {
                        $this->raiseError('Expected "key"');
                    }
                    $constraintOpts = array(
                        'type'  => 'primary_key',
                        'value' => true,
                    );
                    $this->getTok();
                    break;
                case 'not':
                    $this->getTok();
                    if ($this->token != 'null') {
                        $this->raiseError('Expected "null"');
                    }
                    $constraintOpts = array(
                        'type'  => 'not_null',
                        'value' => true,
                    );
                    $this->getTok();
                    break;
                case 'check':
                    $this->getTok();
                    if ($this->token != '(') {
                        $this->raiseError('Expected (');
                    }

                    $this->getTok();
                    $results = $this->parseCondition();
                    if (false === $results) {
                        return $results;
                    }

                    $results['type'] = 'check';
                    $constraintOpts  = $results;
                    if ($this->token != ')') {
                        $this->raiseError('Expected )');
                    }
                    $this->getTok();
                    break;
                case 'unique':
                    $this->getTok();
                    if ($this->token != '(') {
                        $this->raiseError('Expected (');
                    }

                    $constraintOpts = array('type'=>'unique');
                    $this->getTok();
                    while ($this->token != ')') {
                        if ($this->token != 'ident') {
                            $this->raiseError('Expected an identifier');
                        }
                        $constraintOpts['column_names'][] = $this->lexer->tokText;
                        $this->getTok();
                        if ($this->token != ')' && $this->token != ',') {
                            $this->raiseError('Expected ) or ,');
                        }
                    }

                    if ($this->token != ')') {
                        $this->raiseError('Expected )');
                    }
                    $this->getTok();
                    break;
                case 'month':
                case 'year':
                case 'day':
                case 'hour':
                case 'minute':
                case 'second':
                    $intervals = array(
                    array(
                            'month' => 0,
                            'year'  => 1,
                    ),
                    array(
                            'second' => 0,
                            'minute' => 1,
                            'hour'   => 2,
                            'day'    => 3,
                    )
                    );

                    foreach ($intervals as $class) {
                        if (isset($class[$option])) {
                            $constraintOpts = array(
                            	'quantum_1' => $this->token,
                            );
                            $this->getTok();
                            if ($this->token == 'to') {
                                $this->getTok();
                                if (! isset($class[$this->token])) {
                                    $this->raiseError(
                                        'Expected interval quanta');
                                }

                                if ($class[$this->token] >=
                                $class[$constraintOpts['quantum_1']]
                                ) {
                                    $this->raiseError($this->token
                                    . ' is not smaller than ' .
                                    $constraintOpts['quantum_1']);
                                }
                                $constraintOpts['quantum_2'] = $this->token;
                            } else {
                                $this->lexer->unget();
                            }
                            $this->getTok();
                            break;
                        }
                    }
                    if (!isset($constraintOpts['quantum_1'])) {
                        $this->raiseError('Expected interval quanta');
                    }
                    $constraintOpts['type'] = 'values';
                    $this->getTok();
                    break;
                case 'null':
                    $haveValue = false;
                    $this->getTok();
                    break;
                case 'auto_increment':
                    $constraintOpts = array(
                        'type'  => 'auto_increment',
                        'value' => true,
                    );
                    $this->getTok();
                    break;
                case 'unsigned':
                    $constraintOpts = array(
                        'type'  => 'unsigned',
                        'value' => true,
                    );
                    $this->getTok();
                    break;
                case 'text_val': // ???
                    $constraintOpts = array(
                        'type'  => 'comment',
                        'value' => $this->lexer->tokText,
                    );
                    $this->getTok();
                    break;
                case 'ident': // ???
                    $constraintOpts = array();
                    $this->getTok();
                    break;
                case 'commentx':
                    $this->getTok();
                    $this->getTok();
                    $this->getTok();
                    //continue;
                    break;

                default:
                    $this->raiseError('Unexpected token '
                    . $this->token . ': "' . $this->lexer->tokText . '"');
            }

            if ($haveValue) {
                if ($namedConstraint) {
                    $options['constraints'][$constraintName] = $constraintOpts;
                    $namedConstraint = false;
                } else {
                    $options['constraints'][] = $constraintOpts;
                }
            }

        }
        return $options;
    }
    // }}}

    // {{{ parseCondition()
    /**
    * parses conditions usually used in WHERE or ON
    *
    * @return  array   parsed condition
    * @uses  EcrSqlParser::$token
    * @uses  EcrSqlParser::$lexer
    * @uses  EcrSqlParser::getTok()
    * @uses  EcrSqlParser::raiseError()
    * @uses  EcrSqlParser::getParams()
    * @uses  EcrSqlParser::isFunc()
    * @uses  EcrSqlParser::parseFunctionOpts()
    * @uses  EcrSqlParser::parseCondition()
    * @uses  EcrSqlParser::isReserved()
    * @uses  EcrSqlParser::isOperator()
    * @uses  EcrSqlParser::parseSelect()
    * @uses  SQL_Parser_Lexer::$tokText
    * @uses  SQL_Parser_Lexer::unget()
    * @uses  SQL_Parser_Lexer::pushBack()
    */
    public function parseCondition()
    {
        $clause = array();

        while (true) {
            // parse the first argument
            if ($this->token == 'not') {
                $clause['neg'] = true;
                $this->getTok();
            }

            if ($this->token == '(') {
                $this->getTok();
                $clause['args'][] = $this->parseCondition();
                if ($this->token != ')') {
                    $this->raiseError('Expected ")"');
                }
                $this->getTok();
            } elseif ($this->isFunc()) {
                $result = $this->parseFunctionOpts();
                if (false === $result) {
                    return $result;
                }
                $clause['args'][] = $result;
            } elseif ($this->token == 'ident') {
                $clause['args'][] = $this->parseIdentifier();
            } else {
                $arg = $this->lexer->tokText;
                $argtype = $this->token;
                $clause['args'][] = array(
                    'value' => $arg,
                	'type'  => $argtype,
                );
                $this->getTok();
            }

            if (! $this->isOperator()) {
                // no operator, return
                return $clause;
            }

            // parse the operator
            $op = $this->token;
            if ($op == 'not') {
                $this->getTok();
                $not = 'not ';
                $op = $this->token;
            } else {
                $not = '';
            }

            $this->getTok();
            switch ($op) {
                case 'is':
                    // parse for 'is' operator
                    if ($this->token == 'not') {
                        $op .= ' not';
                        $this->getTok();
                    }
                    $clause['ops'][] = $op;
                    break;
                case 'like':
                    $clause['ops'][] = $not . $op;
                    break;
                case 'between':
                    // @todo
                    //$clause['ops'][] = $not . $op;
                    //$this->getTok();
                    break;
                case 'in':
                    // parse for 'in' operator
                    if ($this->token != '(') {
                        $this->raiseError('Expected "("');
                    }

                    // read the subset
                    $this->getTok();
                    // is this a subselect?
                    if ($this->token == 'select') {
                        $clause['args'][] = $this->parseSelect(true);
                    } else {
                        $this->lexer->pushBack();
                        // parse the set
                        $result = $this->getParams($values, $types);
                        if (false === $result) {
                            return $result;
                        }
                        $clause['args'][] = array(
                            'values' => $values,
                        	'types'  => $types,
                        );
                    }
                    if ($this->token != ')') {
                        $this->raiseError('Expected ")"');
                    }
                    break;
                case 'and':
                case 'or':
                    $clause['ops'][] = $not . $op;
                    continue;
                    break;
                default:
                    $clause['ops'][] = $not . $op;
            }
            // next argument [with operator]
        }

        return $clause;
    }
    // }}}

    public function parseSelectExpression()
    {
        $clause = array();

        $this->getTok();
        while (true) {
            // parse the first argument
            if ($this->token == 'not') {
                $clause['neg'] = true;
                $this->getTok();
            }

            if ($this->token == '(') {
                $this->getTok();
                $clause['args'][] = $this->parseCondition();
                if ($this->token != ')') {
                    $this->raiseError('Expected ")"');
                }
                $this->getTok();
            } elseif ($this->isFunc()) {
                $result = $this->parseFunctionOpts();
                if (false === $result) {
                    return $result;
                }
                $clause['args'][] = $result;
            } elseif ($this->token == 'ident') {
                $clause['args'][] = $this->parseIdentifier();
            } else {
                $arg = $this->lexer->tokText;
                $argtype = $this->token;
                $clause['args'][] = array(
                    'value' => $arg,
                	'type'  => $argtype,
                );
                $this->getTok();
            }

            if (! $this->isOperator()) {
                // no operator, return
                return $clause;
            }

            // parse the operator
            $op = $this->token;
            if ($op == 'not') {
                $this->getTok();
                $not = 'not ';
                $op = $this->token;
            } else {
                $not = '';
            }

            $this->getTok();
            switch ($op) {
                case 'is':
                    // parse for 'is' operator
                    if ($this->token == 'not') {
                        $op .= ' not';
                        $this->getTok();
                    }
                    $clause['ops'][] = $op;
                    break;
                case 'like':
                    $clause['ops'][] = $not . $op;
                    break;
                case 'between':
                    // @todo
                    //$clause['ops'][] = $not . $op;
                    //$this->getTok();
                    break;
                case 'in':
                    // parse for 'in' operator
                    if ($this->token != '(') {
                        $this->raiseError('Expected "("');
                    }

                    // read the subset
                    $this->getTok();
                    // is this a subselect?
                    if ($this->token == 'select') {
                        $clause['args'][] = $this->parseSelect(true);
                    } else {
                        $this->lexer->pushBack();
                        // parse the set
                        $result = $this->getParams($values, $types);
                        if (false === $result) {
                            return $result;
                        }
                        $clause['args'][] = array(
                            'values' => $values,
                        	'types'  => $types,
                        );
                    }
                    if ($this->token != ')') {
                        $this->raiseError('Expected ")"');
                    }
                    break;
                case 'and':
                case 'or':
                    $clause['ops'][] = $not . $op;
                    continue;
                    break;
                default:
                    $clause['ops'][] = $not . $op;
            }
            // next argument [with operator]
        }

        return $clause;
    }

    // {{{ parseFieldList()
    /**
    * @access  public
    * @return mixed array parsed field list on success, otherwise Error
    */
    public function parseFieldList()
    {
        $fields = array();

        $this->getTok();
        if ($this->token != '(') {
            $this->raiseError('Expected (');
        }

        while (1) {
            // parse field identifier
            $this->getTok();
            if ($this->token == ',') {
                continue;
            }
            // In this context, field names can be reserved words or function names
            if ($this->token == 'primary'
            || $this->token == 'unique') {
                $this->getTok();
                if ($this->token != 'key') {
                    $this->raiseError('Expected key');
                }
                $this->getTok();
                if ($this->token != '(') {
                    $this->raiseError('Expected (');
                }
                $this->getTok();
                if ($this->token != 'ident') {
                    $this->raiseError('Expected identifier');
                }
                $name = $this->lexer->tokText;
                $this->getTok();
                if ($this->token != ')') {
                    $this->raiseError('Expected )');
                }
                $fields[$name]['constraints'][] = array(
                    'type'  => 'primary_key',
                    'value' => true,
                );
                continue;
            } elseif ($this->token == 'key') {
                $this->getTok();
                if ($this->token != 'ident') {
                    $this->raiseError('Expected identifier');
                }
                $key = $this->lexer->tokText;
                $this->getTok();
                if ($this->token != '(') {
                    $this->raiseError('Expected (');
                }
                $this->getTok();
                $rows = array();
                while ($this->token != ')')
                {
                    if ($this->token != 'ident'
                    && $this->token != ',')
                    {
                        $this->raiseError('Expected identifier');
                    }

                    $name= $this->lexer->tokText;
                    $fields[$name]['constraints'][] = array(
                        'type'  => 'key',
                        'value' => $key,
                    );
                    $this->getTok();
                }
                continue;
            } elseif ($this->token == 'ident' || $this->isFunc()
            || $this->isReserved()) {
                $name = $this->lexer->tokText;
            } elseif ($this->token == ')') {
                return $fields;
            } else {
                //$this->raiseError('Expected identifier');
            }

            // parse field type
            $this->getTok();
            if (! $this->isType($this->token)) {
                $this->raiseError('Expected a valid type');
            }
            $type = $this->token;

            $this->getTok();
            // handle special case two-word types
            if ($this->token == 'precision') {
                // double precision == double
                if ($type == 'double') {
                    $this->raiseError('Unexpected token');
                }
                $this->getTok();
            } elseif ($this->token == 'varying') {
                // character varying() == varchar()
                if ($type != 'character' && $type != 'varchar') {
                    $this->raiseError('Unexpected token');
                }
                $this->getTok();
            }
            $fields[$name]['type'] =(isset($this->synonyms[$type])) ? $this->synonyms[$type] : '';
            // parse type parameters
            if ($this->token == '(') {
                $results = $this->getParams($values, $types);
                if (false === $results) {
                    return $results;
                }
                switch ($fields[$name]['type']) {
                    case 'numeric':
                        if (isset($values[1])) {
                            if ($types[1] != 'int_val') {
                                $this->raiseError('Expected an integer');
                            }
                            $fields[$name]['decimals'] = $values[1];
                        }
                    case 'float':
                        if ($types[0] != 'int_val') {
                            $this->raiseError('Expected an integer');
                        }
                        $fields[$name]['length'] = $values[0];
                        break;
                    case 'char':
                    case 'varchar':
                    case 'integer':
                    case 'int':
                    case 'tinyint' :
                        if (sizeof($values) != 1) {
                            $this->raiseError('Expected 1 parameter');
                        }
                        if ($types[0] != 'int_val') {
                            $this->raiseError('Expected an integer');
                        }
                        $fields[$name]['length'] = $values[0];
                        break;
                    case 'set':
                    case 'enum':
                        if (! sizeof($values)) {
                            $this->raiseError('Expected a domain');
                        }
                        $fields[$name]['domain'] = $values;
                        break;
                    default:
                        if (sizeof($values)) {
                            $this->raiseError('Unexpected )');
                        }
                }
                $this->getTok();
            }

            $options = $this->parseFieldOptions();
            if (false === $options) {
                return $options;
            }

            $fields[$name] += $options;

            if ($this->token == ')') {
                return $fields;
            } elseif ($this->token == ';' || is_null($this->token)) {
                return $fields;
            }
        }

        return $fields;
    }
    // }}}

    // {{{ parseFunctionOpts()
    /**
    * Parses parameters in a function call
    *
    * @access  public
    * @return mixed array parsed function options on success, otherwise Error
    */
    public function parseFunctionOpts()
    {
        $function     = $this->token;
        $opts['name'] = $function;
        $this->getTok();
        if ($this->token != '(') {
            $this->raiseError('Expected "("');
        }

        $this->getParams($opts['arg'], $opts['type']);
        if ($this->token != ')') {
            $this->raiseError('Expected ")"');
        }
        $this->getTok();

        return $opts;
    }
    // }}}

    // {{{ parseCreate()
    /**
    * @access  public
    * @return mixed array parsed create on success, otherwise Error
    */
    public function parseCreate()
    {
        $tree = array();

        $this->getTok();
        switch ($this->token) {
            case 'table':
                $tree['command'] = 'create_table';
                $this->getTok();
                if ($this->token != 'ident')
                {
                    if('if' == $this->token)
                    {
                        $this->getTok();
                        $this->getTok();
                        $this->getTok();
                        if ($this->token != 'ident') {
                            $this->raiseError('Expected table name');
                        }
                    }
                    else
                    {
                        $this->raiseError('Expected table name');
                    }
                }

                $tree['table_names'][] = $this->lexer->tokText;
                $fields = $this->parseFieldList();
                if (false === $fields) {
                    return $fields;
                }
                $tree['column_defs'] = $fields;
                // $tree['column_names'] = array_keys($fields);
                break;
            case 'index':
                $tree['command'] = 'create_index';
                break;
            case 'constraint':
                $tree['command'] = 'create_constraint';
                break;
            case 'sequence':
                $tree['command'] = 'create_sequence';
                break;
            default:
                $this->raiseError('Unknown object to create');
        }

        $this->getTok();
        return $tree;
    }
    // }}}

    // {{{ parseInsert()
    // INSERT INTO tablename
    /**
    * @access  public
    * @return mixed array parsed insert on success, otherwise Error
    */
    public function parseInsert()
    {
        $this->getTok();
        if ($this->token != 'into') {
            $this->raiseError('Expected "into"');
        }
        $tree = array('command' => 'insert');

        $this->getTok();
        if ($this->token != 'ident') {
            $this->raiseError('Expected table name');
        }
        $tree['table_names'][] = $this->lexer->tokText;

        $this->getTok();
        if ($this->token == '(') {
            $results = $this->getParams($values, $types);
            if (false === $results) {
                return $results;
            } elseif (sizeof($values)) {
                $tree['column_names'] = $values;
            }
            $this->getTok();
        }

        if ($this->token != 'values') {
            $this->raiseError('Expected "values"');
        }

        // loop over all (value[, ...])[,(value[, ...]), ...]
        while (1) {
            // get opening brace '('
            $this->getTok();
            if ($this->token != '(') {
                $this->raiseError('Expected "("');
            }
            $results = $this->getParams($values, $types);
            if (false === $results) {
                return $results;
            }
            if (isset($tree['column_defs'])
            && sizeof($tree['column_defs']) != sizeof($values)) {
                $this->raiseError('field/value mismatch');
            }
            if (! sizeof($values)) {
                $this->raiseError('No fields to insert');
            }
            foreach ($values as $key => $value) {
                $values[$key] = array(
                    'value' => $value,
                    'type'  => $types[$key],
                );
            }
            $tree['values'][] = $values;

            $this->getTok();
            if ($this->token != ',') {
                return $tree;
            }
        }
    }
    // }}}

    // {{{ parseUpdate()
    /**
    * UPDATE tablename SET (colname = (value|colname) (,|WHERE searchclause))+
    *
    * @todo This is incorrect.  multiple where clauses would parse
    * @access  public
    * @return mixed array parsed update on success, otherwise Error
    */
    public function parseUpdate()
    {
        $tree = array('command' => 'update');
        $this->getTok();
        $tree['tables'][] = $this->parseIdentifier('table');

        if ($this->token != 'set') {
            $this->raiseError('Expected "set"');
        }

        while (true) {
            $this->getTok();
            $set['column'] = $this->parseIdentifier();

            if ($this->token != '=') {
                $this->raiseError('Expected =');
            }

            $this->getTok();
            $set['column'] = $this->parseCondition();

            $tree['sets'][] = $set;

            if ($this->token != ',') {
                break;
            }
        }

        if ($this->token == 'from') {
            $this->getTok();
            $tree['from'] = $this->parseFrom();
        }

        if ($this->token == 'where') {
            $this->getTok();
            $clause = $this->parseCondition();
            if (false === $clause) {
                return $clause;
            }
            $tree['where_clause'] = $clause;
        }

        return $tree;
    }
    // }}}

    public function parseTableFactor()
    {
        if ($this->token == '(') {
            $this->getTok();
            $tree = $this->parseTableReference();
            // closing )
            $this->getTok();
            return $tree;
        } elseif ($this->token == 'select') {
            return $this->parseSelect();
        } else {
            return $this->parseIdentifier('table');
        }
    }

    public function parseTableReference()
    {
        $tree = array();

        while (true) {
            $tree['table_factors'][] = $this->parseTableFactor();

            // join condition
            if ($this->token == 'on') {
                $this->getTok();
                $clause = $this->parseCondition();
                if (false === $clause) {
                    return $clause;
                }
                $tree['table_join_clause'][] = $clause;
            }

            // joins LEFT|RIGHT|INNER|OUTER|NATURAL|CROSS|STRAIGHT_JOIN
            if ($this->token == ',') {
                $tree['table_join'][] = $this->token;
                $this->getTok();
            } elseif ($this->token == 'straight_join') {
                $tree['table_join'][] = $this->token;
                $this->getTok();
            } elseif ($this->token == 'join') {
                $tree['table_join'][] = $this->token;
                $this->getTok();
            } elseif ($this->token == 'cross'
            || $this->token == 'inner') {
                // (CROSS|INNER) JOIN
                $join = $this->token;
                $this->getTok();
                if ($this->token != 'join') {
                    $this->raiseError('Expected token "join"');
                }
                $tree['table_join'][] = $join.' join';
                $this->getTok();
            } elseif ($this->token == 'left'
            || $this->token == 'right') {
                // {LEFT|RIGHT} [OUTER] JOIN
                $join = $this->token;

                $this->getTok();
                if ($this->token == 'outer') {
                    $join .= ' outer';
                    $this->getTok();
                }

                if ($this->token != 'join') {
                    $this->raiseError('Expected token "join"');
                }
                $tree['table_join'][] = $join.' join';

                $this->getTok();
            } elseif ($this->token == 'natural') {
                // NATURAL [{LEFT|RIGHT} [OUTER]] JOIN
                $join = $this->token;

                $this->getTok();
                if (($this->token == 'left')
                || ($this->token == 'right')) {
                    $join .= ' ' . $this->token;
                    $this->getTok();
                }

                if ($this->token == 'outer') {
                    $join .= ' ' . $this->token;
                    $this->getTok();
                }

                if ($this->token == 'join') {
                    $tree['table_join'][] = $join.' join';
                } else {
                    $this->raiseError('Expected token "join"');
                }
                $this->getTok();
            } else {
                break;
            }
        }

        return $tree;
    }

    public function parseFrom()
    {
        $tree = array();

        $tree['table_references'] = $this->parseTableReference();

        return $tree;
    }

    // {{{ parseDelete()
    /**
    * DELETE FROM tablename WHERE searchclause
    *
    * @access  public
    * @return mixed array parsed delete on success, otherwise Error
    */
    public function parseDelete()
    {
        $tree = array('command' => 'delete');

        $this->getTok();
        if ($this->token == 'from') {
            // FROM is not required
            $this->getTok();
        }

        if ($this->token != 'ident') {
            $this->raiseError('Expected a table name');
        }
        $tree['table_names'][] = $this->lexer->tokText;

        $this->getTok();
        if ($this->token == 'where') {
            // WHERE is not required
            $this->getTok();
            $clause = $this->parseCondition();
            if (false === $clause) {
                return $clause;
            }
            $tree['where_clause'] = $clause;
        }

        return $tree;
    }
    // }}}

    // {{{ parseDrop()
    /**
    * @access  public
    * @return mixed array parsed drop on success, otherwise Error
    */
    public function parseDrop()
    {
        $this->getTok();
        switch ($this->token) {
            case 'table':
                $tree = array('command' => 'drop_table');
                $this->getTok();
                if ($this->token != 'ident') {
                    $this->raiseError('Expected a table name');
                }
                $tree['table_names'][] = $this->lexer->tokText;

                $this->getTok();
                if ($this->token == 'restrict'
                || $this->token == 'cascade')
                {
                    $tree['drop_behavior'] = $this->token;
                    $this->getTok();
                }
                return $tree;
                break;
            case 'index':
                $tree = array('command' => 'drop_index');
                break;
            case 'constraint':
                $tree = array('command' => 'drop_constraint');
                break;
            case 'sequence':
                $tree = array('command' => 'drop_sequence');
                break;
            default:
                $this->raiseError('Unknown object to drop');
        }
        return $tree;
    }
    // }}}

    /**
     * [[db.].table].column [[AS] alias]
     */
    public function parseIdentifier($type = 'column')
    {
        $ident = array(
            'database' => '',
            'table'    => '',
            'column'   => '',
            'alias'    => '',
        );

        $ident['column'] = $this->lexer->tokText;
        $prevTok = $this->token;

        $this->getTok();
        if ($this->token == '.') {
            $this->getTok();
            $prevTok = $this->token;
            $ident['table']  = $ident['column'];
            $ident['column'] = $this->lexer->tokText;
            $this->getTok();
            if ($this->token == '.') {
                $this->getTok();
                $prevTok = $this->token;
                $ident['database'] = $ident['table'];
                $ident['table']    = $ident['column'];
                $ident['column']   = $this->lexer->tokText;
                $this->getTok();
            }
        }

        if ($prevTok != 'ident' && $prevTok != '*') {
            $this->raiseError('Expected name');
        }

        if ($type === 'table') {
            $ident['database'] = $ident['table'];
            $ident['table']    = $ident['column'];
            unset($ident['column']);
        }

        if ($this->token == 'as') {
            $this->getTok();
            if ($this->token != 'ident' ) {
                $this->raiseError('Expected column alias');
            }
            $ident['alias'] = $this->lexer->tokText;
            $this->getTok();
        } elseif ($this->token == 'ident') {
            $ident['alias'] = $this->lexer->tokText;
            $this->getTok();
        }

        return $ident;
    }

    // {{{ parseSelect()
    /**
    * @access  public
    * @return mixed array parsed select on success, otherwise Error
    */
    public function parseSelect($subSelect = false)
    {
        $tree = array('command' => 'select');
        $this->getTok();
        if ($this->token == 'distinct' || $this->token == 'all') {
            $tree['set_quantifier'] = $this->token;
            $this->getTok();
        }

        while (1) {
            $tree['select_expressions'][] = $this->parseCondition();
            if ($this->token != ',') {
                break;
            }
            $this->getTok();
        }

        // FROM
        if ($this->token != 'from') {
            return $tree;
        }

        $this->getTok();
        $tree['from'] = $this->parseFrom();

        // WHERE

        // GROUP BY

        // HAVING

        // ORDER BY

        // LIMIT

        // UNION
        while ($this->token != ';' && ! is_null($this->token) && (!$subSelect || $this->token != ')')
        && $this->token != ')') {
            switch ($this->token) {
                case 'where':
                    $this->getTok();
                    $clause = $this->parseCondition();
                    if (false === $clause) {
                        return $clause;
                    }
                    $tree['where_clause'] = $clause;
                    break;
                case 'order':
                    $this->getTok();
                    if ($this->token != 'by') {
                        $this->raiseError('Expected "by"');
                    }
                    $this->getTok();
                    while ($this->token == 'ident') {
                        $arg = $this->lexer->tokText;
                        $this->getTok();
                        if ($this->token == '.') {
                            $this->getTok();
                            if ($this->token == 'ident') {
                                $arg .= '.'.$this->lexer->tokText;
                            } else {
                                $this->raiseError('Expected a column name');
                            }
                        } else {
                            $this->lexer->pushBack();
                        }
                        $col = $arg;
                        //$col = $this->lexer->tokText;
                        $this->getTok();
                        if (isset($this->synonyms[$this->token])) {
                            $order = $this->synonyms[$this->token];
                            if (($order != 'asc') && ($order != 'desc')) {
                                $this->raiseError('Unexpected token');
                            }
                            $this->getTok();
                        } else {
                            $order = 'asc';
                        }
                        if ($this->token == ',') {
                            $this->getTok();
                        }
                        $tree['sort_order'][$col] = $order;
                    }
                    break;
                case 'limit':
                    $this->getTok();
                    if ($this->token != 'int_val') {
                        $this->raiseError('Expected an integer value');
                    }
                    $length = $this->lexer->tokText;
                    $start = 0;
                    $this->getTok();
                    if ($this->token == ',') {
                        $this->getTok();
                        if ($this->token != 'int_val') {
                            $this->raiseError('Expected an integer value');
                        }
                        $start  = $length;
                        $length = $this->lexer->tokText;
                        $this->getTok();
                    }
                    $tree['limit_clause'] = array('start'=>$start,
                    'length'=>$length);
                    break;
                case 'group':
                    $this->getTok();
                    if ($this->token != 'by') {
                        $this->raiseError('Expected "by"');
                    }
                    $this->getTok();
                    while ($this->token == 'ident') {
                        $arg = $this->lexer->tokText;
                        $this->getTok();
                        if ($this->token == '.') {
                            $this->getTok();
                            if ($this->token == 'ident') {
                                $arg .= '.'.$this->lexer->tokText;
                            } else {
                                $this->raiseError('Expected a column name');
                            }
                        } else {
                            $this->lexer->pushBack();
                        }
                        $col = $arg;
                        //$col = $this->lexer->tokText;
                        $this->getTok();
                        if ($this->token == ',') {
                            $this->getTok();
                        }
                        $tree['group_by'][] = $col;
                    }
                    break;
                default:
                    $this->raiseError('Unexpected clause');
            }
        }
        return $tree;
    }
    // }}}

    /**
     * tbl_name [[AS] alias] lock_type[, ...]
     */
    public function parseLock()
    {
        $tree = array('command' => 'lock tables');

        $this->getTok();
        if ($this->token != 'tables') {
            $this->raiseError('Expected tables');
        }

        while(1) {
            $this->getTok();
            $table = $this->parseIdentifier('table');
            if (false === $table) {
                return $table;
            }

            $lock = $this->parseLockType();
            if (false === $lock) {
                return $lock;
            }

            $lock['table'] = $table;
            $tree['locks'][] = $lock;

            if ($this->token != ',') {
                return $tree;
            }
        }
    }

    /**
     * READ [LOCAL] | [LOW_PRIORITY] WRITE
     */
    public function parseLockType()
    {
        $tree = array();

        if ($this->token == 'read') {
            $tree['type'] = $this->token;
            $this->getTok();
            if ($this->token == 'local') {
                $tree['option'] = $this->token;
                $this->getTok();
            }
            return $tree;
        }

        if ($this->token == 'low_priority') {
            $tree['option'] = $this->token;
            $this->getTok();
        }

        if ($this->token == 'write') {
            $tree['type'] = $this->token;
            $this->getTok();
        } else {
            $this->raiseError('Expected READ [LOCAL] | [LOW_PRIORITY] WRITE');
        }

        return $tree;
    }

    // {{{ parse($string)
    /**
    *
    * @return  array   parsed data
    * @uses  EcrSqlParser::$lexeropts
    * @uses  EcrSqlParser::$lexer
    * @uses  EcrSqlParser::$symbols
    * @uses  EcrSqlParser::$token
    * @uses  EcrSqlParser::raiseError()
    * @uses  EcrSqlParser::getTok()
    * @uses  EcrSqlParser::parseSelect()
    * @uses  EcrSqlParser::parseUpdate()
    * @uses  EcrSqlParser::parseInsert()
    * @uses  EcrSqlParser::parseDelete()
    * @uses  EcrSqlParser::parseCreate()
    * @uses  EcrSqlParser::parseDrop()
    * @uses  SQL_Parser_Lexer
    * @uses  SQL_Parser_Lexer::$symbols
    * @access  public
    */
    public function parseQuery()
    {
        $tree = array();

        // get query action
        $this->getTok();
        while (1) {
            $branch = array();
            switch ($this->token) {
                case null:
                    // null == end of string
                    break;
                case 'select':
                    $branch = $this->parseSelect();
                    break;
                case 'update':
                    $branch = $this->parseUpdate();
                    break;
                case 'insert':
                    $branch = $this->parseInsert();
                    break;
                case 'delete':
                    $branch = $this->parseDelete();
                    break;
                case 'create':
                    $branch = $this->parseCreate();
                    break;
                case 'drop':
                    $branch = $this->parseDrop();
                    break;
                case 'unlock':
                    $this->getTok();
                    if ($this->token != 'tables') {
                        $this->raiseError('Expected tables');
                    }

                    $this->getTok();
                    $branch = array('command' => 'unlock tables');
                    break;
                case 'lock':
                    $branch = $this->parseLock();
                    break;
                case '(':
                    $branch[] = $this->parseQuery();
                    if ($this->token != ')') {
                        $this->raiseError('Expected )');
                    }
                    $this->getTok();
                    break;
                default:
                    $this->raiseError('Unknown action: ' . $this->token);
            }
            $tree[] = $branch;

            // another command separated with ; or a UNION
            if ($this->token == ';') {
                $tree[] = ';';
                $this->getTok();
                if (! is_null($this->token)) {
                    continue;
                }
            }

            // another command separated with ; or a UNION
            if ($this->token == 'UNION') {
                $tree[] = 'UNION';
                $this->getTok();
                continue;
            }

            // end? unknown?
            break;
        }

        return $tree;
    }

    public function parse($string = null)
    {
        try {
            if (is_string($string)) {
                $this->initLexer($string);
            } elseif (! $this->lexer instanceof SQL_Parser_Lexer) {
                throw new Exception('No initial string specified');
                return array('empty' => true);
            }
        } catch (Exception $e) {
            return 'Caught exception on init: ' . $e->getMessage() . "\n";
        }

        try {
            $tree = $this->parseQuery();
            if (! is_null($this->token)) {
                $this->raiseError('Expected EOQ');
            }
        } catch (Exception $e) {
            $tree = "\n";
            $tree .= 'Caught exception: ' . $e->getMessage() . "\n";
            $tree .= 'in: ' . $e->getFile() . '#' . $e->getLine() . "\n";
            $tree .= 'from: ' . "\n" . $e->getTraceAsString();
            $tree .= "\n";
        }

        return $tree;
    }
    // }}}
}
