<?php
// @codingStandardsIgnoreStart
/**
 * CHANGELOG.
 *
 * This is the changelog for EasyCreator.<br>
 * <b>Please</b> be patient =;)
 *
 * @version SVN: $Id$
 * $HeadURL$
 * @package    EasyCreator
 * @subpackage Documentation
 * @author     Nikolai Plath {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 03-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access to this changelog...
defined('_JEXEC') || die('=;)');

//-- For phpDocumentor documentation we need to construct a function =;)
/**
 * CHANGELOG.
 * {@source}
 */
function CHANGELOG()
{
/*
_______________________________________________
_______________________________________________

This is the changelog for EasyCreator

Please be patient =;)
_______________________________________________
_______________________________________________

Legend:

 * -> Security Fix
 # -> Bug Fix
 + -> Addition
 ^ -> Change
 - -> Removed
 ! -> Note
______________________________________________

25-Oct-2010 Nikolai Plath
 ^ Upgrade HighCharts to version 2.0.5

07-Oct-2010 Nikolai Plath
 + Drag & Drop ordering for 1.6 packages

07-Sep-2010 Nikolai Plath
 # Fixed spaces in language files causes errors

07-Sep-2010 Nikolai Plath
 + Added access keys in translation interface - e.g.: 'g' + 's' = 'Google translate' + 'Save'

05-Sep-2010 Nikolai Plath
 + Support for translations of Joomla.JText javascript strings in translation manager

05-Sep-2010 Nikolai Plath
 + Implemented J! 1.6's Joomla.JText Javascript translation with compat for J! 1.5 and a special loader =;)

01-Sep-2010 Nikolai Plath
 + Option to set a default font size for Editarea code editor

20-Aug-2010 Nikolai Plath
 + 1.6 Support for script.php files for the J! installer
 + 1.6 Support for multiple language file locations preferencing the extension directory
 # Fixed translation interface in 1.6

03-Aug-2010 Nikolai Plath
 # Fixed plugins in 1.6
 # Fixed unwanted Thumbs.db files on windows systems

17-Jul-2010 Nikolai Plath
 # Fixed Failed to include EasyCreator project file for projects other than component

--------------- 0.0.13 BETA -- [04-Jun-2010] ---------------------

06-Jun-2010 Nikolai Plath
 # Fixed can not register plugins with the same name but different groups - Thanks kenmcd
 + Added DTDs for project xml and startup xml at http://projects.easy-joomla.org/xml/dtd/easycreator/0.0.14/

31-May-2010 Nikolai Plath
 + Added extension type 'Joomla! 1.6 package'

28-May-2010 Nikolai Plath
 + Added Selenium test runner to CodeEyes

14-May-2010 Nikolai Plath
 + Added qtabs for slicker project display

14-May-2010 Nikolai Plath
 + Added first component template for J! 1.6 - mvc_9_16
 + Support for J! 1.6 media tag
 + Support for J! 1.6 update.sql files
 + Added extension type 'Joomla! 1.6 library'

07-May-2010 Nikolai Plath
 # Fixed [#20347] Error saving translations containing an ampersand (&) - Thanks Santiago Galindo

28-Apr-2010 Nikolai Plath
 + Added krumo for internal debugging

24-Apr-2010 Nikolai Plath
 + Language converter for INI and PHP files J! 1.5 => 1.6

21-Apr-2010 Nikolai Plath
 # Fixed module and plugin parameters and languages missing in packages

10-Apr-2010 Nikolai Plath
 + Added pchart for statistics

08-Apr-2010 Nikolai Plath
 # Fixed broken Plugin parameters
 # Fixed broken Plugin translations
 ^ Updated DTD location to http://joomla.org/xml/dtd/1.5
 + Project specific settings for creating packages

04-Apr-2010 Nikolai Plath
 # Fixed broken XML file translations

02-Apr-2010 Martin Riethmayer
 # authorEmail and authorUrl are case-sensitive in the install manifest, correcting from all small case.

27-Mar-2010 Nikolai Plath
 + Added highcharts.js from www.highcharts.com
 + Refactored the statistics and added some charts
 + Added code to comment ratio calculation to statistics

25-Mar-2010 Nikolai Plath
 + EasyCreator now checks a md5 file on his installation
 + Added md5 checksum routine to component extension templates

24-Mar-2010 Nikolai Plath
 # Fixed [#20003] Module and Plugin translation broken - Thanks Santiago Galindo.

21-Mar-2010 Nikolai Plath
 + Option to specify a custom list postfix in creation wizard
 ^ Changed hardcoded list postfix 's' to a custom one in all suitable ExtensionTemplates

20-Mar-2010 Nikolai Plath
 # Fixed broken "Project Info" - Thanks Michael (Mitomedia)

18-Mar-2010 Nikolai Plath
 + Alpha status for EasyAutoCode
 + Custom table fields and AutoCodes in ExtensionTemplate mvc_5 and mvc_9
 ^ Build packages are now located in folder 'builds'
 ^ Moved folders 'logs' and 'scripts' to extension root

03-Mar-2010 Nikolai Plath
 + Option to create a md5 checksum file for packages

26-Feb-2010 Nikolai Plath
 # Fixed broken translation engine - modal js missing

--------------- 0.0.12.1 BETA -- [22-Feb-2010] ---------------------

19-Feb-2010 Nikolai Plath
 + Admin submenu ordering

12-Feb-2010 Nikolai Plath
 # Fixed manifest XML not validating against J!'s DTD
 + Option to deactivate UTF8 and BOM checking for language files - subtile error..

10-Feb-2010 Nikolai Plath
 # Fixed error on saving plugin configuration
 # Fixed [#19768] error on creating zip packages - windows only
 # Fixed error on deleting package files - windows only
 + Config option to specify the location of a local J! API copy - for J! Help

12-Jan-2010 Nikolai Plath
 ^ PHP install files are placed in a separate 'install' directory when packing the extension

12-Jan-2010 Nikolai Plath
 # Fixed error when packing a site module - Thanks MartinR (ripper17)
 # Fixed Opera bug: Registering projects not working properly - Thanks Martin (ripper17)
 + Parameters from config.xml and manifest.xml will be added to install manifest on packing - Thanks Martin (ripper17)

--------------- 0.0.12 BETA -- [02-Jan-2010] ---------------------

01-Jan-2010 Nikolai Plath
 + Added Extension template type 'template' named 'Blueprint'
 - Removed Extension templates 'Beez Copy' and 'MilkyWay Copy'
 - Removed old help screens - added link to documentation wiki

30-Dec-2009 Nikolai Plath
 ^ Optimized Install file detection

22-Nov-2009 Nikolai Plath
 ^ Refactored all extension templates. Using XML files now
 ^ EasyCreator project file is in XML format now
 + Option to include EasyCreator project file in final package
 + Creating skeleton UnitTest classes for project classes

20-Oct-2009 Nikolai Plath
 + Added profiler for J! 1.6
 + Added Mootools compat 1.1 => 1.2 - Json (sry: JSON...)

18-Oct-2009 Nikolai Plath
 + Added PEAR and PEAR::Archive_Tar as they will be removed in J! 1.6

15-Oct-2009 Nikolai Plath
 + Added Language file 'overview' in Language Manager
 - Removed language creation from startup Wizard
 - Removed language creation from extension Setup
 ^ Changed EasyCreator Config from lightbox to a 'normal' view

06-Oct-2009 Nikolai Plath
 + Joomla! doctype DTD will be added to manifest

05-Oct-2009 Nikolai Plath
 + Introducing EasyCodeEye
 + Added Interface to PEAR::phpcpd - PHP Copy & Paste Detector
 + Added Interface to PEAR::phpDocumentor
 + Added Interface to PEAR::phpUnit

28-Sep-2009 Nikolai Plath
 ^ Refactored manifest class to use PHPs SimpleXML
 ^ Refactored Toolbars to be tableless
 + Added Mootools compat 1.1 => 1.2 - Ajax
 ! We should now run on Joomla! 1.6 (tested on alpha2) =;)

21-Sep-2009 Nikolai Plath
 + Option to place empty index.html in every directory on building the project
 ^ Package install file placed in subfolder /install
 # Fixed [#18718] code deleted on saving when an ampersand (&) is found

23-Aug-2009 Nikolai Plath
 + Added an interface to PEAR::CodeSniffer
 + Added the first Joomla! coding standards to be used with CodeSniffer - WIP

--------------- 0.0.11 BETA -- [22-Aug-2009] ---------------------

17-Aug-2009 Nikolai Plath
 + Added option to include 'method=upgrade' in install XML - this should be improved in J! 1.6
 + Added Easy Tooltips

11-Aug-2009 Nikolai Plath
 + ohloh button - please use it =;)

09-Aug-2009 Nikolai Plath
 + Added Joomla! Framework search engine

12-Jul-2009 Nikolai Plath
 + Highlighting php code in log files
 ^ Changed TreeIt class to php_file_tree function (see: credits)

11-Jul-2009 Nikolai Plath
 + Added template using J! categories
 ! Enabled EditArea Autocomplete (experimental!)
 ! Changed EditArea reg_syntax/php.js file - added Joomla! classes and methods

21-Jun-2009 Nikolai Plath
 # Fixed not correctly handling plugin language files

16-Jun-2009 Nikolai Plath
 + Custom file names for package files

12-May-2009 Nikolai Plath
 + Context menu with file/folder operations for 'Template' section
 ^ tweaked EditArea to dynamically change the syntax also for ini files

12-May-2009 Nikolai Plath
 + Ajax loading file contents from file tree for editing
 + Simple context menu for file tree - (new, rename, delete..)

--------------- 0.0.10 BETA -- [12-May-2009] ---------------------

12-May-2009 Nikolai Plath
 ^ Moved Archive to Packer view

10-May-2009 Nikolai Plath
 + Custom options for extension templates
 + Plugin extension template in custom folder
 + Option to delete translations

02-May-2009 Nikolai Plath
 # Fixed custom folders for plugins not included in packages

30-Apr-2009 Nikolai Plath
 ^ Changed all models to helpers !

29-Apr-2009 Nikolai Plath
 - removed DirectoryInfo class

27-Apr-2009 Nikolai Plath
 ^ Refactoring of 'Add Elements' - all code comes from templates now
 ^ Improved logging
 ^ Updated EditArea to version 0.8.1.1
 + Added a 'create table + admin' part - Thanks hidabe
 + Added a 'map table + admin' part

23-Apr-2009 Nikolai Plath
 ^ Facelifting for EasyCreator - green is easy =;)

19-Apr-2009 Nikolai Plath
 ^ Cleanup language manager

16-Apr-2009 Nikolai Plath
 + Using lightbox & ajax for translation

29-Mar-2009 Nikolai Plath
 + Added DokuWiki DifferenceEngine for language file diffs - see: helpers/DifferenceEngine.php
 - Removed old diff routine
 # Misspelled param in user plugin example

--------------- 0.0.9 BETA -- [28-Feb-2009] ---------------------

27-Feb-2009 Nikolai Plath
 + Support for packages
 + Install & uninstall file for packages - BIG Thanks to Andrew Eddie - jxtended.com

01-Feb-2009 Nikolai Plath
 # Fixed language file names for plugins

25-Jan-2009 Nikolai Plath
 ^ Using the J! 1.5 <folder> tag in XML manifest for copying complete folders

19-Jan-2009 Nikolai Plath
 # Stripping bad characters from name of created extensions - Thanks keran

--------------- 0.0.8 BETA -- [28-Dec-2008] ---------------------

23-Dec-2008 Nikolai Plath
 # Fixed wrong entry for uninstall file in XML manifest (on windows) - Thanks baijianpeng
 # Fixed wrong template file in system plugin template

20-Dec-2008 Nikolai Plath
 + Added a template for a system plugin
 # Fixed languages files / menu language files not added to XML if added afterwards

19-Dec-2008 Nikolai Plath
 + Added simplified chinese to language selector
 # Fixed languages files / uninstall file missing in new created XML - Thanks baijianpeng

17-Dec-2008 Nikolai Plath
 + Added chinese language - Thanks baijianpeng

06-Dec-2008 Nikolai Plath
 # Fixed missing field for entry file in creating form

05-Dec-2008 Nikolai Plath
 # Fixed path errors on creating XML install file (on windows) - Thanks ricola
 # Fixed install/uninstall file not registered in importing projects
 + Added a menu icon to all extension templates

04-Dec-2008 Nikolai Plath
 + Support for tar.gz format in packages (while PEAR packages are still included in J!)
 + Support for parameters in XML install file (templates still WIP)
 # Fixed creationDate in XML install file
 # Fixed lightbox, ajax loader gif in template mvc_6

02-Dec-2008 Nikolai Plath
 + Added a warning message if a live install is detected ( + option to switch this off in config)

--------------- 0.0.7.1 BETA -- [01-Dec-2008] ---------------------

30-Nov-2008 Nikolai Plath
 # Fixed saving templates not working
 # Fixed registering existing extensions not properly
 # Fixed admin menu designer displayed for all extension types :~|
 # Fixed missing field for install XML file in creating form
 + Added a template for a backend module

26-Nov-2008 Nikolai Plath
 # Fixed group/client not set for modules/plugins in XML install file

20-Nov-2008 Nikolai Plath
 + Use Google translation API as an 'aid' (optional of course)

--------------- 0.0.7 BETA -- [23-Nov-2008] ---------------------

16-Nov-2008 Nikolai Plath
 + Added polish language - Thanks keran

15-Nov-2008 Nikolai Plath
 + Added J! core example plugins for content, user and authentication as templates
 + Added more reference links from docs.joomla.org
 - Removed screenshots from help leading to smaller install file size
 # Smaller install file size should fix error 'blank page after install' - time out - Thanks keran

13-Nov-2008 Nikolai Plath
 # Fixed path issues on windows systems

11-Nov-2008 Nikolai Plath
 + Admin menu translation for components
 + Check language files for correct UTF-8 encoding and presence of a BOM
 + Option to remove the BOM from language files

07-Nov-2008 Nikolai Plath
 + New languages for J! 1.5.8

06-Nov-2008 Nikolai Plath
 + Admin menu designer for components
 ^ Code cleanup
 ^ Start adding access modifiers so - PHP 5 is required now !

30-Oct-2008 Nikolai Plath
 # Fixed path issues on windows leading to 'file not found' in multiple occasions ;(
 # Fixed error caused by incompatible PHP function money_format() on windows
 + Added french language - Thanks crony

--------------- 0.0.6 BETA -- [22-Oct-2008] ---------------------

22-Oct-2008 Nikolai Plath
 + JDump will be used for some debugging output (if installed)
 ^ Updated EditArea to version 0.7.2.3

20-Oct-2008 Nikolai Plath
 + Form for extension setup
 ^ Changed structure of all extension templates !
 ^ Moved the templates folder to component root

16-Oct-2008 Nikolai Plath
 + Language selector in wizard step 4
 + Language selector in extension setup form
 + Adding and editing admin menu entries for components
 + Adding code structures like view, controller, model or table to components

--------------- 0.0.5 BETA -- [09-Oct-2008] ---------------------

08-Oct-2008 Nikolai Plath
 + Option to leave the editor open after saving
 + Option to switch EditAreaLoader version
 + Some HTML tags and Joomla! constants for inserting via EditArea
 ^ Changed template MVC 6 to a MooTools demo component
 ^ Updated EditArea to version 0.7.2.2

06-Oct-2008 Nikolai Plath
 + Inspect your code with PHP 5's reflection class - look in 'project info'

03-Oct-2008 Nikolai Plath
 + Using existing Rhuk MilkyWay and Beez templates as templates for new templates =;)

02-Oct-2008 Nikolai Plath
 + Version check with Soeren Eberhardt's version manager

01-Oct-2008 Nikolai Plath
 + Option to include Joomla! core projects
 ^ Reworked language manager - added MooTools drag & drop + sortables
 ^ Overall facelifting

24-Sep-2008 Nikolai Plath
 + Parameter handling for projects
 + Frontend sandbox for testing projects

--------------- 0.0.4 ALPHA -- [17-Sep-2008] ---------------------

15-Sep-2008 Nikolai Plath
 + Create + edit language files
 + Extension template component MVC 7 - Component with editor
 + Credits and long description link for extension templates
 + Logging facility
 # Fixed errors in template com MVC 3
 # Fixed language creation for new registered projects

12-Sep-2008 Nikolai Plath
 + Salary calculator ($Xline=;)

--------------- 0.0.3 ALPHA -- [10-Sep-2008] ---------------------

10-Sep-2008 Nikolai Plath
 # Fixed path issues on windows systems

09-Sep-2008 Nikolai Plath
 ! OK, we are using the standard Joomla! installer now (!)
 # Language check not working properly

--------------- 0.0.2 ALPHA -- [08-Sep-2008] ---------------------

08-Sep-2008 Nikolai Plath
 + Load core language for compare

03-Sep-2008 Nikolai Plath
 + Importing of existing components, modules, plugins and templates

01-Sep-2008 Nikolai Plath
 + Wizard for new projects

31-Aug-2008 Nikolai Plath
 + Import of existing projects

09-Aug-2008 Nikolai Plath
 ^ Improved language handling - analysis of php and XML files, compare ini files

08-Aug-2008 Nikolai Plath
 + AJAX component template

30-Jul-2008 Nikolai Plath
 ^ Improved building of modules and plugins

29-Jul-2008 Nikolai Plath
 + Admin menu and submenu entries in cfg file
 + Form for edit cfg file

23-Jul-2008 Nikolai Plath
 + Description files for extension templates

22-Jul-2008 Nikolai Plath
 ^ Updated EditArea to version 0.7.1.3 -> fixes bug in FF 3

10-Jul-2008 Nikolai Plath
 + Multiple language files will be created

--------------- 0.0.1 ALPHA -- [03-Mar-2008] ---------------------

03-Mar-2008 Nikolai Plath
 ! Startup.

*/
}//--This is the END
