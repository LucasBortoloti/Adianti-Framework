<?php

use Adianti\Control\TPage;

class SaleList extends TPage
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    use Adianti\Base\AdiantiStandardListTrait;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('sale');          // defines the database
        $this->setActiveRecord('Sale');         // defines the active record
        $this->setDefaultOrder('id', 'asc');    // defines the default order
        $this->addFilterField('id', '=', 'id'); // filterField, operator, formField
        $this->addFilterField('cliente_id', '=', 'cliente_id');

        
        $this->addFilterField('date', '=', 'date', function($value) {
            return TDate::convertToMask($value, 'dd/mm/yyyy', 'yyyy-mm-dd');
        });

        $this->form = new BootstrapFormBuilder('form_search_Sale');
        $this->form->setFormTitle(('Sale list'));

        $id        = new TEntry('id');
        $date      = new TDate('date');

        $cliente_id   = new TDBUniqueSearch('cliente_id', 'sale', 'Cliente', 'id', 'nome');
        $cliente_id->setMinLength(1);
        $cliente_id->setMask('{nome} ({id})');

        $this->form->addFields( [new TLabel('Id')],                    [$id]); 
        $this->form->addFields( [new TLabel('Data')],                [$date]);
        $this->form->addFields( [new TLabel('Cliente')],       [$cliente_id]);

        $id->setSize('50%');
        $date->setSize('100%');
        $date->setMask( 'dd/mm/yyyy' );

        $this->form->setData( TSession::getValue('SaleList_filter_data') );

        $this->form->addAction('Find', new TAction([$this, 'onSearch']), 'fa:search');
        $this->form->addActionLink('New',  new TAction(['SaleForm', 'onEdit'], ['register_state' => 'false']), 'fa:plus green');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->width = '100%';

        $column_id       = new TDataGridColumn('id', 'Id', 'center', '10%');
        $column_date     = new TDataGridColumn('date', 'Date', 'center', '10%');
        $column_cliente  = new TDataGridColumn('cliente->nome', 'Cliente', 'left', '40%');
        $column_status   = new TDataGridColumn('status', 'Status', 'center', '20%');
        $column_total    = new TDataGridColumn('total', 'Total', 'center', '20%');
        
        $format_value = function($value) {
            if (is_numeric($value)) {
                return 'R$ '.number_format($value, 2, ',', '.');
            }
            return $value;
        };

        $column_total->setTransformer( $format_value );

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_date);
        $this->datagrid->addColumn($column_cliente);
        $this->datagrid->addColumn($column_status);
        $this->datagrid->addColumn($column_total);
    
        $column_status->setTransformer( function($value, $object, $row, $cell) {
            $cell->href='#';
            $dropdown = new TDropDown($object->status->nome, '');
            $dropdown->getButton()->style .= ';color:white;border-radius:5px;background:'.$object->status->color;
            
            TTransaction::open('sale');
            $statuses = SaleStatus::orderBy('id')->load();
            foreach ($statuses as $status)
            {
                $params = ['id' => $object->id,
                           'status_id' => $status->id, 
                           'offset' => $_REQUEST['offset'] ?? 0,
                           'limit' => $_REQUEST['limit'] ?? 10,
                           'page' => $_REQUEST['page'] ?? 1,
                           'first_page' => $_REQUEST['first_page'] ?? 1,
                           'register_state' => 'false'];
                
                $dropdown->addAction( $status->nome, new TAction([$this, 'changeStatus'], $params ), 'fas:circle  ' . $status->color );
            }
            TTransaction::close();
            
            return $dropdown;
        });

        $column_id->setAction(new TAction([$this, 'onReload']),   ['order' => 'id']);
        $column_date->setAction(new TAction([$this, 'onReload']), ['order' => 'date']);
        
        $column_date->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            return $date->format('d/m/Y');
        });

        $action_view   = new TDataGridAction(['SaleSidePanelView', 'onView'],   ['key' => '{id}', 'register_state' => 'false'] );
        $action_edit   = new TDataGridAction(['SaleForm', 'onEdit'],   ['key' => '{id}', 'register_state' => 'false'] );
        $action_delete = new TDataGridAction([$this, 'onDelete'],   ['key' => '{id}'] );
        
        $this->datagrid->addAction($action_view, ('View details'), 'fa:search green fa-fw');
        $this->datagrid->addAction($action_edit, 'Edit',   'far:edit blue fa-fw');
        $this->datagrid->addAction($action_delete, 'Delete', 'far:trash-alt red fa-fw');
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        //$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($panel = TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        $panel->getBody()->style = 'overflow-x:auto';
        parent::add($container);

    }

    public function changeStatus($param)
    {
        try
        {
            TTransaction::open('sale');
            $sale = Sale::find($param['id']);
            $sale->status_id = $param['status_id'];
            $sale->store();
            TTransaction::close();
            
            $this->onReload($param);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

}
