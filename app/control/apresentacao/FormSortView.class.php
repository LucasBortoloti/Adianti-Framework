<?php

use Adianti\Control\TPage;

class FormSortView extends TPage{

    private $form;

    public function __construct(){

        parent:: __construct();

        $this->form = new TQuickForm('form1');
        $this->form->class = 'tform';
        $this->form->style = 'width: 700px';
        $this->form->setFormTitle('Sort List');

        $list1 = new TSortList('list1');
        $list2 = new TSortList('list2');

        $list1->connectTo( $list2 );
        $list2->connectTo( $list1 );

        $list1->addItems( array('A' => 'Arroz', 'F' => 'Fejão', 'B' => 'Bife', 'M' => 'Maionese', 'L' => 'Alface'));

        $this->form->addQuickField('List 1', $list1, 100);
        $this->form->addQuickField('List 2', $list2, 100);

        $action = new TAction( array( $this, 'onSave'));
        $this->form->addQuickAction('Salvar', $action, 'ico_save.png' );

        parent::add($this->form);

    }

    public function onSave(){
        
        try{
            $data = $this->form->getData();

            $this->form->Validate();
        
            $this-> form->setData( $data ); 
                
        
            $mensagem = 'Lista 1: ' . implode(',', $data->list1) . '<br>'. 'Lista 2: ' . implode(',', $data->list2);

             new TMessage('info', $mensagem);
        }
        catch(Exception $e){
            
            new TMessage('error', $e->getMessage());

        }

        
    }

}
?>