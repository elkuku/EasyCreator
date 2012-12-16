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
    urlAdd:'',
    box:'',

    initialize:function(options)
    {
        this.setOptions(options);

        this.url = 'index.php?option=com_easycreator'
            + '&tmpl=component'
            + '&format=raw'
            + '&controller=deploy';
    },

    /**
     *
     * @param deployTarget
     * @return {*}
     */
    deployPackage:function(deployTarget)
    {
        var files = '';

        $$('table.adminlist input').each(function(input)
        {
            if(input.checked)
            {
                files += '&file[]=' + input.value;
            }
        });

        if('' == files)
        {
            alert(jgettext('Please choose one or more files to deploy'));

            return false;
        }

        this.urlAdd = files;

        var data = this._getCredentials(deployTarget, 'deployPackages');

        var containers = {
            status:document.id(deployTarget + 'DeployMessage'),
            debug:document.id(deployTarget + 'DeployDebug'),
            display:document.id(deployTarget + 'DeployDebug')
        };

        startPoll();

        this._send(containers, data
            , php2js.sprintf(jgettext('Deploying to %s'), deployTarget)
            , 'getPackageList', deployTarget
        );
    },

    /**
     *
     * @param deployTarget
     * @return {*}
     */
    deployFiles:function(deployTarget)
    {
        var files = '';
        var deletedFiles = '';

        $$('div#syncTree div input').each(function(input)
        {
            if(input.checked)
            {
                if(input.value == 'deleted')
                {
                    deletedFiles += '&deletedfiles[]=' + input.id;
                }
                else
                {
                    files += '&files[]=' + input.id;
                }
            }
        });

        if('' == files && '' == deletedFiles)
        {
            alert(jgettext('Please choose one or more files to deploy'));

            return false;
        }

        this.urlAdd = files + deletedFiles;

        var data = this._getCredentials(deployTarget, 'deployFiles');

        var containers = {
            status:document.id(deployTarget + 'Message'),
            debug:document.id(deployTarget + 'Debug'),
            display:document.id(deployTarget + 'Debug')
        };

        startPoll();

        this._send(containers, data
            , php2js.sprintf(jgettext('Deploying to %s'), deployTarget)
            , 'getSyncList', deployTarget
        );
    },

    /**
     *
     * @param deployTarget
     */
    getPackageList:function(deployTarget, logMode)
    {
        var data = this._getCredentials(deployTarget, 'getPackageList');

        data.logMode = (undefined == logMode) ? '' : logMode;

        var containers = {
            status:document.id('ajax' + deployTarget + 'Message'),
            debug:document.id('ajax' + deployTarget + 'Debug'),
            display:document.id(deployTarget + 'Display')
        };

        startPoll();

        this._send(containers, data, php2js.sprintf(jgettext('Obtaining downloads from: %s'), deployTarget))
    },

    /**
     *
     */
    getSyncList:function(deployTarget)
    {
        var containers = {
            status:document.id('syncList'),
            debug:document.id('syncList'),
            display:document.id('syncList')
        };

        var data = this._getCredentials(deployTarget, 'getSyncList');

        this._send(containers, data, jgettext('Generating synchronization list...'));
    },

    /**
     *
     * @param deployTarget
     * @param file
     */
    deletePackage:function(deployTarget, file)
    {
        try
        {
            var data = this._getCredentials(deployTarget, 'deletePackage');

            switch(deployTarget)
            {
                case 'github' :
                    data.id = file;
                    break;

                case 'ftp' :
                    data.file = file;
                    break;

                default:
                    throw('Unknown deploy target: ' + deployTarget);
                    break;
            }

            var containers = {
                status:document.id('ajax' + deployTarget + 'Message'),
                debug:document.id('ajax' + deployTarget + 'Debug'),
                display:document.id(deployTarget + 'Display')
            };

            startPoll();

            this._send(containers, data
                , php2js.sprintf(jgettext('Deleting files on: %s'), deployTarget)
                , 'getPackageList', deployTarget
            );
        }
        catch(e)
        {
            debug.innerHTML = e;
        }
    },

    /**
     *
     * @param deployTarget
     */
    syncFiles:function(deployTarget)
    {
        try
        {
            var data = this._getCredentials(deployTarget, 'syncFiles');

            var containers = {
                status:document.id(deployTarget + 'Message'),
                debug:document.id(deployTarget + 'Debug'),
                display:document.id(deployTarget + 'Display')
            };

            startPoll();

            this._send(containers, data
                , php2js.sprintf(jgettext('Synchronizing files on: %s'), deployTarget)
            );
        }
        catch(e)
        {
            alert(e);
        }
    },

    saveUpdateFiles:function()
    {
        alert('he');
    },

    addUpdateFile:function()
    {
        //addUpdateServer:function (name, url, type, priority) {
        var container = document.id('updateFiles');
        var html = '';

        /*
         html += jgettext('URL') + ': <input type="text" name="updateServers[url][]" value="' + url + '" /><br />';
         html += jgettext('Name') + ': <input type="text" name="updateServers[name][]" value="' + name + '" /><br />';
         html += jgettext('Priority') + ': <input type="text" size="2" name="updateServers[priority][]" value="' + priority + '" /> ';
         html += jgettext('Type') + ': <input type="text" size="8" name="updateServers[type][]" value="' + type + '" /><br />';
         */

        html += '<div class="buttons">';
        html += '<a href="javascript:;" class="btn" onclick="this.getParent().dispose();">';
        html += jgettext('Delete');

        html += '</a>';
        html += '<a href="javascript:;" class="btn" onclick="EcrDeploy.addUpdateFiles(this);">'
            + jgettext('Add files')
            + '</a>';
        html += '</div>';

        var div = new Element('div', {'style':'border: 1px dashed gray; padding: 0.4em; margin: 0.2em;'});

        div.set('html', html);

        div.inject(container);
    },

    addUpdateFiles:function(target)
    {
        var files = '';

        var html = 'hahaha';

        $$('table.adminlist input').each(function(input)
        {
            if(input.checked)
            {
                files += '&file[]=' + input.value;
            }
        });

        if('' == files)
        {
            alert(jgettext('Please choose one or more files to deploy'));

            return false;
        }

        document.id(target).inject(html);
    },

    addUpdateItem:function(name, description, element, type, version, infourl, downloads, targetVersion)
    {
        html += jgettext('URL') + ': <input type="text" name="updateServers[url][]" value="' + url + '" /><br />';
        html += jgettext('Name') + ': <input type="text" name="updateServers[name][]" value="' + name + '" /><br />';
        html += jgettext('Priority') + ': <input type="text" size="2" name="updateServers[priority][]" value="' + priority + '" /> ';
        html += jgettext('Type') + ': <input type="text" size="8" name="updateServers[type][]" value="' + type + '" /><br />';

        return html;
    },

    /**
     * Check all checkboxes.
     *
     * @param type
     */
    checkAll:function(type)
    {
        type = (undefined == type) ? '' : '.' + type;

        $$('div#syncTree div' + type + ' input').each(function(e)
        {
            e.checked = 'checked';
        });
    },

    /**
     * Uncheck all checkboxes.
     */
    uncheckAll:function()
    {
        $$('div#syncTree div input').each(function(e)
        {
            e.checked = '';
        });
    },

    _send:function(containers, data, message, additional, deployTarget)
    {
        new Request({
            url:this.url + this.urlAdd,
            data:data,

            onRequest:function()
            {
                containers.status.style.color = 'black';
                containers.status.innerHTML = message;
                containers.status.className = 'ajax_loading16';
            },

            onComplete:function(response)
            {
                resp = JSON.decode(response);

                containers.status.className = '';

                if(resp.status)
                {
                    containers.status.style.color = 'red';
                    containers.status.set('text', resp.message);
                    containers.debug.set('text', resp.debug);
                } else
                {
                    containers.status.set('text', '');
                    containers.debug.set('text', '');
                    containers.display.set('html', resp.message);
                }

                stopPoll();

                if(additional)
                {
                    switch(additional)
                    {
                        case 'getPackageList':
                            EcrDeploy.getPackageList(deployTarget, 'preserve');
                            break;

                        case 'getSyncList':
                            EcrDeploy.getSyncList(deployTarget);
                            break;

                        default:
                            console.log('Unknown additinal:' + additional);
                            break;
                    }
                }
            },

            onFailure:function()
            {
                containers.status.style.color = 'red';
                containers.status.set('text', 'The request failed');
                containers.status.className = '';
                //debug.set('html', resp.debug);
            }
        }).send();
    },

    /**
     *
     * @param deployTarget
     * @return {*}
     * @private
     */
    _getCredentials:function(deployTarget, task)
    {
        var data = null;

        switch(deployTarget)
        {
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
                throw('Unknown deploy targetx: ' + deployTarget);
                break;
        }

        data.task = task;
        data.deployTarget = deployTarget;
        data.ecr_project = document.id('ecr_project').value;

        return data;
    }
});

var EcrDeploy = new EcrDeploy;
