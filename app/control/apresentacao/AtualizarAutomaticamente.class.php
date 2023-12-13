<?php

use Adianti\Control\TPage;

class AtualizarAutomaticamente extends TPage
{
    protected $datagrid;
    protected $pageNavigation;
    
    use Adianti\Base\AdiantiStandardListTrait;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('venda');
        $this->setActiveRecord('Produto');
        $this->setDefaultOrder('id', 'asc');
        // add the filter (filter field, operator, form field)
        $this->addFilterField('nome', 'like', 'nome');
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_Produto');
        $this->form->setFormTitle(('Atualizar dados'));
        
        // create the form fields
        $nome = new TEntry('nome');
        $this->form->addFields( [new TLabel('Produto')], [$nome] );
        
        $this->form->addAction(('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        
        // keep the form filled with session data
        $this->form->setData( TSession::getValue('Produto_filter_data') );
        
        // creates the datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        
        // create the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_nome = new TDataGridColumn('nome', 'Nome', 'left');
        $column_estoque = new TDataGridColumn('estoque', 'Estoque', 'right');
        $column_sale_price = new TDataGridColumn('sale_price_widget', 'Sale Price', 'right');
        
        $column_estoque->setTransformer( function($value, $object, $row) {
            $widget = new TEntry('estoque' . '_'. $object->id);
            
            $widget->setNumericMask(',','.', true);
            $widget->setValue( $object->estoque);
            
            $widget->setSize(120);
            $widget->setFormName('form_search_Produto');
            
            $action = new TAction( [$this, 'onSaveInline'],
                                   ['column' => 'estoque'] );
            
            $widget->setExitAction( $action );
            return $widget;
        });

        $column_sale_price->setTransformer( function($value, $object, $row) {
            $widget = new TEntry('sale_price' . '_'. $object->id);
            
            $widget->setNumericMask(2,',','.', true);
            $widget->setValue( $object->sale_price);
            
            $widget->setSize(120);
            $widget->setFormName('form_search_Produto');
            
            $action = new TAction( [$this, 'onSaveInline'],
                                   ['column' => 'sale_price'] );
            
            $widget->setExitAction( $action );
            return $widget;
        });

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_estoque);
        $this->datagrid->addColumn($column_sale_price);
       
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the pagination
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        
        $this->datagrid->disableDefaultClick();
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }

    public static function onSaveInline($param)
    {
        $name   = $param['_field_name'];
        $value  = $param['_field_value'];
        $column = $param['column'];
        
        $parts  = explode('_', $name);
        $id     = end($parts);
        
        try
        {
            // open transaction
            TTransaction::open('venda');
            
            $object = Produto::find($id);
            if ($object)
            {
                $object->$column = str_replace(['.', ','],['', '.'],$value);
                $object->store();
            }
            
            TToast::show('success', '<b>'.$object->nome . '</b> updated',
                         'bottom center', 'far:check-circle');
            
            // close transaction
            TTransaction::close();
        }
        catch (Exception $e)
        {
            // show the exception message
            TToast::show('error', $e->getMessage(), 'bottom center', 'fa:exclamation-triangle');
        }
    }
}