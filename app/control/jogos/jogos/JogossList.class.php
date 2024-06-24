<?php

/**
 * JogosList Listing
 * @author Marcelo Barreto Nees <marcelo.linux@gmail.com>
 */
class JogossList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {

        parent::__construct();

        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_Jogos');
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



        // keep the form filled during navigation with session data
        $this->form->setData(TSession::getValue(__CLASS__ . '_filter_data'));

        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['JogosForm', 'onEdit']), 'fa:plus green');

        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');


        // creates the datagrid columns 
        $column_id = new TDataGridColumn('id', 'id', 'left');
        $column_nome = new TDataGridColumn('nome', 'nome', 'left');
        $column_ano_lancamento = new TDataGridColumn('ano_lancamento', 'ano_lancamento', 'left');
        $column_quantidade_avaliacoes = new TDataGridColumn('quantidade_avaliacoes', 'quantidade_avaliacoes', 'left');
        $column_desenvolvedoras_id = new TDataGridColumn('desenvolvedoras_id', 'desenvolvedoras_id', 'left');
        $column_thumbnail = new TDataGridColumn('thumbnail', 'thumbnail', 'left');
        $column_sinopse = new TDataGridColumn('sinopse', 'sinopse', 'left');
        $column_avaliacoes = new TDataGridColumn('avaliacoes', 'avaliacoes', 'left');
        $column_vendas = new TDataGridColumn('vendas', 'vendas', 'left');


        // add the columns to the DataGrid 
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_ano_lancamento);
        $this->datagrid->addColumn($column_quantidade_avaliacoes);
        $this->datagrid->addColumn($column_desenvolvedoras_id);
        $this->datagrid->addColumn($column_thumbnail);
        $this->datagrid->addColumn($column_sinopse);
        $this->datagrid->addColumn($column_avaliacoes);
        $this->datagrid->addColumn($column_vendas);



        $action1 = new TDataGridAction(['JogosForm', 'onEdit'], ['id' => '{id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['id' => '{id}']);

        $this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
        $this->datagrid->addAction($action2, _t('Delete'), 'far:trash-alt red');

        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));


        parent::add($container);
    }

    /**
     * Inline record editing
     * @param $param Array containing:
     *              key: object ID value
     *              field name: object attribute to be updated
     *              value: new attribute content 
     */
    public function onInlineEdit($param)
    {
        try {
            // get the parameter $key
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];

            TTransaction::open('jogos'); // open a transaction with database
            $object = new Jogos($key); // instantiates the Active Record
            $object->{$field} = $value;
            $object->store(); // update the object in the database
            TTransaction::close(); // close the transaction

            $this->onReload($param); // reload the listing
            new TMessage('info', "Record Updated");
        } catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    /**
     * Register the filter in the session
     */
    public function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();

        // clear session filters



        TSession::setValue('JogosList_filter_id',             NULL);
        TSession::setValue('JogosList_filter_nome',             NULL);
        TSession::setValue('JogosList_filter_ano_lancamento',             NULL);
        TSession::setValue('JogosList_filter_quantidade_avaliacoes',             NULL);
        TSession::setValue('JogosList_filter_desenvolvedoras_id',             NULL);
        TSession::setValue('JogosList_filter_thumbnail',             NULL);
        TSession::setValue('JogosList_filter_sinopse',             NULL);
        TSession::setValue('JogosList_filter_avaliacoes',             NULL);
        TSession::setValue('JogosList_filter_vendas',             NULL);
        if (isset($data->id) and ($data->id)) {
            $filter = new TFilter('id', '=', "{$data->id}");
            TSession::setValue('JogosList_filter_id', $filter);
        }

        if (isset($data->nome) and ($data->nome)) {
            $filter = new TFilter('nome', '=', "{$data->nome}");
            TSession::setValue('JogosList_filter_nome', $filter);
        }

        if (isset($data->ano_lancamento) and ($data->ano_lancamento)) {
            $filter = new TFilter('ano_lancamento', '=', "{$data->ano_lancamento}");
            TSession::setValue('JogosList_filter_ano_lancamento', $filter);
        }

        if (isset($data->quantidade_avaliacoes) and ($data->quantidade_avaliacoes)) {
            $filter = new TFilter('quantidade_avaliacoes', '=', "{$data->quantidade_avaliacoes}");
            TSession::setValue('JogosList_filter_quantidade_avaliacoes', $filter);
        }

        if (isset($data->desenvolvedoras_id) and ($data->desenvolvedoras_id)) {
            $filter = new TFilter('desenvolvedoras_id', '=', "{$data->desenvolvedoras_id}");
            TSession::setValue('JogosList_filter_desenvolvedoras_id', $filter);
        }

        if (isset($data->thumbnail) and ($data->thumbnail)) {
            $filter = new TFilter('thumbnail', '=', "{$data->thumbnail}");
            TSession::setValue('JogosList_filter_thumbnail', $filter);
        }

        if (isset($data->sinopse) and ($data->sinopse)) {
            $filter = new TFilter('sinopse', '=', "{$data->sinopse}");
            TSession::setValue('JogosList_filter_sinopse', $filter);
        }

        if (isset($data->avaliacoes) and ($data->avaliacoes)) {
            $filter = new TFilter('avaliacoes', '=', "{$data->avaliacoes}");
            TSession::setValue('JogosList_filter_avaliacoes', $filter);
        }

        if (isset($data->vendas) and ($data->vendas)) {
            $filter = new TFilter('vendas', '=', "{$data->vendas}");
            TSession::setValue('JogosList_filter_vendas', $filter);
        }



        // fill the form with data again
        $this->form->setData($data);

        // keep the search data in the session
        TSession::setValue(__CLASS__ . '_filter_data', $data);

        $param = array();
        $param['offset']    = 0;
        $param['first_page'] = 1;
        $this->onReload($param);
    }

    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try {
            // open a transaction with database 'jogos'
            TTransaction::open('jogos');

            // creates a repository for Jogos
            $repository = new TRepository('Jogos');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;

            // default order
            if (empty($param['order'])) {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);


            // add the session filters 
            if (TSession::getValue('JogosList_filter_id')) {
                $criteria->add(TSession::getValue('JogosList_filter_id'));
            }

            if (TSession::getValue('JogosList_filter_nome')) {
                $criteria->add(TSession::getValue('JogosList_filter_nome'));
            }

            if (TSession::getValue('JogosList_filter_ano_lancamento')) {
                $criteria->add(TSession::getValue('JogosList_filter_ano_lancamento'));
            }

            if (TSession::getValue('JogosList_filter_quantidade_avaliacoes')) {
                $criteria->add(TSession::getValue('JogosList_filter_quantidade_avaliacoes'));
            }

            if (TSession::getValue('JogosList_filter_desenvolvedoras_id')) {
                $criteria->add(TSession::getValue('JogosList_filter_desenvolvedoras_id'));
            }

            if (TSession::getValue('JogosList_filter_thumbnail')) {
                $criteria->add(TSession::getValue('JogosList_filter_thumbnail'));
            }

            if (TSession::getValue('JogosList_filter_sinopse')) {
                $criteria->add(TSession::getValue('JogosList_filter_sinopse'));
            }

            if (TSession::getValue('JogosList_filter_avaliacoes')) {
                $criteria->add(TSession::getValue('JogosList_filter_avaliacoes'));
            }

            if (TSession::getValue('JogosList_filter_vendas')) {
                $criteria->add(TSession::getValue('JogosList_filter_vendas'));
            }



            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);

            if (is_callable($this->transformCallback)) {
                call_user_func($this->transformCallback, $objects, $param);
            }

            $this->datagrid->clear();
            if ($objects) {
                // iterate the collection of active records
                foreach ($objects as $object) {
                    // add the object inside the datagrid
                    $this->datagrid->addItem($object);
                }
            }

            // reset the criteria for record count
            $criteria->resetProperties();
            $count = $repository->count($criteria);

            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit

            // close the transaction
            TTransaction::close();
            $this->loaded = true;
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    /**
     * Ask before deletion
     */
    public static function onDelete($param)
    {
        // define the delete action
        $action = new TAction([__CLASS__, 'Delete']);
        $action->setParameters($param); // pass the key parameter ahead

        // shows a dialog to the user
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }

    /**
     * Delete a record
     */
    public static function Delete($param)
    {
        try {
            $key = $param['key']; // get the parameter $key
            TTransaction::open('jogos'); // open a transaction with database
            $object = new Jogos($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object from the database
            TTransaction::close(); // close the transaction

            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $pos_action); // success message
        } catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded and (!isset($_GET['method']) or !(in_array($_GET['method'],  array('onReload', 'onSearch'))))) {
            if (func_num_args() > 0) {
                $this->onReload(func_get_arg(0));
            } else {
                $this->onReload();
            }
        }

        parent::show();
    }
}
