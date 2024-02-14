<?php

use Adianti\Control\TPage;

/**
 * ProdutoList Listing
 * @author Lucas Bortoloti <bortoloti91@gmail.com
 */
class ProdutoList extends TPage
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    protected $formgrid;
    protected $deleteButton;

    use Adianti\base\AdiantiStandardListTrait;

    /**
     * Page constructor
     */
    public function __construct()
    {

        parent::__construct();

        $this->setDatabase('venda');            // defines the database
        $this->setActiveRecord('Produto');   // defines the active record
        $this->setDefaultOrder('id', 'asc');         // defines the default order
        $this->setLimit(10);
        // $this->setCriteria($criteria) // define a standard filter


        // add the filter fields ('filterField', 'operator', 'formField') 
        $this->addFilterField('id', '=', 'id');
        $this->addFilterField('nome', '=', 'nome');
        $this->addFilterField('sale_price', '=', 'sale_price');



        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_Produto');
        $this->form->setFormTitle('Produto');


        // create the form fields 
        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $sale_price = new TEntry('sale_price');


        // add the fields 
        $this->form->addFields([new TLabel('Id')], [$id]);
        $this->form->addFields([new TLabel('Nome')], [$nome]);
        $this->form->addFields([new TLabel('Preco')], [$sale_price]);



        // keep the form filled during navigation with session data
        $this->form->setData(TSession::getValue(__CLASS__ . '_filter_data'));

        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['ProdutoForm', 'onEdit']), 'fa:plus green');

        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');


        // creates the datagrid columns 
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_nome = new TDataGridColumn('nome', 'Nome', 'left');
        $column_preco = new TDataGridColumn('sale_price', 'Preco', 'left');


        // add the columns to the DataGrid 
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_preco);


        $action1 = new TDataGridAction(['ProdutoForm', 'onEdit'], ['id' => '{id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['id' => '{id}']);

        $this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
        $this->datagrid->addAction($action2, _t('Delete'), 'far:trash-alt red');

        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));

        $panel = new TPanelGroup('', 'white');
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);

        // header actions
        $dropdown = new TDropDown(_t('Export'), 'fa:list');
        $dropdown->setPullSide('right');
        $dropdown->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown->addAction(_t('Save as CSV'), new TAction([$this, 'onExportCSV'], ['register_state' => 'false', 'static' => '1']), 'fa:table blue');
        $dropdown->addAction(_t('Save as PDF'), new TAction([$this, 'onExportPDF'], ['register_state' => 'false', 'static' => '1']), 'far:file-pdf red');
        $panel->addHeaderWidget($dropdown);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($panel);


        parent::add($container);
    }
}
