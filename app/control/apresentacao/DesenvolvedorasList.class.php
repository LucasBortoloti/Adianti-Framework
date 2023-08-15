<?php

use Adianti\Control\TPage;

/**
 * DesenvolvedorasList Listing
 * @author Lucas Bortoloti <bortoloti91@gmail.com
 */
class DesenvolvedorasList extends TPage
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
        
        $this->setDatabase('jogos');            // defines the database
        $this->setActiveRecord('Desenvolvedoras');   // defines the active record
        $this->setDefaultOrder('id', 'asc');         // defines the default order
        $this->setLimit(10);
        // $this->setCriteria($criteria) // define a standard filter


        // add the filter fields ('filterField', 'operator', 'formField') 
        $this->addFilterField('id', '=', 'id');
        $this->addFilterField('nome', '=', 'nome');
        $this->addFilterField('ano_fundacao', '=', 'ano_fundacao');
        $this->addFilterField('assinatura', '=', 'assinatura');
 

        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_Desenvolvedoras');
        $this->form->setFormTitle('Desenvolvedoras');
        

        // create the form fields 
        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $ano_fundacao = new TEntry('ano_fundacao');
        $assinatura = new TEntry('assinatura');
 
 
        // add the fields 
        $this->form->addFields( [ new TLabel('Id')] , [ $id ] );
        $this->form->addFields( [ new TLabel('Nome')] , [ $nome ] );
        $this->form->addFields( [ new TLabel('Ano Fundacao')] , [ $ano_fundacao ] );
        $this->form->addFields( [ new TLabel('Assinatura')] , [ $assinatura ] );
 

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['DesenvolvedorasForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns 
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_nome = new TDataGridColumn('nome', 'Nome', 'left');
        $column_ano_fundacao = new TDataGridColumn('ano_fundacao', 'Ano Fundacao', 'left');
        $column_assinatura = new TDataGridColumn('assinatura', 'Assinatura', 'left');
 
 
        // add the columns to the DataGrid 
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_ano_fundacao);
        $this->datagrid->addColumn($column_assinatura);
 

        
        $action1 = new TDataGridAction(['DesenvolvedorasForm', 'onEdit'], ['id'=>'{id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
        
        $this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
        $this->datagrid->addAction($action2 ,_t('Delete'), 'far:trash-alt red');
        
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
        $dropdown->addAction( _t('Save as CSV'), new TAction([$this, 'onExportCSV'], ['register_state' => 'false', 'static'=>'1']), 'fa:table blue' );
        $dropdown->addAction( _t('Save as PDF'), new TAction([$this, 'onExportPDF'], ['register_state' => 'false', 'static'=>'1']), 'far:file-pdf red' );
        $panel->addHeaderWidget( $dropdown );
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($panel);
        
       
        parent::add($container);
    }
}
