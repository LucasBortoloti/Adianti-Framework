<?php

use Adianti\Control\TPage;

class DataGridView extends TPage{

    private $datagrid;
    private $form;
    private $fields;

    public function __construct(){

        parent:: __construct();

        $this->form = new TForm;

        $this->datagrid =  new TDataGrid;
        $this->datagrid->disableDefaultClick();
        
        $this->form->add( $this->datagrid );

        //modifica o CSS da datagrid
        //$this->datagrid->class = 'customized_table';
        
        //carregar um CSS
        //parent::include_css('app/lib/include/custom-table.css');

        //barra de rolagem
        //$this->datagrid->setHeight(300);
        //$this->datagrid->makeScrollable();

        $id = new TDataGridColumn('id', 'Código', 'left', 100);
        $titulo = new TDataGridColumn('titulo', 'Título', 'left', 200);
        $duracao = new TDataGridColumn('duracao', 'Duração', 'left', 70);
        $dt_lcto = new TDataGridColumn('dt_lcto', 'Lanc.', 'left', 70);
        $orcamento = new TDataGridColumn('orcamento', 'Orçam.', 'right', 100);

        $this->datagrid->addColumn($id);
        $this->datagrid->addColumn($titulo);
        $this->datagrid->addColumn($duracao);
        $this->datagrid->addColumn($dt_lcto);
        $this->datagrid->addColumn($orcamento);

        $action = new TDataGridAction( array($this, 'onView'));
        $action->setLabel('Visualizar');
        //$action->setImage('ico_find.png');
        $action->setField('id');

        $this->datagrid->addAction($action);

        $action_col = new TAction( array($this, 'onColumnAction'));
        $action_col->setParameter('column', 'id');
        $id->setAction($action_col);

        $action_col2 = new TAction( array($this, 'onColumnAction'));
        $action_col2->setParameter('column', 'titulo');
        $titulo->setAction($action_col2);

        $orcamento->setTransformer( array($this, 'formataOrcamento'));
        $dt_lcto->setTransformer( array($this, 'formataData'));

        $this->datagrid->createModel();

        $button = new TButton('action1');
        $button->setAction( new TAction(array($this, 'onSave')), 'Salvar');
        $button->setImage('ico_save.png');

        $this->fields[] = $button;
        $this->form->setFields( $this->fields );

        $vbox = new TVBox;
        $vbox->add($this->form);
        $vbox->add($button);


        parent::add($vbox);

    }

    public function onView( $param ){

        $key  = $param['key'];
        new TMessage('info', 'Você clicou no registro: ' . $key);

    }

    public function onColumnAction( $param ){
        $column = $param['column'];
        new TMessage('info', 'Você clicou na coluna ' . $column);
    }

    public function formataOrcamento($valor, $objeto, $row){

        if($valor > 20000000){
            $valor = number_format($valor, 2, ',', '.');
            $row->style = 'background: #FFF9A7';
            return "<span style='color:red'>$valor </span>";
        }

        $valor = number_format($valor,2, ',', '.');
        return $valor;

    }

    public function formataData($data, $objeto, $row){
        
       $obj = new DateTime($data);
       return $obj->format('d/m/y');
    }

    public function onSave(){

        $data = $this->form->getData();

        $this->form->setData ( $data );

        $fields = $this->form->getFields();

        foreach($fields as $name => $field){
            
            if( $field instanceof TEntry){

                print "$name " . $field->getValue() . '<br>';

            }
        }
    }

    public function onReload( $param ){
        
        $this->datagrid->clear();

        $item = new StdClass;
        $item->id = 1;
        $item->titulo = 'The Godfather';
        $item->duracao = new TEntry('duracao1');
        $item->duracao->setValue(175);
        $item->dt_lcto = '1972-03-15';
        $item->orcamento = 6500000;
        $this->datagrid->addItem( $item );
        $this->fields[] = $item->duracao;
       

        $item = new StdClass;
        $item->id = 2;
        $item->titulo = 'Scent of a woman';
        $item->duracao = new TEntry('duracao2');
        $item->duracao->setValue(157);
        $item->dt_lcto = '1992-12-23';
        $item->orcamento = 21000000;
        $this->datagrid->addItem( $item );
        $this->fields[] = $item->duracao;


        $item = new StdClass;
        $item->id = 3;
        $item->titulo = 'Awakenings';
        $item->duracao = new TEntry('duracao3');
        $item->duracao->setValue(121);
        $item->dt_lcto = '1990-12-21';
        $item->orcamento = 21000000;
        $this->datagrid->addItem( $item );
        $this->fields[] = $item->duracao;


        $this->form->setFields( $this->fields );
        
        //varios registros para a barra de rolagem
        /*for($n=1; $n<=40; $n++){

            $item = new StdClass;
            $item->id = $n;
            $item->titulo = 'Titulo ' . $n;
            $item->duracao = 100;
            $item->dt_lcto = date('Y-m-d');
            $item->orcamento = 1000000000;

            $this->datagrid->addItem($item);

        } 
        */
    }

    function show(){

        $this->onReload( func_get_arg( 0 ));

        parent::show();
    }


    }


?>