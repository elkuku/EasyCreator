#!/bin/bash
#
# This will create a file "version.txt" with the version of the **previous*** commit
#
# Must be symlinked/copied to .git/hooks/pre-commit
#
# @package    EasyCreator
# @subpackage Helpers.Scripts
# @author     Nikolai Plath (elkuku)
# @author     Created on 08-Mar-2012
# @license    GNU/GPL, see JROOT/LICENSE.php

projectDir=administrator/components/com_easycreator

git describe --long > $projectDir/version.txt

git add $projectDir/version.txt
