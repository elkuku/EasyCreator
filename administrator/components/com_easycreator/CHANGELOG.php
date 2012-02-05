<?php
// @codingStandardsIgnoreStart
/**
 * CHANGELOG.---
 *
 * This is the changelog for EasyCreator.<br>
 * <b>Please</b> be patient =;)
 *
 * @package    EasyCreator
 * @subpackage Documentation
 * @author     Nikolai Plath
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
<description>
EasyCreator is a developer tool.
It tries to speed up the developing process of custom components, modules, plugin and templates.
You can create a 'frame' for your extension and an installable zip package with just a few 'clicks'.

<b>From version 0.0.14 onwards the g11n language library is required for foreign (non English) languages !</b>
</description>
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

2012-02-04
Formatting
Bump version to 0.0.15.1
+ Add a CLI builder script
Add cli output to the logger
Add EcrZiperException
Refactor the Ziper

2012-02-03
Add a missing <p>

---------- 0.0.15.1 ----------

2012-02-03
# [#1] Fix a fatal startup error in PHP 5.2 - please update to PHP 5.3 =;) - Thanks adonismedia
+ Add a tiny changelog generator
Update changelog

---------- 0.0.15 ----------

2012-02-03
Formatting

2012-02-02
Icon name changes and format
Add mod_version to known J 2.5 core projects
# Fix a nasty bug that prevents the stuffer form from saving added menu items and update servers correctly, formatting

2012-01-30
Code style

2012-01-29
# Fix admin list views in J! 2.5
Code style - Fix single line comments
Formatting
# Fix another 2.5 option
Refactor EcrBuilder

2012-01-25
Changes JVersion for extension templates from 1.7 to 2.5 ;)
# Fix Missing function parameters in extending classes

---------- 0.0.14.4 ----------

2012-01-19
Fix g11n language loading in frontend sandbox - Thanks Troy
Simplify Projecthelper - a bit
Fix Projecthelper
Rename Pear tar and zip and the php_file_tree class
Rename EasyPearHelper(s) => EcrPearHelper(s)
Rename dbUpdater => EcrDbUpdater
Rename EasyProjectMatrix => EcrProjectMatrix
Rename EcrLanguageConverter and Ecrg11nHelper
Fix wrong extension in ecrStylesheet()
Simplified acrStylesheet() and ecrScript() calls
Add suport for multiple style sheets in ecrStylesheet()
Rename EasyLanguage => EcrLanguage
Adapt the JMethodLister to J! 2.5 (phew....)
Modify (C) date
Remove missing from Renames
Rename EasyPart => EcrPart
Rename EasyAutoCode => EcrAutoCode
Rename EasyXMLElement => EcrXMLElement
Formatting
Rename EasyTable => EcrTable including other table related classes
Rename EasyProfiler => EcrProfiler
Leftover from EcrProject refactoring
Rename JoomlaManifest => EcrManifest
Rename EasyTemplateHelper => EcrTemplateHelper
Rename EasyFile => EcrFile
Add support for a new version replacement in filename creation -> VCSREV

2012-01-17
Add support for mutpile file header types
Move install.sql files to a mysql subfolder
Change comments in language files
Move header template files

2012-01-16
Rename EasyLogger => EcrLogger
Rename EasyProject => EcrProject as well as extending classes
Rename EasyReflection => EcrReflection
Add J! 1.5 deprecation notes
Rename EasyLanguageHelper => EcrLanguageHelper
Rename EasyProjectHelper => EcrProjectHelper
Update xml
Modify readme
Modify readme

---------- 0.0.14.3.3 ----------

2012-01-15
Formatting
Formatting
Refactor Joomla! manifest class
Fix git version info
Rename classes EasyBuilder => EcrBuilder, EasyArchive => EcrArchive, EasyCreatorHelper => EcrHelper
Fix frontend g11n call to removed function
Fix wrong paths in frontend sandbox
Modify .gitignore
Fix autoloader
Renaming SQL package to sql
Renaming classes for auto_loading

---------- 0.0.14.3.2 ----------

2012-01-14
Add SQL dropTable format
Update DTD location in manifest.xml(s)
Add support for multiple database types
Add git version information to footer
Fix Can not save project config **sometimes**
Add SQL formatters for MySQL, Postgre and SQLite
Fix MysqlExporter
Add pre-commit hook script for reference
Add  .git/hooks/pre-commit generated version information to the footer
Add db icon
Add SQL formatters

---------- 0.0.14.3.1 ----------

2012-01-09
Remove obsolete files
Remove obsolete documentation
Add missing J! 2.5 fixes

2012-01-08
Fix another JParameter usage
Fix template component 9_16, cleanup queries, removed JParameter

2012-01-07
Code style
Code style
Remove deprecated JError - part 4
Remove deprecated JError - part 3
Remove deprecated JError - part 2
Remove bad link :(
Remove bad link :(
Remove deprecated JError - 1st part
More J! 2.5 work
Code style
Removing obsolete svn ids
Prepare for J! 2.5
Merge remote branch 'elkuku/master'

2011-10-29
First check if the g11n language library is installed as a PEAR package
Switched template compat versions from 1.6 to 1.7

2011-10-21
Add link to ghpage
Add Ohloh stats

2011-10-20
Add Transifex stats

2011-10-18
Add code preview to code sniffer

2011-10-10
Remove Joomla coding standard

2011-10-03
Update language zh-CN
Update language template headers
Add Transifex config file
Update language fr-FR
Update language nl-NL
Add language pt-BR
Update language pl-PL
Update language zh-CN
Add language id-ID
Add language hu-HU
Add language da-DK
Add language ar-AR
Update language es-ES
Add language sv-SE
Garbage in language template :P
Update language

2011-09-30
Add experimental ant script

2011-09-26
Fix package names must be lower case

2011-09-18
Fix g11n loading plugins and templates
Modify icon
Add nl-NL and sv-SE language - Thx Transifex =;)
Fix g11n lookup for templates and plugins

2011-08-28
preference of JPATH_PLATFORM over JPATH_LIBRARIES

2011-08-15
Added automatic language file handling for po files using gettext utilities

23-Aug-2011
 # Fixed "add elements" not working on Windows in J! 1.7 - Thanks Apfelkuchen

12-Aug-2011
 + Added interface for PHPLOC

02-Aug-2011
 # Fixed md5 compression method
 ^ Config now uses JForm
 ^ More CSS 3 eye candy

--------------- 0.0.14.3 BETA -- [29-Jun-2011] ---------------------
Download: http://joomlacode.org/gf/project/elkuku/frs/?action=FrsReleaseView&release_id=15410

27-Jul-2011
 # Fixed a whole lot of path errors on windows in J! 1.7 - Thanks Rita & Krishna

22-Jul-2011
 + 1.6+ Define updateserver(s) for projects
 + Generate SQL install / uninstall files
 + Generate update SQL files for previous versions

08-Jul-2011
 + Added posibility to specify a custom output directory for created zip packages

08-Jul-2011
 # Fixed setting method="upgrade" not read from install.xml - Thanks Kharlanki
 # Fixed Can not delete admin submenu entries in J! 1.5 - Thanks Kharlanki
 # Fixed notices in packager when projects CHANGELOG is not under svn version control - Thanks Kharlanki

02-Jul-2011
 + Added PEARs SQL parser class
 ^ Today I learned about css border-radius... can you see it ? =;)

22-Jun-2011
 + Small optional compression on md5 checksum files

--------------- 0.0.14.2 BETA -- [22-Jun-2011] ---------------------
Download: http://joomlacode.org/gf/project/elkuku/frs/?action=FrsReleaseView&release_id=15067

22-Jun-2011
 # Fixed errors on creating extensions type "package" for J! 1.6 - Thanks Greg Keys

--------------- 0.0.14.1 BETA -- [12-Jun-2011] ---------------------
Download: http://joomlacode.org/gf/project/elkuku/frs/?action=FrsReleaseView&release_id=15022

12-Jun-2011
 ^ Mootools fixes Fx.Style => Fx.Tween
 ^ Changed DTD location to joomlacode.org
 - Mootools fixes Json.evaluate => JSON.decode
 - Removed Mootools compat
 + Header templates for css and js files - Thanks 7list
 + JVersion selector in wizard step 1

08-Jun-2011
 + More modifications for J! 1.7
 + Templates for 1.6 plugins Editor, Extension and Search
 + Templates for 1.6 modules front- and backend
 - Removed qTabs

07-Jun-2011
 + Added modifications for J! 1.7
 # Fixed a fatal error on PHP 5.3 - "Serialization of EasyXMLElement is not allowed" - Thanks Joni

29-May-2011
 # Fixed Code editor does not display ini files - Thanks 7list
 # Fixed can not uninstall template projects - Thanks 7list

24-May-2011
 # Fixed media folders in manifest.xml

21-May-2011
 ^ Created a g11n dummy to make the g11n library optional - if not installed, only english language will be available
 # fixed parameters in J 1.6

18-May-2011
 # Fixed admin menus in J! 1.6
 + Drag & Drop in admin menu manager (J >= 1.6 only)

--------------- 0.0.14 BETA -- [12-May-2011] ---------------------
Download: http://joomlacode.org/gf/project/elkuku/frs/?action=FrsReleaseView&release_id=14856

10-May-2011
 - Removed MooTools compat for Ajax
 + New example plugins for J! 1.6

03-May-2011
 + Added AutoCodes for 1.6 select queries and xml forms
 # Fixed some uninstaller issues

01-Jan-2011
 ! EasyCreator now uses the g11n language library

11-Dec-2010
 # Fiexed various errors when registering and building libraries
 # Fixed incorrect extension name in admin menu when registering components in J! SVN > 18779

25-Oct-2010
 ^ Upgrade HighCharts to version 2.0.5

07-Oct-2010
 + Drag & Drop ordering for 1.6 packages

07-Sep-2010
 # Fixed spaces in language files causes errors

07-Sep-2010
 + Added access keys in translation interface - e.g.: 'g' + 's' = 'Google translate' + 'Save'

05-Sep-2010
 + Support for translations of Joomla.JText javascript strings in translation manager

05-Sep-2010
 + Implemented J! 1.6's Joomla.JText Javascript translation with compat for J! 1.5 and a special loader =;)

01-Sep-2010
 + Option to set a default font size for Editarea code editor

20-Aug-2010
 + 1.6 Support for script.php files for the J! installer
 + 1.6 Support for multiple language file locations preferencing the extension directory
 # Fixed translation interface in 1.6

03-Aug-2010
 # Fixed plugins in 1.6
 # Fixed unwanted Thumbs.db files on windows systems

17-Jul-2010
 # Fixed Failed to include EasyCreator project file for projects other than component

06-Jun-2010
 # Fixed can not register plugins with the same name but different groups - Thanks kenmcd
 + Added DTDs for project xml and startup xml at http://xml.der-beta-server.de/dtd/easycreator/0.0.14/

--------------- 0.0.13 BETA -- [04-Jun-2010] ---------------------
Download: http://joomlacode.org/gf/project/elkuku/frs/?action=FrsReleaseView&release_id=13122

31-May-2010
 + Added extension type 'Joomla! 1.6 package'

28-May-2010
 + Added Selenium test runner to CodeEyes

14-May-2010
 + Added qtabs for slicker project display

14-May-2010
 + Added first component template for J! 1.6 - mvc_9_16
 + Support for J! 1.6 media tag
 + Support for J! 1.6 update.sql files
 + Added extension type 'Joomla! 1.6 library'

07-May-2010
 # Fixed [#20347] Error saving translations containing an ampersand (&) - Thanks Santiago Galindo

28-Apr-2010
 + Added krumo for internal debugging

24-Apr-2010
 + Language converter for INI and PHP files J! 1.5 => 1.6

21-Apr-2010
 # Fixed module and plugin parameters and languages missing in packages

10-Apr-2010
 + Added pchart for statistics

08-Apr-2010
 # Fixed broken Plugin parameters
 # Fixed broken Plugin translations
 ^ Updated DTD location to http://joomla.org/xml/dtd/1.5
 + Project specific settings for creating packages

04-Apr-2010
 # Fixed broken XML file translations

02-Apr-2010 Martin Riethmayer
 # authorEmail and authorUrl are case-sensitive in the install manifest, correcting from all small case.

27-Mar-2010
 + Added highcharts.js from www.highcharts.com
 + Refactored the statistics and added some charts
 + Added code to comment ratio calculation to statistics

25-Mar-2010
 + EasyCreator now checks a md5 file on his installation
 + Added md5 checksum routine to component extension templates

24-Mar-2010
 # Fixed [#20003] Module and Plugin translation broken - Thanks Santiago Galindo.

21-Mar-2010
 + Option to specify a custom list postfix in creation wizard
 ^ Changed hardcoded list postfix 's' to a custom one in all suitable ExtensionTemplates

20-Mar-2010
 # Fixed broken "Project Info" - Thanks Michael (Mitomedia)

18-Mar-2010
 + Alpha status for EasyAutoCode
 + Custom table fields and AutoCodes in ExtensionTemplate mvc_5 and mvc_9
 ^ Build packages are now located in folder 'builds'
 ^ Moved folders 'logs' and 'scripts' to extension root

03-Mar-2010
 + Option to create a md5 checksum file for packages

26-Feb-2010
 # Fixed broken translation engine - modal js missing

--------------- 0.0.12.1 BETA -- [22-Feb-2010] ---------------------

19-Feb-2010
 + Admin submenu ordering

12-Feb-2010
 # Fixed manifest XML not validating against J!'s DTD
 + Option to deactivate UTF8 and BOM checking for language files - subtile error..

10-Feb-2010
 # Fixed error on saving plugin configuration
 # Fixed [#19768] error on creating zip packages - windows only
 # Fixed error on deleting package files - windows only
 + Config option to specify the location of a local J! API copy - for J! Help

12-Jan-2010
 ^ PHP install files are placed in a separate 'install' directory when packing the extension

12-Jan-2010
 # Fixed error when packing a site module - Thanks MartinR (ripper17)
 # Fixed Opera bug: Registering projects not working properly - Thanks Martin (ripper17)
 + Parameters from config.xml and manifest.xml will be added to install manifest on packing - Thanks Martin (ripper17)

--------------- 0.0.12 BETA -- [02-Jan-2010] ---------------------

01-Jan-2010
 + Added Extension template type 'template' named 'Blueprint'
 - Removed Extension templates 'Beez Copy' and 'MilkyWay Copy'
 - Removed old help screens - added link to documentation wiki

30-Dec-2009
 ^ Optimized Install file detection

22-Nov-2009
 ^ Refactored all extension templates. Using XML files now
 ^ EasyCreator project file is in XML format now
 + Option to include EasyCreator project file in final package
 + Creating skeleton UnitTest classes for project classes

20-Oct-2009
 + Added profiler for J! 1.6
 + Added Mootools compat 1.1 => 1.2 - Json (sry: JSON...)

18-Oct-2009
 + Added PEAR and PEAR::Archive_Tar as they will be removed in J! 1.6

15-Oct-2009
 + Added Language file 'overview' in Language Manager
 - Removed language creation from startup Wizard
 - Removed language creation from extension Setup
 ^ Changed EasyCreator Config from lightbox to a 'normal' view

06-Oct-2009
 + Joomla! doctype DTD will be added to manifest

05-Oct-2009
 + Introducing EasyCodeEye
 + Added Interface to PEAR::phpcpd - PHP Copy & Paste Detector
 + Added Interface to PEAR::phpDocumentor
 + Added Interface to PEAR::phpUnit

28-Sep-2009
 ^ Refactored manifest class to use PHPs SimpleXML
 ^ Refactored Toolbars to be tableless
 + Added Mootools compat 1.1 => 1.2 - Ajax
 ! We should now run on Joomla! 1.6 (tested on alpha2) =;)

21-Sep-2009
 + Option to place empty index.html in every directory on building the project
 ^ Package install file placed in subfolder /install
 # Fixed [#18718] code deleted on saving when an ampersand (&) is found

23-Aug-2009
 + Added an interface to PEAR::CodeSniffer
 + Added the first Joomla! coding standards to be used with CodeSniffer - WIP

--------------- 0.0.11 BETA -- [22-Aug-2009] ---------------------

17-Aug-2009
 + Added option to include 'method=upgrade' in install XML - this should be improved in J! 1.6
 + Added Easy Tooltips

11-Aug-2009
 + ohloh button - please use it =;)

09-Aug-2009
 + Added Joomla! Framework search engine

12-Jul-2009
 + Highlighting php code in log files
 ^ Changed TreeIt class to php_file_tree function (see: credits)

11-Jul-2009
 + Added template using J! categories
 ! Enabled EditArea Autocomplete (experimental!)
 ! Changed EditArea reg_syntax/php.js file - added Joomla! classes and methods

21-Jun-2009
 # Fixed not correctly handling plugin language files

16-Jun-2009
 + Custom file names for package files

12-May-2009
 + Context menu with file/folder operations for 'Template' section
 ^ tweaked EditArea to dynamically change the syntax also for ini files

12-May-2009
 + Ajax loading file contents from file tree for editing
 + Simple context menu for file tree - (new, rename, delete..)

--------------- 0.0.10 BETA -- [12-May-2009] ---------------------

12-May-2009
 ^ Moved Archive to Packer view

10-May-2009
 + Custom options for extension templates
 + Plugin extension template in custom folder
 + Option to delete translations

02-May-2009
 # Fixed custom folders for plugins not included in packages

30-Apr-2009
 ^ Changed all models to helpers !

29-Apr-2009
 - removed DirectoryInfo class

27-Apr-2009
 ^ Refactoring of 'Add Elements' - all code comes from templates now
 ^ Improved logging
 ^ Updated EditArea to version 0.8.1.1
 + Added a 'create table + admin' part - Thanks hidabe
 + Added a 'map table + admin' part

23-Apr-2009
 ^ Facelifting for EasyCreator - green is easy =;)

19-Apr-2009
 ^ Cleanup language manager

16-Apr-2009
 + Using lightbox & ajax for translation

29-Mar-2009
 + Added DokuWiki DifferenceEngine for language file diffs - see: helpers/DifferenceEngine.php
 - Removed old diff routine
 # Misspelled param in user plugin example

--------------- 0.0.9 BETA -- [28-Feb-2009] ---------------------

27-Feb-2009
 + Support for packages
 + Install & uninstall file for packages - BIG Thanks to Andrew Eddie - jxtended.com

01-Feb-2009
 # Fixed language file names for plugins

25-Jan-2009
 ^ Using the J! 1.5 <folder> tag in XML manifest for copying complete folders

19-Jan-2009
 # Stripping bad characters from name of created extensions - Thanks keran

--------------- 0.0.8 BETA -- [28-Dec-2008] ---------------------

23-Dec-2008
 # Fixed wrong entry for uninstall file in XML manifest (on windows) - Thanks baijianpeng
 # Fixed wrong template file in system plugin template

20-Dec-2008
 + Added a template for a system plugin
 # Fixed languages files / menu language files not added to XML if added afterwards

19-Dec-2008
 + Added simplified chinese to language selector
 # Fixed languages files / uninstall file missing in new created XML - Thanks baijianpeng

17-Dec-2008
 + Added chinese language - Thanks baijianpeng

06-Dec-2008
 # Fixed missing field for entry file in creating form

05-Dec-2008
 # Fixed path errors on creating XML install file (on windows) - Thanks ricola
 # Fixed install/uninstall file not registered in importing projects
 + Added a menu icon to all extension templates

04-Dec-2008
 + Support for tar.gz format in packages (while PEAR packages are still included in J!)
 + Support for parameters in XML install file (templates still WIP)
 # Fixed creationDate in XML install file
 # Fixed lightbox, ajax loader gif in template mvc_6

02-Dec-2008
 + Added a warning message if a live install is detected ( + option to switch this off in config)

--------------- 0.0.7.1 BETA -- [01-Dec-2008] ---------------------

30-Nov-2008
 # Fixed saving templates not working
 # Fixed registering existing extensions not properly
 # Fixed admin menu designer displayed for all extension types :~|
 # Fixed missing field for install XML file in creating form
 + Added a template for a backend module

26-Nov-2008
 # Fixed group/client not set for modules/plugins in XML install file

20-Nov-2008
 + Use Google translation API as an 'aid' (optional of course)

--------------- 0.0.7 BETA -- [23-Nov-2008] ---------------------

16-Nov-2008
 + Added polish language - Thanks keran

15-Nov-2008
 + Added J! core example plugins for content, user and authentication as templates
 + Added more reference links from docs.joomla.org
 - Removed screenshots from help leading to smaller install file size
 # Smaller install file size should fix error 'blank page after install' - time out - Thanks keran

13-Nov-2008
 # Fixed path issues on windows systems

11-Nov-2008
 + Admin menu translation for components
 + Check language files for correct UTF-8 encoding and presence of a BOM
 + Option to remove the BOM from language files

07-Nov-2008
 + New languages for J! 1.5.8

06-Nov-2008
 + Admin menu designer for components
 ^ Code cleanup
 ^ Start adding access modifiers so - PHP 5 is required now !

30-Oct-2008
 # Fixed path issues on windows leading to 'file not found' in multiple occasions ;(
 # Fixed error caused by incompatible PHP function money_format() on windows
 + Added french language - Thanks crony

--------------- 0.0.6 BETA -- [22-Oct-2008] ---------------------

22-Oct-2008
 + JDump will be used for some debugging output (if installed)
 ^ Updated EditArea to version 0.7.2.3

20-Oct-2008
 + Form for extension setup
 ^ Changed structure of all extension templates !
 ^ Moved the templates folder to component root

16-Oct-2008
 + Language selector in wizard step 4
 + Language selector in extension setup form
 + Adding and editing admin menu entries for components
 + Adding code structures like view, controller, model or table to components

--------------- 0.0.5 BETA -- [09-Oct-2008] ---------------------

08-Oct-2008
 + Option to leave the editor open after saving
 + Option to switch EditAreaLoader version
 + Some HTML tags and Joomla! constants for inserting via EditArea
 ^ Changed template MVC 6 to a MooTools demo component
 ^ Updated EditArea to version 0.7.2.2

06-Oct-2008
 + Inspect your code with PHP 5's reflection class - look in 'project info'

03-Oct-2008
 + Using existing Rhuk MilkyWay and Beez templates as templates for new templates =;)

02-Oct-2008
 + Version check with Soeren Eberhardt's version manager

01-Oct-2008
 + Option to include Joomla! core projects
 ^ Reworked language manager - added MooTools drag & drop + sortables
 ^ Overall facelifting

24-Sep-2008
 + Parameter handling for projects
 + Frontend sandbox for testing projects

--------------- 0.0.4 ALPHA -- [17-Sep-2008] ---------------------

15-Sep-2008
 + Create + edit language files
 + Extension template component MVC 7 - Component with editor
 + Credits and long description link for extension templates
 + Logging facility
 # Fixed errors in template com MVC 3
 # Fixed language creation for new registered projects

12-Sep-2008
 + Salary calculator ($Xline=;)

--------------- 0.0.3 ALPHA -- [10-Sep-2008] ---------------------

10-Sep-2008
 # Fixed path issues on windows systems

09-Sep-2008
 ! OK, we are using the standard Joomla! installer now (!)
 # Language check not working properly

--------------- 0.0.2 ALPHA -- [08-Sep-2008] ---------------------

08-Sep-2008
 + Load core language for compare

03-Sep-2008
 + Importing of existing components, modules, plugins and templates

01-Sep-2008
 + Wizard for new projects

31-Aug-2008
 + Import of existing projects

09-Aug-2008
 ^ Improved language handling - analysis of php and XML files, compare ini files

08-Aug-2008
 + AJAX component template

30-Jul-2008
 ^ Improved building of modules and plugins

29-Jul-2008
 + Admin menu and submenu entries in cfg file
 + Form for edit cfg file

23-Jul-2008
 + Description files for extension templates

22-Jul-2008
 ^ Updated EditArea to version 0.7.1.3 -> fixes bug in FF 3

10-Jul-2008
 + Multiple language files will be created

--------------- 0.0.1 ALPHA -- [03-Mar-2008] ---------------------

03-Mar-2008
 ! Startup.

*/
}//--This is the END
