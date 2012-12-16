//##*HEADERJS*##

Joomla.submitbutton = function (task) {
    if (task == '')
    {
        return false;
    }

    var isValid = true;

    var action = task.split('.');

    if (action[1] != 'cancel' && action[1] != 'close')
    {
        var forms = $$('form.form-validate');

        for (var i = 0; i < forms.length; i++)
        {
            if (!document.formvalidator.isValid(forms[i]))
            {
                isValid = false;

                break;
            }
        }
    }

    if (isValid)
    {
        Joomla.submitform(task, document.id('ECR_LOWER_COM_NAME-form'));

        return true;
    }

    alert(Joomla.JText._('ECR_UPPER_COM_COM_NAME_ECR_UPPER_COM_NAME_ERROR_UNACCEPTABLE'));

    return false;
};
