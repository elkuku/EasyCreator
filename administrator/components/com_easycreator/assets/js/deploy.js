/**
 * @package EasyCreator
 * @subpackage Javascript
 * @author Nikolai Plath
 * @author Created on 19-Apr-2012
 * @license GNU/GPL, see JROOT/LICENSE.php
 */

var EcrDeploy = new Class({
    Implements:[Options],

    options:{
        url:''
    },

    initialize:function (options) {
        this.setOptions(options);
    },

    /**
     *
     * @param destination
     * @return {*}
     */
    deployPackage:function (destination) {
        var form = document.id('adminForm');

        var files = '';

        for (i = 0; i < document.adminForm.elements.length; i++) {
            var el = document.adminForm.elements[i];
            if ('checkbox' == el.type
                && 'file[]' == el.name
                && true == el.checked
                ) {
                files += '&file[]=' + el.value;
            }
        }

        if ('' == files) {
            alert(jgettext('Please choose one or more files to deploy'));

            return false;
        }

        switch (destination) {
            case 'github' :
                var data = {
                    owner:document.id('githubRepoOwner').value,
                    repo:document.id('githubRepoName').value,
                    user:document.id('githubUser').value,
                    pass:document.id('githubPass').value
                };
                break;

            case 'ftp' :
                var data = {
                    ftpHost:document.id('ftpHost').value,
                    ftpPort:document.id('ftpPort').value,
                    ftpUser:document.id('ftpUser').value,
                    ftpPass:document.id('ftpPass').value,
                    ftpDirectory:document.id('ftpDirectory').value
                };
                break;

            default:
                alert('Unknown destination: ' + destination);
                return;
                break;
        }

        elements = form.elements;

        var url = 'index.php?option=com_easycreator&tmpl=component&format=raw';
        url += '&controller=deploy&task=deployPackages';
        url += '&type=' + destination;
        url += '&ecr_project=' + document.id('ecr_project');
        url += files;

        var box = document.id(destination + 'DeployMessage');
        var debug = document.id(destination + 'DeployDebug');

        new Request({
            url:url,
            data:data,
            onRequest:function () {
                box.style.color = 'black';
                box.innerHTML = jgettext(php2js.sprintf('Deploying to %s...', destination));
                box.className = 'ajax_loading16';

                startPoll();
            },

            onComplete:function (response) {
                resp = JSON.decode(response);

                if (resp.status) {
                    box.style.color = 'red';
                    debug.set('text', resp.debug);
                } else {
                    box.style.color = 'green';
                    box.set('text', resp.message);
                    box.className = '';

//                    EcrDeploy.getList(destination);
                }

                stopPoll();
            }
        }).send();
    },

    /**
     *
     * @param destination
     * @return {*}
     */
    deployFiles:function (destination) {
        var files = '';

        $$('ul.syncList li input').each(function (input) {
            if (input.checked) {
                files += '&file[]=' + input.value;
            }
        });

        if ('' == files) {
            alert(jgettext('Please choose one or more files to deploy'));

            return false;
        }

        switch (destination) {
            case 'ftp' :
                var data = this._getCredentials(destination);
                var datax = {
                    ftpHost:document.id('ftpHost').value,
                    ftpPort:document.id('ftpPort').value,
                    ftpUser:document.id('ftpUser').value,
                    ftpPass:document.id('ftpPass').value,
                    ftpDirectory:document.id('ftpDirectory').value
                };
                break;

            default:
                alert('Unknown destination: ' + destination);
                return;
                break;
        }

        var url = 'index.php?option=com_easycreator&tmpl=component&format=raw';
        url += '&controller=deploy&task=deployFiles';
        url += '&type=' + destination;
        url += '&ecr_project=' + document.id('ecr_project').value;
        url += files;

        var box = document.id(destination + 'Message');
        var debug = document.id(destination + 'Debug');

        new Request({
            url:url,
            data:data,
            onRequest:function () {
                box.style.color = 'black';
                box.innerHTML = jgettext(php2js.sprintf('Deploying to %s...', destination));
                box.className = 'ajax_loading16';

                startPoll();
            },

            onComplete:function (response) {
                resp = JSON.decode(response);

                if (resp.status) {
                    box.style.color = 'red';
                    box.set('text', resp.message);
                    debug.set('text', resp.debug);
                } else {
                    box.style.color = 'green';
                    box.set('text', resp.message);
                    box.className = '';

                    EcrDeploy.getSyncList(destination);
                }

                stopPoll();
            }
        }).send();
    },

    /**
     *
     * @param destination
     */
    getList:function (destination) {
        var task;
        switch (destination) {
            case 'github' :
                task = 'getGitHubDownloads';

                var data = {
                    owner:document.id('githubRepoOwner').value,
                    repo:document.id('githubRepoName').value
//                    user:document.id('githubUser').value,
//                    pass:document.id('githubPass').value,
                };
                break;

            case 'ftp' :
                task = 'getFtpDownloads';

                var data = {
                    ftpHost:document.id('ftpHost').value,
                    ftpPort:document.id('ftpPort').value,
                    ftpUser:document.id('ftpUser').value,
                    ftpPass:document.id('ftpPass').value,
                    ftpDirectory:document.id('ftpDirectory').value
                };
                break;

            default:
                alert('Unknown destination: ' + destination);
                return;
                break;
        }
        var url = 'index.php?option=com_easycreator&tmpl=component&format=raw';
        url += '&controller=deploy&task=' + task;
        url += '&type=' + destination;

        var box = document.id('ajax' + destination + 'Message');
        var debug = document.id('ajax' + destination + 'Debug');
        var display = document.id(destination + 'Display');

        new Request({
            url:url,
            data:data,
            onRequest:function () {
                box.style.color = 'black';
                box.innerHTML = jgettext(php2js.sprintf('Obtaining downloads from: %s', destination));
                box.className = 'ajax_loading16';
                display.set('html', '');

                startPoll();
            },

            onComplete:function (response) {
                resp = JSON.decode(response);

                box.className = '';

                if (resp.status) {
                    box.style.color = 'red';
                    box.set('text', resp.message);
                    debug = resp.debug;
                } else {
                    box.set('text', '');
                    display.set('html', resp.message);
                }

                stopPoll();
            }
        }).send();
    },

    /**
     *
     */
    getSyncList:function () {

        var url = 'index.php?option=com_easycreator&tmpl=component&format=raw';
        url += '&controller=deploy&task=getSyncList';
        url += '&ecr_project=' + document.id('ecr_project').value;

        var box = document.id('syncList');

        new Request({
            url:url,
            onRequest:function () {
                box.style.color = 'black';
                box.innerHTML = jgettext('Creating synclist...');
                box.className = 'ajax_loading16';
            },

            onComplete:function (response) {
                resp = JSON.decode(response);

                box.className = '';

                if (resp.status) {
                    box.style.color = 'red';
                    box.set('text', resp.message + resp.debug);
                } else {
                    box.set('html', resp.message);
                }
            }
        }).send();
    },

    /**
     *
     * @param destination
     * @param file
     */
    deleteDownload:function (destination, file) {
        var task;

        switch (destination) {
            case 'github' :
                task = 'deleteGitHubDownload';

                var data = {
                    owner:document.id('githubRepoOwner').value,
                    repo:document.id('githubRepoName').value,
                    user:document.id('githubUser').value,
                    pass:document.id('githubPass').value,

                    id:file
                };

                break;

            default:
                alert('Unknown destination: ' + destination);
                return;
                break;
        }
        var url = 'index.php?option=com_easycreator&tmpl=component&format=raw';
        url += '&controller=deploy&task=' + task;

        var box = document.id('ajax' + destination + 'Message');
        var debug = document.id('ajax' + destination + 'Debug');
        var display = document.id(destination + 'Display');

        new Request({
            url:url,
            data:data,
            onRequest:function () {
                box.style.color = 'black';
                box.innerHTML = jgettext(php2js.sprintf('Deleting files on: %s', destination));
                box.className = 'ajax_loading16';
                display.set('html', '');
            },

            onComplete:function (response) {
                resp = JSON.decode(response);

                box.className = '';
                EcrDeploy.getList(destination);

                if (resp.status) {
                    box.style.color = 'red';
                    box.set('text', resp.message);
                    debug = resp.debug;
                } else {
                    box.set('text', '');
                    display.set('html', resp.message);
                }

            }
        }).send();
    },

    /**
     *
     * @param destination
     */
    syncFiles:function (destination) {
        var task;

        switch (destination) {
            case 'ftp' :
                task = 'syncFiles';

                var data = {
                    ftpHost:document.id('ftpHost').value,
                    ftpPort:document.id('ftpPort').value,
                    ftpUser:document.id('ftpUser').value,
                    ftpPass:document.id('ftpPass').value,
                    ftpDirectory:document.id('ftpDirectory').value
                };
                break;

            default:
                alert('Unknown destination: ' + destination);
                return;
                break;
        }

        var url = 'index.php?option=com_easycreator&tmpl=component&format=raw';
        url += '&controller=deploy&task=' + task;
        url += '&type=' + destination;
        url += '&ecr_project=' + document.id('ecr_project').value;

        var box = document.id(destination + 'Message');
        var debug = document.id(destination + 'Debug');
        var display = document.id(destination + 'Display');

        new Request({
            url:url,
            data:data,
            onRequest:function () {
                box.style.color = 'black';
                box.innerHTML = jgettext(php2js.sprintf('Synchronizing files on: %s', destination));
                box.className = 'ajax_loading16';
                display.set('html', '');

                startPoll();
            },

            onComplete:function (response) {
                resp = JSON.decode(response);

                box.className = '';

                stopPoll();

                EcrDeploy.getSyncList();

                if (resp.status) {
                    box.style.color = 'red';
                    box.set('text', resp.message);
                    debug.set('html', resp.debug);
                } else {
                    box.set('text', '');
                    display.set('html', resp.message);
                }
            }
        }).send();
    },

    /**
     * Check all checkboxes.
     *
     * @param type
     */
    checkAll:function (type) {
        type = (undefined == type) ? '' : '.' + type;

        $$('ul.syncList li' + type + ' input').each(function (e) {
            e.checked = 'checked'
        });
    },

    /**
     * Uncheck all checkboxes.
     */
    uncheckAll:function () {
        $$('ul.syncList li input').each(function (e) {
            e.checked = ''
        });
    },


    /**
     *
     * @param destination
     * @return {*}
     * @private
     */
    _getCredentials:function (destination) {
        switch (destination) {
            case 'ftp' :
                var credentials = {
                    ftpHost:document.id('ftpHost').value,
                    ftpPort:document.id('ftpPort').value,
                    ftpUser:document.id('ftpUser').value,
                    ftpPass:document.id('ftpPass').value,
                    ftpDirectory:document.id('ftpDirectory').value
                };

                return credentials;
                break;

            default:
                alert('Unknown destination: ' + destination);
                return;
                break;
        }
    }

});

var EcrDeploy = new EcrDeploy;
