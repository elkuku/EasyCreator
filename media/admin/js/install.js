var EcrInstallWebClass = new Class({
    fetchReleases: function (el) {
        var container = document.getElementById(el);
        var outer = this;

        container.innerHTML = 'Fetching releases from GitHub... ';

        Joomla.request({
            url: 'index.php?option=com_easycreator&controller=templates&task=fetchTemplates&tmpl=component&format=raw ',
            onSuccess: function(response, xhr){
                outer.updateScreen(container, JSON.parse(response));
            }
        });
    },

    updateScreen: function (container, gitHubReleases) {
        container.innerHTML = tmpl('tmpl-releases', gitHubReleases);
    }
});

var EcrInstallWeb = new EcrInstallWebClass();


function submitInstallForm()
{
    if($('install_package').value == '')
    {
        alert(jgettext('Please select a package to upload'));

        return;
    }

    document.installForm.submit();
}
function submitInstallWebForm()
{
    if(0)//$('install_package').value == '')
    {
        alert(jgettext('Please select a package to upload'));

        return;
    }

    document.installWebForm.submit();
}
