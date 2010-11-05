<?php
##*HEADER*##

//-- Import Joomla modelitem library
jimport('joomla.application.component.modelitem');

/**
 * _ECR_COM_NAME_ Model.
 *
 * @package _ECR_COM_NAME_
 */
class _ECR_COM_NAME_Model_ECR_COM_NAME_ extends JModelItem
{
    /**
     * @var object $item
     */
    protected $item;

    /**
     * @var string  $category
     */
    protected $category;

    /**
     * Get the item.
     *
     * @return string The item
     */
    public function getItem()
    {
        if( ! isset($this->item))
        {
            $id = JRequest::getInt('id');

            // Get a JTable instance
            $table = $this->getTable();

            // Load the message
            $table->load($id);

            //-- Add global parameters
            $params = clone JFactory::getApplication('site')->getParams();
            $params->merge($table->params);

            $table->params = $params;

            //-- Assign the item
            $this->item = $table;
        }

        return $this->item;
    }//function

    /**
     * Get the category.
     *
     * @return object The category assigned to the item
     */
    public function getCategory()
    {
        if( ! isset($this->category))
        {
            $catid = $this->getItem()->catid;

            //-- Get a JTable instance
            $table = $this->getTable('Category', 'JTable');

            //-- Load the category
            $table->load($catid);

            //-- Assign the category
            $this->category = $table;
        }

        return $this->category;
    }//function
}//class
