<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers
 *
 *  == PHP FILE TREE ==
 *  Let's call it...oh, say...version 1?
 * @author    Cory S.N. LaViska {@link http://abeautifulsite.net/}
 * For documentation and updates, visit
 * @documentation {@link http://abeautifulsite.net/notebook.php?article=21}
 *
 * @version Let's call this one... version 2 =;)
 * @author    Nikolai Plath (elkuku)
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Draws a file tree.
 *
 * @package EasyCreator
 */
class EcrFileTree
{
    /**
     * @var integer
     */
    private $linkId = 0;

    private $directory = '';

    private $href = '';

    private $jsFolder = '';

    private $jsFile = '';

    private $extensionsOnly = array();

    private $extensionsExclude = array();

    public $showExtension = true;

    private $reverse = false;

    private $indent = 0;

    /**
     * Constructor.
     *
     * @param string     $directory The directory to draw the tree for
     * @param string     $href Link pattern
     * @param string     $jsFile JS pattern for files
     * @param string     $jsFolder JS pattern for folders
     * @param array|bool $extensionsOnly Positiv filter for extensions
     * @param boolean    $reverse Reverse order
     */
    public function __construct($directory = '', $href = '', $jsFile = '', $jsFolder = ''
    , $extensionsOnly = array(), $reverse = false)
    {
        $this->directory = $directory;
        $this->href = $href;
        $this->jsFile = $jsFile;
        $this->jsFolder = $jsFolder;

        $this->extensionsOnly = $extensionsOnly;
        $this->reverse = $reverse;
    }//function

    /**
     * Draw the tree.
     *
     * @return string
     */
    public function drawTree()
    {
        // Generates a valid XHTML list of all directories, sub-directories, and files in $directory
        // Remove trailing slash
        $d = $this->directory;

        if(substr($d, -1) == DS)
        {
            $d = substr($d, 0, strlen($d) - 1);
        }

        return $this->scanDir($d);
    }//function

    /**
     * Draw the full tree.
     *
     * @return string
     */
    public function drawFullTree()
    {
        $r = '';
        $r .= $this->startTree();
        $d = $this->directory;

        if(substr($d, -1) == DS)
        {
            $d = substr($d, 0, strlen($d) - 1);
        }

        $r .= $this->scanDir($d);
        $r .= $this->endTree();

        return $r;
    }//function

    /**
     * Set the directory.
     *
     * @param string $directory Path to directory
     *
     * @return EcrFileTree
     */
    public function setDir($directory)
    {
        $this->directory = $directory;

        return $this;
    }//function

    /**
     * Set the JavaScript pattern.
     *
     * @param string $type The type to set the pattern for e.g. file or folder
     * @param string $js The JavaScript pattern
     *
     * @return void
     */
    public function setJs($type, $js)
    {
        switch($type)
        {
            case 'folder':
                $this->jsFolder = $js;
                break;

            case 'file':
                $this->jsFile = $js;
                break;
        }//switch
    }//function

    /**
     * Draw only the start part of the tree.
     *
     * @return string
     */
    public function startTree()
    {
        $s = '';
        $s .= NL.'<!-- PHPFileTree Start -->';
        $s .= NL.'<div class="php-file-tree">';

        $this->indent = 0;

        return $s;
    }//function

    /**
     * Draw only the end part of the tree.
     *
     * @return string
     */
    public function endTree()
    {
        $s = '';
        $s .= NL.'</div>';
        $s .= NL.'<!-- PHPFileTree End -->'.NL;

        return $s;
    }//function

    /**
     * Recursive function to list directories/files.
     *
     * @param string $directory Path to directory
     *
     * @return string
     */
    private function scanDir($directory)
    {
        // Get and sort directories/files
        $entries = scandir($directory);
        natcasesort($entries);
        if($this->reverse)
        $entries = array_reverse($entries);

        // Make directories first
        $files = $dirs = array();

        foreach($entries as $this_file)
        {
            if(is_dir($directory.DS.$this_file))
            {
                $dirs[] = $this_file;
            }
            else
            {
                $files[] = $this_file;
            }
        }//foreach

        $entries = array_merge($dirs, $files);

        // Filter unwanted extensions
        if( ! empty($this->extensionsOnly))
        {
            foreach(array_keys($entries) as $key)
            {
                if( ! is_dir($directory.DS.$entries[$key]))
                {
                    $ext = substr($entries[$key], strrpos($entries[$key], '.') + 1);

                    if( ! in_array($ext, $this->extensionsOnly))
                    unset($entries[$key]);
                }
            }//foreach
        }

        $php_file_tree = '';

        if(count($entries) > 2)
        {
            // Use 2 instead of 0 to account for . and .. "directories"
            $php_file_tree .= $this->idtAdd();
            $php_file_tree .= '<ul class="unstyled">';
            $this->indent ++;

            foreach($entries as $this_file)
            {
                if($this_file != '.' && $this_file != '..' && $this_file != '.svn')
                {
                    if(is_dir($directory.DS.$this_file))
                    {
                        //-- Directory
                        $li =($directory == JPATH_ROOT) ? '' : str_replace(JPATH_ROOT, '', $directory);

                        $li = trim($li, DS);

                        if(substr($li, -1) != DS)
                        $li .= DS;

                        $d = $this_file;

                        if(strpos($d, '-'))
                        {
                            // @todo: what's that for ??
                            // $d = substr($d, strpos($d, '-') + 1);
                        }

                        $js = $this->parseLink($this->jsFolder, $li, $d);
                        $php_file_tree .= $this->idt();
                        $php_file_tree .= '<li class="pft-directory">';

                        if($this->href)
                        {
                            $php_file_tree .= '<a href="javascript:"'.$js.'>'.htmlspecialchars($d).'</a>';
                        }
                        else
                        {
                            $php_file_tree .= '<div'.$js.'>'.htmlspecialchars($d).'</div>';
                        }

                        //-- Recurse...
                        $php_file_tree .= $this->scanDir($directory.DS.$this_file);

                        $php_file_tree .= $this->idtDel();
                        $php_file_tree .= '</li>';
                    }
                    else
                    {
                        //-- File
                        $php_file_tree .= $this->getLink($directory, $this_file);
                    }
                }
            }//foreach

            $php_file_tree .= $this->idtDel();
            $php_file_tree .= '</ul>';
        }

        return $php_file_tree;
    }//function

    /**
     * Increase indent.
     *
     * @return string
     */
    private function idtAdd()
    {
        $this->indent ++;

        return NL.str_repeat('   ', $this->indent);
    }//function

    /**
     * Decrease indent.
     *
     * @return string
     */
    private function idtDel()
    {
        $this->indent --;

        if($this->indent < 0)
        $this->indent = 0;

        return NL.str_repeat('   ', $this->indent);
    }//function

    /**
     * Get indent.
     *
     * @return string
     */
    private function idt()
    {
        return NL.str_repeat('   ', $this->indent);
    }//function

    /**
     * Displays a link.
     *
     * @param string $folder Folder name
     * @param string $file File name
     *
     * @return string
     */
    public function getLink($folder, $file)
    {
        $li =($folder == JPATH_ROOT) ? '' : str_replace(JPATH_ROOT, '', $folder);

        $li = ltrim($li, DS);
        $li = ltrim($li, '/');//---WTF :|

        if(substr($li, -1) != DS)
        $li .= DS;

        $ext = 'ext-'.substr($file, strrpos($file, '.') + 1);

        $href = $this->parseLink($this->href, $li, $file);
        $js = $this->parseLink($this->jsFile, $li, $file);

        if( ! $this->showExtension)
            $file = JFile::stripExt($file);

        $s = '';
        $s .= $this->idt();
        $s .= '<li class="pft-file '.strtolower($ext).'" id="tl_'.$this->linkId.'" '.$js.'>';
        $s .= htmlspecialchars($file);
        $s .= '</li>';

        $this->linkId ++;

        return $s;
    }//function

    /**
     * Parse a link.
     *
     * @param string $string The string to parse
     * @param string $folder Folder name
     * @param string $file File name
     *
     * @return string
     */
    private function parseLink($string, $folder, $file)
    {
        //--Windoze stuff..
        $folder = str_replace('\\', '/', $folder);

        if(strrpos($folder, '/') == strlen($folder) - 1)
        {
            $folder = substr($folder, 0, strlen($folder) - 1);
        }

        $s = $string;
        $s = str_replace('[folder]', $folder, $s);
        $s = str_replace('[link]', $folder, $s);
        $s = str_replace('[file]', urlencode($file), $s);
        $s = str_replace('[id]', 'tl_'.$this->linkId, $s);

        return $s;
    }//function
}//class
