<?php
/**
 * @copyright    Nikolai Plath
 * @author        Created on 01-Oct-2017
 * @license     GNU/GPL, see JROOT/LICENSE.php
 */

?>
<script type="text/x-tmpl" id="tmpl-releases">
    <form enctype="multipart/form-data" action="index.php" method="post" name="installWebForm">
        <div>
            <ul>
            {% for (var rel in o) { %}
                <li>{%= rel %}</li>
                <ul>
                {% for (var i=0; i< o[rel].assets.length; i++) { %}
                    <li>
                        <label>
                            <input type="checkbox" name="packages[]" value="{%= o[rel].assets[i].browser_download_url %}">
                            {%= o[rel].assets[i].name %}
                        </label>
                    </li>
                {% } %}
                </ul>
            {% } %}
            </ul>
            <input class="btn btn-success btn-large" type="button"
                   value="<?php echo jgettext('Download and install packages'); ?>"
                   onclick="submitInstallWebForm();"/>

            <input type="hidden" name="option" value="com_easycreator"/>
            <input type="hidden" name="controller" value="templates"/>
            <input type="hidden" name="task" value="do_installweb"/>
        </div>
    </form>
</script>
