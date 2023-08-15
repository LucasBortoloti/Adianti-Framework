<?php

use Adianti\Control\TPage;

class FilmeCompleteDataGridView extends TPage{

    private $datagrid;
    private $loaded;
    private $pageNavigation;

    public function __construct(){

        parent:: __construct();

        $this->form = new TQuickForm('form_busca_filme');
        $this->form->class = 'tform';

        $busca_titulo = new TEntry('titulo');
        $busca_titulo->setValue( TSession::getValue('Filme_titulo'));

        $this->form->addQuickField('Título', $busca_titulo, 150);
        $this->form->addQuickAction('Buscar', new TAction(array($this, 'onSearch')), 'ico_find.png');

        $this->datagrid = new TDataGrid; 

        $id = new TDataGridColumn('id', 'ID','center', 40);
        $titulo = new TDataGridColumn('titulo', 'Titulo','left', 150);
        $duracao = new TDataGridColumn('duracao', 'Duracao','left', 50);
        $dt_lcto = new TDataGridColumn('dt_lcto', 'Dt. Lcto','left', 70); 
        $distribuidor = new TDataGridColumn('distribuidor->nome', 'Distribuidor','left', 150);

        $dt_lcto->setTransformer( array($this, 'formataData'));

        $this->datagrid->addColumn( $id );
        $this->datagrid->addColumn( $titulo );
        $this->datagrid->addColumn( $duracao );
        $this->datagrid->addColumn( $dt_lcto );
        $this->datagrid->addColumn( $distribuidor );

        $action1 = new TDataGridAction( array('FilmeCompletoFormView', 'onEdit'));
        $action1->setLabel('Editar');
        $action1->setImage('ico.edit.png');
        $action1->setField('id');

        $action2 = new TDataGridAction( array( $this, 'onDelete'));
        $action2->setLabel('Excluir');
        $action2->setImage('ico.delete.png');
        $action2->setField('id');

        $order1 = new TAction( array($this, 'onReload'));
        $order2 = new TAction( array($this, 'onReload'));

        $order1->setParameter('order', 'id');
        $order2->setParameter('order', 'titulo');

        $id->setAction($order1);
        $titulo->setAction($order2);

        $this->datagrid->addAction( $action1 );
        $this->datagrid->addAction( $action2 );
        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction( new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth( $this->datagrid->getWidth());

        $vbox = new TVBox;
        $vbox->add($this->form);
        $vbox->add($this->datagrid);
        $vbox->add($this->pageNavigation);

        parent::add($vbox);

    }

    public function onSearch(){

         $data = $this->form->getData();

         if($data->titulo){
            
            $filter = new TFilter('titulo', 'like', "%{$data->titulo}%");
            TSession::setValue('Filme_filter', $filter);
            TSession::setValue('Filme_titulo', $data->titulo); 

         }
         else{
            TSession::setValue('Filme_filter', NULL);
            TSession::setValue('Filme_titulo', ''); 

         }

         $this->form->setData($data);

         $param = array();
         $param['offset'] = 0;
         $param['firts_page'] = 1;
         $this->onReload( $param );


    }


    public function onReload( $param = NULL ){
        
        try{

            TTransaction::open('filme');
            
            $repository = new TRepository('Filme');

            $criteria = new TCriteria;
            $criteria->setProperties( $param );
            $criteria->setProperty('limit', 10);

            if(TSession::getValue('Filme_filter')){

                $criteria->add( TSession::getValue('Filme_filter'));

            }


            $objects = $repository->load( $criteria );
            $this->datagrid->clear();
            
            if($objects){
                
                foreach ($objects as $object){
                    $this->datagrid->addItem( $object );
                }
            }

            $criteria->resetProperties();
            $count = $repository->count($criteria);

            $this->pageNavigation->setCount( $count );
            $this->pageNavigation->setProperties( $param );
            $this->pageNavigation->setLimit( 10 );


            TTransaction::close();
            $this->loaded = TRUE;

        }
        catch(Exception $e){

            new TMessage('error', $e->getMessage());
            TTransaction::rollback();

        }
        
    }

    public function onDelete( $param ){

        $action = new TAction( array($this, 'delete'));
        $action->setParameters( $param );
        new TQuestion('Você deseja realmente excluir?', $action);


    }

    public function delete(){

            try{
                TTransaction::open('filme');

                $filme = new Filme;
                $filme->delete ($param['key']);

                new TMessage('info', 'Registro excluido com sucesso');
                TTransaction::close();

                $this->onReload( $param );


            }
            catch(Exception $e){
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
            }
         
    }


    public function formataData( $data, $objeto,$row ){
       
        $obj = new DateTime($data);
        return $obj->format('d/m/Y');



    }


    public function show(){
        
        if(!$this->loaded){
            
            $this->onReload( func_get_arg( 0 ) );
        }

        parent::show();
    }

}

?>