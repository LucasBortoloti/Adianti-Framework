<?php

/**
 * JogosForm Form
 * @author Marcelo Barreto Nees <marcelo.linux@gmail.com>
 */
class JogossForm extends TPage
{
    protected $form; // form

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct($param)
    {

        parent::__construct();


        // creates the form
        $this->form = new BootstrapFormBuilder('form_Jogos');
        $this->form->setFormTitle('Jogos');


        // create the form fields 
        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $ano_lancamento = new TEntry('ano_lancamento');
        $quantidade_avaliacoes = new TEntry('quantidade_avaliacoes');
        $desenvolvedoras_id = new TEntry('desenvolvedoras_id');
        $thumbnail = new TEntry('thumbnail');
        $sinopse = new TEntry('sinopse');
        $avaliacoes = new TEntry('avaliacoes');
        $vendas = new TEntry('vendas');


        // add the fields 
        $this->form->addFields([new TLabel('id')], [$id]);
        $this->form->addFields([new TLabel('nome')], [$nome]);
        $this->form->addFields([new TLabel('ano_lancamento')], [$ano_lancamento]);
        $this->form->addFields([new TLabel('quantidade_avaliacoes')], [$quantidade_avaliacoes]);
        $this->form->addFields([new TLabel('desenvolvedoras_id')], [$desenvolvedoras_id]);
        $this->form->addFields([new TLabel('thumbnail')], [$thumbnail]);
        $this->form->addFields([new TLabel('sinopse')], [$sinopse]);
        $this->form->addFields([new TLabel('avaliacoes')], [$avaliacoes]);
        $this->form->addFields([new TLabel('vendas')], [$vendas]);



        if (!empty($id)) {
            $id->setEditable(FALSE);
        }

        /** samples
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( '100%' ); // set size
         **/

        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);


        parent::add($container);
    }

    /**
     * Save form data
     * @param $param Request
     */
    public function onSave($param)
    {
        try {
            TTransaction::open('jogos'); // open a transaction

            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
             **/

            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array

            $object = new Jogos;  // create an empty object
            $object->fromArray((array) $data); // load the object with data
            $object->store(); // save the object

            // get the generated id
            $data->id = $object->id;

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
        } catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData($this->form->getData()); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }

    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear($param)
    {
        $this->form->clear(TRUE);
    }

    /**
     * Load object to form data
     * @param $param Request
     */
    public function onEdit($param)
    {
        try {
            if (isset($param['key'])) {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('jogos'); // open a transaction
                $object = new Jogos($key); // instantiates the Active Record
                $this->form->setData($object); // fill the form
                TTransaction::close(); // close the transaction
            } else {
                $this->form->clear(TRUE);
            }
        } catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
}
