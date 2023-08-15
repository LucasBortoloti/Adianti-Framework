<?php

use Adianti\Control\TPage;

class SaleForm extends TPage
{
    protected $form; // form

    function __construct()
    {
        parent::__construct();
        $this->setTargetContainer('adianti_right_panel');

        // creates the form
        $this->form = new BootstrapFormBuilder('form_Sale');
        $this->form->setFormTitle('Sale');
        $this->form->setProperty('style', 'margin:0;border:0');

        $id        = new TEntry('id');
        $date      = new TDate('date');
        $cliente_id   = new TDBUniqueSearch('cliente_id', 'sale', 'Cliente', 'id', 'nome');

        $product_detail_unqid      = new THidden('product_detail_uniqid');
        $product_detail_id         = new THidden('product_detail_id');
        $product_detail_product_id = new TDBUniqueSearch('product_detail_product_id', 'sale', 'Product', 'id', 'nome');
        $product_detail_preco      = new TEntry('product_detail_preco');
        $product_detail_quantidade = new TEntry('product_detail_quantidade');
        $product_detail_discount   = new TEntry('product_detail_discount');
        $product_detail_total      = new TEntry('product_detail_total');

        $id->setEditable(false);
        $cliente_id->setMask('{nome} ({id})');
        $cliente_id->setSize('calc(100% - 30px)');
        $cliente_id->setMinLength(1);
        $date->setSize('100%');
        $product_detail_product_id->setSize('100%');
        $product_detail_product_id->setMinLength(1);
        $product_detail_preco->setSize('100%');
        $product_detail_quantidade->setSize('100%');
        $product_detail_discount->setSize('100%');

    
        $date->addValidation('Date', new TRequiredValidator);
        $cliente_id->addValidation('Customer', new TRequiredValidator);


        $product_detail_product_id->setChangeAction(new TAction([$this,'onProductChange']));
        
        $this->form->addFields( [new TLabel('Id')], [$id],
                                [new TLabel('Data (*)', '#FF0000')], [$date] );
        $this->form->addFields( [new TLabel('Cliente (*)', '#FF0000')], [$cliente_id] );

        $this->form->addContent( ['<h4>Details</h4><hr>'] );
        $this->form->addFields( [ $product_detail_unqid], [$product_detail_id] );  
        $this->form->addFields( [ new TLabel('Product (*)', '#FF0000') ], [$product_detail_product_id],
                                [ new TLabel('Quantidade (*)', '#FF0000') ],   [$product_detail_quantidade]);
        $this->form->addFields( [ new TLabel('Preco (*)', '#FF0000') ],   [$product_detail_preco],
                                [ new TLabel('Desconto', '#FF0000')],     [$product_detail_discount]);

        $add_product = TButton::create('add_product', [$this, 'onProductAdd'], 'Register', 'fa:plus-circle green');
        $add_product->getAction()->setParameter('static','1');
        $this->form->addFields( [], [$add_product] );

        $this->product_list = new BootstrapDatagridWrapper(new TDataGrid);
        $this->product_list->setHeight(150);
        $this->product_list->makeScrollable();
        $this->product_list->setId('products_list');
        $this->product_list->generateHiddenFields();
        $this->product_list->style = "min-width: 700px; width:100%;margin-bottom: 10px";
        $this->product_list->setMutationAction(new TAction([$this, 'onMutationAction']));
    
        $col_uniq   = new TDataGridColumn( 'uniqid', 'Uniqid', 'center', '10%');
        $col_id     = new TDataGridColumn( 'id', 'ID', 'center', '10%');
        $col_pid    = new TDataGridColumn( 'product_id', 'Produto', 'left', '20%');
        $col_nome    = new TDataGridColumn( 'product_id', 'Prd_id', 'left', '20%');
        $col_quantidade = new TDataGridColumn( 'quantidade', 'Qntd', 'left', '15%');
        $col_preco  = new TDataGridColumn( 'sale_price', 'Preco', 'center', '10%');
        $col_disc   = new TDataGridColumn( 'discount', 'Desconto', 'center', '20%');
        $col_subt   = new TDataGridColumn( '=({quantidade} * {sale_price}) - {discount} ', 'Subtotal', 'right', '20%');
        
        $this->product_list->addColumn( $col_uniq );
        $this->product_list->addColumn( $col_id );
        $this->product_list->addColumn( $col_pid );
        $this->product_list->addColumn( $col_nome );
        $this->product_list->addColumn( $col_quantidade );
        $this->product_list->addColumn( $col_preco );
        $this->product_list->addColumn( $col_disc );
        $this->product_list->addColumn( $col_subt );

        $col_pid->setTransformer(function($value) {
        return Product::findInTransaction('sale', $value)->nome;
        });

        $col_subt->enableTotal('sum', 'R$', 2, ',', '.');

        $col_id->setVisibility(false);
        $col_uniq->setVisibility(false);

        $action1 = new TDataGridAction([$this, 'onEditItemProduto'] );
        $action1->setFields( ['uniqid', '*'] );

        $action2 = new TDataGridAction([$this, 'onDeleteItem']);
        $action2->setField('uniqid');


        $this->product_list->addAction($action1, _t('Edit'), 'far:edit blue');
        $this->product_list->addAction($action2, _t('Delete'), 'far:trash-alt red');

        $this->product_list->createModel();

        $panel = new TPanelGroup;
        $panel->add($this->product_list);
        $panel->getBody()->style = 'overflow-x:auto';
        $this->form->addContent( [$panel] );

        $format_value = function($value) {
            if (is_numeric($value)) {
                return 'R$ '.number_format($value, 2, ',', '.');
            }
            return $value;
        };

        $col_preco->setTransformer( $format_value );
        $col_subt->setTransformer( $format_value );

        $this->form->addHeaderActionLink( _t('Close'),  new TAction([__CLASS__, 'onClose'], ['static'=>'1']), 'fa:times red');
        $this->form->addAction( 'Save',  new TAction([$this, 'onSave'], ['static'=>'1']), 'fa:save green');
        $this->form->addAction( 'Clear', new TAction([$this, 'onClear']), 'fa:eraser red');
        
        $container = new TVBox;
        $container->style = 'width: 100%';
        //$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        parent::add($container);
    }  

    public function onLoad($param)
    {
        $data = new stdClass;
        $data->cliente_id  = $param['cliente_id'];
        $this->form->setData($data);
    }

    public static function onProductChange( $params )
    {
        if( !empty($params['product_detail_product_id']) )
        {
            try
            {
                TTransaction::open('sale');
                $product   = new Product($params['product_detail_product_id']);
                TForm::sendData('form_Sale', (object) ['product_detail_preco' => $product->preco]);
                TTransaction::close();
            }
            catch (Exception $e)
            {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        }
    }
    
    function onClear($param)
    {
        $this->form->clear();
    }

    public function onProductAdd( $param )
    {
        try
        {
            $this->form->validate();
            $data = $this->form->getData();
            
            if( (! $data->product_detail_product_id) || (! $data->product_detail_quantidade) || (! $data->product_detail_preco) )
            {
                throw new Exception('The fields Product, Amount and Price are required');
            }

            $uniqid = !empty($data->product_detail_uniqid) ? $data->product_detail_uniqid : uniqid();

            $grid_data = ['uniqid'          => $uniqid,
                          'id'              => $data->product_detail_id,
                          'product_id'      => $data->product_detail_product_id,
                          'quantidade'      => $data->product_detail_quantidade,
                          'sale_price'      => $data->product_detail_preco,
                          'discount'        => $data->product_detail_discount];
            
            // insert row dynamically
            $row = $this->product_list->addItem( (object) $grid_data );
            $row->id = $uniqid;
            
            TDataGrid::replaceRowById('products_list', $uniqid, $row);
            
            // clear product form fields after add
            $data->product_detail_uniqid     = '';
            $data->product_detail_id         = '';
            $data->product_detail_product_id = '';
            $data->product_detail_quantidade = '';
            $data->product_detail_preco      = '';
            $data->product_detail_discount   = '';

            
            // send data, do not fire change/exit events
            TForm::sendData( 'form_Sale', $data, false, false );
        }
        catch (Exception $e)
        {
            $this->form->setData( $this->form->getData());
            new TMessage('error', $e->getMessage());
        }
    }
    
    public static function onEditItemProduto( $param )
    {
        $data = new stdClass;
        $data->product_detail_uniqid     = $param['uniqid'];
        $data->product_detail_id         = $param['id'];
        $data->product_detail_product_id = $param['product_id'];
        $data->product_detail_quantidade = $param['quantidade'];
        $data->product_detail_preco      = $param['sale_price'];
        $data->product_detail_discount   = $param['discount'];


        // send data, do not fire change/exit events
        TForm::sendData( 'form_Sale', $data, false, false );
    }

    public static function onDeleteItem( $param )
    {
        $data = new stdClass;
        $data->product_detail_uniqid     = '';
        $data->product_detail_id         = '';
        $data->product_detail_product_id = '';
        $data->product_detail_quantidade = '';
        $data->product_detail_preco      = '';
        $data->product_detail_discount   = '';
        
        // send data, do not fire change/exit events
        TForm::sendData( 'form_Sale', $data, false, false );
        
        // remove row
        TDataGrid::removeRowById('products_list', $param['uniqid']);
    }

    public function onEdit($param)
    {
        try
        {
            TTransaction::open('sale');
            
            if (isset($param['key']))
            {
                $key = $param['key'];
                
                $object = new Sale($key);
                $sale_items = SaleItem::where('sale_id', '=', $object->id)->load();
                
                foreach( $sale_items as $item )
                {
                    $item->uniqid = uniqid();
                    $row = $this->product_list->addItem( $item );
                    $row->id = $item->uniqid;
                }
                
                $this->form->setData($object);
                TTransaction::close();
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    public function onSave($param)
    {
        try
        {
            TTransaction::open('sale');
            
            $data = $this->form->getData();
            $this->form->validate();
            
            $sale = new Sale;
            $sale->fromArray((array) $data);
            
            /*
            echo "<pre>";
            print_r($param);
            echo "</pre>";
            */
            
            if (empty($sale->id))
            {
                $sale->status_id = SaleStatus::orderBy('id')->take(1)->first()->id;
            }
            $sale->store();

            SaleItem::where('sale_id', '=', $sale->id)->delete();
            
            $total = 0;
            if( !empty($param['products_list_product_id'] ))
            {
                foreach( $param['products_list_product_id'] as $key => $item_id )
                {
                    $item = new SaleItem;
                    $item->product_id  = $item_id;
                    $item->sale_price  = (float) $param['products_list_sale_price'][$key];
                    $item->quantidade  = (float) $param['products_list_quantidade'][$key];
                    $item->discount    = (float) $param['products_list_discount'][$key];
                    $item->total       = ( $item->sale_price * $item->quantidade) - $item->discount;
                    
                    $item->sale_id = $sale->id;
                    $item->store();
                    $total += $item->total;
                }
            }
            $sale->total = $total;
            $sale->store(); // stores the object
            
            TForm::sendData('form_Sale', (object) ['id' => $sale->id]);
            
            TTransaction::close(); // close the transaction
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback();
        }
    }
    
    public static function onMutationAction($param)
    {
        // Form data: $param['form_data']
        // List data: $param['list_data']
        //echo '<pre>';var_dump($param);
        $total = 0;
        
        if ($param['list_data'])
        {
            foreach ($param['list_data'] as $row)
            {
                $total += ( floatval($row['sale_price']) - floatval($row['discount'])) *  floatval($row['quantidade']);
            }
        }
        
        TToast::show('info', 'Novo total: <b>' . 'R$ '.number_format($total, 2, ',', '.') . '</b>', 'bottom right');
    }

    /**
     * Closes window
     */
    public static function onClose()
    {
        TScript::create("Template.closeRightPanel()");
    }
}
