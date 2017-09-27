<?php
##*HEADER*##

jimport('joomla.plugin.plugin');

/**
 * Content Plugin.
 *
 * @package    ECR_COM_NAME
 * @subpackage Plugin
 */
class plgContentECR_COM_NAME extends JPlugin
{
    /**
     * Example after delete method.
     *
     * @param  string  $context  The context for the content passed to the plugin.
     * @param  object  $data     The data relating to the content that was deleted.
     *
     * @return boolean
     */
    public function onContentAfterDelete($context, $data)
    {
        return true;
    }

    /**
     * Example after display content method
     *
     * Method is called by the view and the results are imploded and displayed in a placeholder
     *
     * @param  string  $context     The context for the content passed to the plugin.
     * @param  object  &$article    The content object.  Note $article->text is also available
     * @param  object  &$params     The content params
     * @param  int     $limitstart  The 'page' number
     *
     * @return string
     */
    public function onContentAfterDisplay($context, &$article, &$params, $limitstart)
    {
        return '';
    }

    /**
     * Example after save content method
     * Article is passed by reference, but after the save, so no changes will be saved.
     * Method is called right after the content is saved
     *
     * @param  string  $context   The context of the content passed to the plugin (added in 1.6)
     * @param  object  &$article  A JTableContent object
     * @param  bool    $isNew     If the content is just about to be created
     *
     * @return boolean
     */
    public function onContentAfterSave($context, &$article, $isNew)
    {
        return true;
    }

    /**
     * Example after display title method
     *
     * Method is called by the view and the results are imploded and displayed in a placeholder
     *
     * @param  string  $context     The context for the content passed to the plugin.
     * @param  object  &$article    The content object.  Note $article->text is also available
     * @param  object  &$params     The content params
     * @param  int     $limitstart  The 'page' number
     *
     * @return  string
     */
    public function onContentAfterTitle($context, &$article, &$params, $limitstart)
    {
        return '';
    }

    /**
     * Example before delete method.
     *
     * @param  string  $context  The context for the content passed to the plugin.
     * @param  object  $data     The data relating to the content that is to be deleted.
     *
     * @return  boolean
     */
    public function onContentBeforeDelete($context, $data)
    {
        return true;
    }

    /**
     * Example before display content method
     *
     * Method is called by the view and the results are imploded and displayed in a placeholder
     *
     * @param  string  $context     The context for the content passed to the plugin.
     * @param  object  &$article    The content object.  Note $article->text is also available
     * @param  object  &$params     The content params
     * @param  int     $limitstart  The 'page' number
     *
     * @return string
     */
    public function onContentBeforeDisplay($context, &$article, &$params, $limitstart)
    {
        return '';
    }

    /**
     * Example before save content method
     *
     * Method is called right before content is saved into the database.
     * Article object is passed by reference, so any changes will be saved!
     * NOTE:  Returning false will abort the save with an error.
     * You can set the error by calling $article->setError($message)
     *
     * @param  string  $context   The context of the content passed to the plugin.
     * @param  object  &$article  A JTableContent object
     * @param  bool    $isNew     If the content is just about to be created
     *
     * @return  boolean  If false, abort the save
     */
    public function onContentBeforeSave($context, &$article, $isNew)
    {
        return true;
    }

    /**
     * Example after delete method.
     *
     * @param  string  $context  The context for the content passed to the plugin.
     * @param  array   $pks      A list of primary key ids of the content that has changed state.
     * @param  int     $value    The value of the state that the content has been changed to.
     *
     * @return  boolean
     */
    public function onContentChangeState($context, $pks, $value)
    {
        return true;
    }

    /**
     * Example prepare content method
     *
     * Method is called by the view
     *
     * @param  string  $context     The context of the content being passed to the plugin.
     * @param  object  &$article    The content object.  Note $article->text is also available
     * @param  object  &$params     The content params
     * @param  int     $limitstart  The 'page' number
     */
    public function onContentPrepare($context, &$article, &$params, $limitstart)
    {
    }
}
