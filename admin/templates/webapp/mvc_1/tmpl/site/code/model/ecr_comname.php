<?php
##*HEADER*##
/**
 * ECR_COM_NAME _ECR_COM_NAME model.
 *
 * @package     ECR_COM_NAME
 * @subpackage  Model
 */
class ECR_CLASS_PREFIXModelECR_UCF_COM_NAME extends JModelBase
{
    /**
     * @var ECR_CLASS_PREFIXTableECR_UCF_COM_NAME
     */
    private $table;

    /**
     * @var JDatabase
     */
    private $db;

    /**
     * @var int
     */
    private $id = 0;

    /**
     * Instantiate the model.
     *
     * @param   JRegistry  $state  The model state.
     */
    public function __construct(JRegistry $state = null)
    {
        $this->db = JFactory::getDbo();
        $this->id = JFactory::getApplication()->input->getInt('id');
        $this->table = new ECR_CLASS_PREFIXTableECR_UCF_COM_NAME($this->db);

        parent::__construct($state);
    }

    /**
     * Get tha data.
     *
     * @return ECR_CLASS_PREFIXTableECR_UCF_COM_NAME
     *
     * @throws UnexpectedValueException
     */
    public function getData()
    {
        if(0 === $this->id)
            return $this->table;

        if(false === $this->table->load($this->id))
            throw new UnexpectedValueException(sprintf('%s - Failed to load the data for id: %s'
                , __METHOD__, $this->id));

        return $this->table;
    }

    /**
     * Save the data.
     *
     * @return bool
     *
     * @throws UnexpectedValueException
     */
    public function save()
    {
        $input = JFactory::getApplication()->input;

        if(false === $this->table->save($input))
            throw new UnexpectedValueException($this->table->getError(), 1);

        return true;
    }

    /**
     * Delete data.
     *
     * @return mixed
     */
    public function delete()
    {
        $db = JFactory::getDbo();

        $table = new ECR_CLASS_PREFIXTableECR_UCF_COM_NAME($db);

        $input = JFactory::getApplication()->input;

        return $table->delete($input->getInt('id'));
    }
}
