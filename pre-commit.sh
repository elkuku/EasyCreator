#!/bin/sh
#
# This will create a file "version.txt" with the version of the **previous*** commit
#
# Must be symlinked/copied to .git/hooks/pre-commit

ecrDir=administrator/components/com_easycreator

git describe --long > $ecrDir/version.txt

git add $ecrDir/version.txt
