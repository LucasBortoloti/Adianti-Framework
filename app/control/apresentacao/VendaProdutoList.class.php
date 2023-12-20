<?php

use Adianti\Control\TPage;

/**
 * VendaProdutoList Listing
 * @author Lucas Bortoloti <bortoloti91@gmail.com
 */
class VendaProdutoList extends TPage
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
        $this->setActiveRecord('VendaProduto');   // defines the active record
        $this->setDefaultOrder('venda_id, produto_id', 'asc');         // defines the default order
        $this->setLimit(10);
        // $this->setCriteria($criteria) // define a standard filter

        // add the filter fields ('filterField', 'operator', 'formField') 
        $this->addFilterField('quantidade', '=', 'quantidade');
        $this->addFilterField('venda_id', '=', 'venda_id');
        $this->addFilterField('produto_id', '=', 'produto_id');

        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_VendaProduto');
        $this->form->setFormTitle('Venda Produto');

        // create the form fields 
        $quantidade = new TEntry('quantidade');
        $venda_id = new TDBCombo('venda_id', 'venda', 'Venda', 'id', 'cliente');
        $produto_id = new TDBCombo('produto_id', 'venda', 'Produto', 'id', 'nome');

        // add the fields 
        $this->form->addFields([new TLabel('Quantidade')], [$quantidade]);
        $this->form->addFields([new TLabel('Venda')], [$venda_id]);
        $this->form->addFields([new TLabel('Produto')], [$produto_id]);

        // keep the form filled during navigation with session data
        $this->form->setData(TSession::getValue(__CLASS__ . '_filter_data'));

        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['VendaProdutoForm', 'onEdit']), 'fa:plus green');

        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');


        // creates the datagrid columns 
        $column_quantidade = new TDataGridColumn('quantidade', 'Quantidade', 'left');
        $column_venda_id = new TDataGridColumn('venda->cliente', 'Venda', 'left');
        $column_produto_id = new TDataGridColumn('produto->nome', 'Produto', 'left');

        // add the columns to the DataGrid 
        $this->datagrid->addColumn($column_quantidade);
        $this->datagrid->addColumn($column_venda_id);
        $this->datagrid->addColumn($column_produto_id);

        $action1 = new TDataGridAction(['VendaProdutoForm', 'onEdit'], ['venda_id' => '{venda_id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['venda_id' => '{venda_id}']);

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
