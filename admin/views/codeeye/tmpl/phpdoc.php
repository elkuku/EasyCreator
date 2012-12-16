<?php
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 30-Sep-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

$phpDoc = new EcrPearHelperPhpdoc;

if( ! $phpDoc->checkEnv()):
    echo 'Environment check failed.. cannot continue :(';

    return;
endif;

$dirs = array();
$files = array();

foreach($this->project->copies as $dir):
    if(is_dir(JPATH_ROOT.DS.$dir['source'])):
        $d = str_replace(JPATH_ROOT.DS, '', $dir);
        $dirs[] = $d;
    else:
        $d = str_replace(JPATH_ROOT.DS, '', $dir);
        $files[] = $d;
    endif;
endforeach;

$sDirs = implode(',', $dirs);
$sFiles = implode(',', $files);

$phpDoc->targetDir = JPATH_ROOT.DS.'documentation'.DS.$this->project->comName;

$options = array();
$options[] = JHtml::_('select.option', '', jgettext('Select...'));

foreach($phpDoc->converters as $key => $value):
    $options[] = JHtml::_('select.option', $key, $value);
endforeach;

$listConverters = JHTML::_('select.genericlist', $options, 'phpdoc_converter', null
, 'value', 'text', null, 'phpdoc_converter');
?>

<div class="ecr_floatbox">
    Target Dir: JROOT <?php echo DS; ?>&nbsp;
    <input type="text" id="target_dir" size="25" value="documentation<?php echo DS.$this->project->comName; ?>" />
    <br />
    Format:
    <?php echo $listConverters; ?>
</div>

<div class="ecr_floatbox">
<strong><?php echo jgettext('Options'); ?></strong>
<br />
<input type="checkbox" id="phpdoc_quiet"/><label for="phpdoc_quiet" > Quiet</label><br />
<input type="checkbox" id="phpdoc_undocumented"/><label for="phpdoc_undocumented" > Undocumented elements</label><br />
<input type="checkbox" id="phpdoc_sourcecode"/><label for="phpdoc_sourcecode" > Source code</label><br />
Parse private:<br />
<br />
Title:<br />
readmeinstallchangelog<br />
<input type="text" id="phpcpd_min_tokens" size="15" value="" /><br />
</div>

<br /><br />
<div class="ecr_floatbox">

<div onclick="doPHPDoc(<?php echo "'$sDirs', '$sFiles'"; ?>);" class="ecr_button" style="font-size: 1.5em;">
    <?php echo jgettext('Generate documentation'); ?>
</div>
</div>
<div style="clear: both;"></div>

<div id="ecr_title_file"></div>

<div id="ecr_codeeye_output" style="padding-top: 0.2em;"><h2><?php echo jgettext('Output')?></h2></div>
<pre id="ecr_codeeye_console"><?php echo jgettext('Console'); ?></pre>

<?php
// @codingStandardsIgnoreStart
/*
phpdoc -t targetdir -o HTML:default:default -d parsedir

  -f    --filename                name of file(s) to parse ',' file1,file2.
                                  Can contain complete path and * ? wildcards

  -d    --directory               name of a directory(s) to parse
                                  directory1,directory2

  -ed    --examplesdir            full path of the directory to look for
                                  example files from @example tags

  -tb    --templatebase           base location of all templates for this
                                  parse.

  -t    --target                  path where to save the generated files

  -i    --ignore                  file(s) that will be ignored, multiple
                                  separated by ','.  Wildcards * and ? are ok

  -is    --ignoresymlinks         ignore symlinks to other files or
                                  directories, default is off

  -it    --ignore-tags            tags to ignore for this parse.  @package,
                                  @subpackage, @access and @ignore may not be
                                  ignored.

  -dh    --hidden                 set equal to on (-dh on) to descend into
                                  hidden directories (directories starting with
                                  '.'), default is off

  -q    --quiet                   do not display parsing/conversion messages.
                                  Useful for cron jobs on/off default off

  -ue    --undocumentedelements   Control whether or not warnings will be shown
                                  for undocumented elements. Useful for
                                  identifying classes and methods that haven't
                                  yet been documented on/off default off

  -ti    --title                  title of generated documentation, default is
                                  'Generated Documentation'

  -h    --help                    show this help message

  -c    --useconfig               Use a Config file in the users/ subdirectory
                                  for all command-line options

  -pp    --parseprivate           parse @internal and elements marked private
                                  with @access.  Use on/off, default off

  -po    --packageoutput          output documentation only for selected
                                  packages.  Use a comma-delimited list

  -dn    --defaultpackagename     name to use for the default package.  If not
                                  specified, uses 'default'

  -dc    --defaultcategoryname    name to use for the default category.  If not
                                  specified, uses 'default'

  -o    --output                  output information to use separated by ','.
                                  Format: output:converter:templatedir like
                                  "HTML:frames:phpedit"

  -cp    --converterparams        dynamic parameters for a converter, separate
                                  values with commas

  -ct    --customtags             custom tags, will be recognized and put in
                                  tags[] instead of unknowntags[]

  -s    --sourcecode              generate highlighted sourcecode for every
                                  parsed file (PHP 4.3.0+ only) on/off default
                                  off

  -j    --javadocdesc             JavaDoc-compliant description parsing.  Use
                                  on/off, default off (more flexibility)

  -p    --pear                    Parse a PEAR-style repository (package is
                                  directory, _members are @access private)
                                  on/off default off

  -ric    --readmeinstallchangelogSpecify custom filenames to parse like
                                  README, INSTALL or CHANGELOG files
*/
