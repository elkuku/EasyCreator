/**
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath
 * @author     Created on 19-Apr-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

var EcrDeploy = new Class({
    Implements:[Options],

    options:{
        url:''
    },

    url:'',

    initialize:function (options) {
        this.setOptions(options);

        this.url = 'index.php?option=com_easycreator'
            + '&tmpl=component'
            + '&format=raw'
            + '&controller=deploy';
    },

    /**
     *
     * @param destination
     * @return {*}
     */
    deployPackage:function (destination) {
        var files = '';

        $$('table.adminlist input').each(function (input) {
            if (input.checked) {
                files += '&file[]=' + input.value;
            }
        });

        if ('' == files) {
            alert(jgettext('Please choose one or more files to deploy'));

            return false;
        }

        var box = document.id(destination + 'DeployMessage');
        var debug = document.id(destination + 'DeployDebug');

        var data = this._getCredentials(destination);

        data.task = 'deployPackages';
        data.type = destination;
        data.ecr_project = document.id('ecr_project');

        new Request({
            url:this.url + files,

            data:data,

            onRequest:function () {
                box.style.color = 'black';
                box.innerHTML = php2js.sprintf(jgettext('Deploying to %s...'), destination);
                box.className = 'ajax_loading16';

                startPoll();
            },

            onComplete:function (response) {
                resp = JSON.decode(response);

                stopPoll();

                box.className = '';

                if (resp.status) {
                    box.style.color = 'red';
                    box.set('text', resp.message);
                    debug.set('text', resp.debug);
                } else {
                    box.style.color = 'green';
                    box.set('text', resp.message);
                }

                EcrDeploy.getPackageList(destination, 'preserve');
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
        var deletedFiles = '';

        $$('div#syncTree div input').each(function (input) {
            if (input.checked) {
                if (input.value == 'deleted') {
                    deletedFiles += '&deletedfiles[]=' + input.id;
                }
                else {
                    files += '&files[]=' + input.id;
                }
            }
        });

        if ('' == files && '' == deletedFiles) {
            alert(jgettext('Please choose one or more files to deploy'));

            return false;
        }

        var box = document.id(destination + 'Message');
        var debug = document.id(destination + 'Debug');

        var data = this._getCredentials(destination);

        data.task = 'deployFiles';
        data.type = destination;
        data.ecr_project = document.id('ecr_project').value;

        new Request({
            url:this.url + files + deletedFiles,

            data:data,

            onRequest:function () {
                box.style.color = 'black';
                box.innerHTML = php2js.sprintf(jgettext('Deploying to %s...'), destination);
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
    getPackageList:function (destination, logMode) {
        var task;

        var data = this._getCredentials(destination);

        data.type = destination;
        data.logMode = (undefined == logMode) ? '' : logMode;
        data.task = 'getPackageList';

        switch (destination) {
            case 'github' :
            case 'ftp' :
                break;

            default:
                alert('Unknown destination: ' + destination);
                return;
                break;
        }

        var box = document.id('ajax' + destination + 'Message');
        var debug = document.id('ajax' + destination + 'Debug');
        var display = document.id(destination + 'Display');

        new Request({
            url:this.url,

            data:data,

            onRequest:function () {
                box.style.color = 'black';
                box.innerHTML = php2js.sprintf(jgettext('Obtaining downloads from: %s'), destination);
                box.className = 'ajax_loading16';
                display.set('html', '');

                startPoll();
            },

            onComplete:function (response) {
                resp = JSON.decode(response);

                box.className = '';

                if (resp.status) {
                    box.style.color = 'red';
                    box.set('test', '');
                    display.set('html', resp.message);
                    debug.set('html', resp.debug);
                } else {
                    box.set('text', '');
                    display.set('html', resp.message);
                    debug.set('html', resp.debug);
                }

                stopPoll();
            }
        }).send();
    },

    /**
     *
     */
    getSyncList:function () {
        var box = document.id('syncList');

        var data = {
            task:'getSyncList',
            ecr_project:document.id('ecr_project').value
        };

        new Request({

            url:this.url,

            data:data,

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
    deletePackage:function (destination, file) {
        var data = this._getCredentials(destination);

        data.type = destination;
        data.task = 'deletePackage';

        switch (destination) {
            case 'github' :
                data.id = file;
                break;

            case 'ftp' :
                data.file = file;
                break;

            default:
                alert('Unknown destination: ' + destination);

                return;
                break;
        }

        var box = document.id('ajax' + destination + 'Message');
        var debug = document.id('ajax' + destination + 'Debug');
        var display = document.id(destination + 'Display');

        new Request({
            url:this.url,

            data:data,

            onRequest:function () {
                box.style.color = 'black';
                box.innerHTML = php2js.sprintf(jgettext('Deleting files on: %s'), destination);
                box.className = 'ajax_loading16';
                display.set('html', '');

                startPoll();
            },

            onComplete:function (response) {
                resp = JSON.decode(response);

                box.className = '';

                stopPoll();

                if (resp.status) {
                    box.style.color = 'red';
                    box.set('text', resp.message);
                    debug = resp.debug;
                } else {
                    box.set('text', '');
                    display.set('html', resp.message);

                    EcrDeploy.getPackageList(destination, 'preserve');
                }
            }
        }).send();
    },

    /**
     *
     * @param destination
     */
    syncFiles:function (destination) {
        var data = this._getCredentials(destination);

        data.task = 'syncFiles';
        data.type = destination;
        data.ecr_project = document.id('ecr_project').value;

        var box = document.id(destination + 'Message');
        var debug = document.id(destination + 'Debug');
        var display = document.id(destination + 'Display');

        new Request({
            url:this.url,

            data:data,

            onRequest:function () {
                box.style.color = 'black';
                box.innerHTML = php2js.sprintf(jgettext('Synchronizing files on: %s'), destination);

                box.className = 'ajax_loading16';
                display.set('html', '');

                startPoll();
            },

            onFailure:function () {
                box.style.color = 'red';
                box.set('text', 'The request failed');
                box.className = '';
                //debug.set('html', resp.debug);
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

        $$('div#syncTree div' + type + ' input').each(function (e) {
            e.checked = 'checked';
        });
    },

    /**
     * Uncheck all checkboxes.
     */
    uncheckAll:function () {
        $$('div#syncTree div input').each(function (e) {
            e.checked = '';
        });
    },

    /**
     *
     * @param destination
     * @return {*}
     * @private
     */
    _getCredentials:function (destination) {
        var data = null;

        switch (destination) {
            case 'ftp' :
                data = {
                    ftpHost:document.id('ftpHost').value,
                    ftpPort:document.id('ftpPort').value,
                    ftpUser:document.id('ftpUser').value,
                    ftpPass:document.id('ftpPass').value,
                    ftpDirectory:document.id('ftpDirectory').value,
                    ftpDownloads:document.id('ftpDownloads').value
                };
                break;

            case 'github' :
                data = {
                    owner:document.id('githubRepoOwner').value,
                    repo:document.id('githubRepoName').value,
                    user:document.id('githubUser').value,
                    pass:document.id('githubPass').value
                };
                break;

            default:
                throw('Unknown destination: ' + destination);
                break;
        }

        return data;
    }

});

var EcrDeploy = new EcrDeploy;
