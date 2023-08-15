<?php

use Adianti\Base\TStandardList;

class FilmeStandardDataGridView extends TStandardList{

    public function __construct(){

        parent:: __construct();

        parent::setDatabase('filme');
        parent::setActiveRecord('Filme');
        parent::setFilterField('titulo');
        parent::setDefaultOrder('id', 'asc');

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

        $this->datagrid->addColumn( $id );
        $this->datagrid->addColumn( $titulo );
        $this->datagrid->addColumn( $duracao );
        $this->datagrid->addColumn( $dt_lcto );
        $this->datagrid->addColumn( $distribuidor );

        $action1 = new TDataGridAction( array('FilmeStandardFormView', 'onEdit'));
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

   

}

?>