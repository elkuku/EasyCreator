/**
 * @package _ECR_COM_NAME_
 * @subpackage Javascript
 */

/**
 * Submit the form.
 * 
 * @param string task
 * 
 * @return true to submit
 */
function submitbutton(task)
{
    if(task == '')
    {
        return false;
    }
    else
    {
        var isValid=true;
        
        if(task != '_ECR_COM_NAME_.cancel' && task != '_ECR_COM_NAME_.close')
        {
            var forms = $$('form.form-validate');

            for(var i=0; i < forms.length; i++)
            {
                if( ! document.formvalidator.isValid(forms[i]))
                {
                    isValid = false;
                    
                    break;
                }
            }//for
        }
 
        if(isValid)
        {
            submitform(task);
            
            return true;
        }
        else
        {
            alert(Joomla.JText._('Some values are unacceptable'));
            
            return false;
        }
    }
}//function
