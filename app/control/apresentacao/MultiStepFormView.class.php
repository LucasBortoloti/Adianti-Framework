<?php

use Adianti\Control\TPage;

class MultiStepFormView extends TPage{

    private $notebook;
    private $form;

    public function __construct(){

        parent:: __construct();

        $this->notebook = new TNotebook (400, 100);
        $this->notebook->setTabsVisibility( FALSE );

        $this->form = new TForm;
        $this->form->add($this->notebook);

        $page1 = new TTable;
        $page2 = new TTable;

        $this->notebook->appendPage('Page 1', $page1);
        $this->notebook->appendPage('Page 2', $page2);

        $field1 = new TEntry('field1');
        $field2 = new TEntry('field2');

        $field3 = new TEntry('field3');
        $field4 = new TEntry('field4');

        $page1->addRowSet( new TLabel('Field1'), $field1);
        $page1->addRowSet( new TLabel('Field2'), $field2);

        $page2->addRowSet( new TLabel('Field3'), $field3);
        $page2->addRowSet( new TLabel('Field4'), $field4);


        $button1 = new TButton('action1');
        $button1->setAction( new TAction(array($this,'onStep2')), 'Avançar');

        $button2 = new TButton('action2');
        $button2->setAction( new TAction(array($this,'onStep1')), 'Voltar');

        $button3 = new TButton('action3');
        $button3->setAction( new TAction(array($this,'onSave')), 'Salvar');

        $page1->addRowSet( $button1 );
        $page2->addRowSet( $button2, $button3 );

        $this->form->setFields (array($field1, $field2, $field3, $field4, $button1, $button2, $button3));

        parent::add($this->form);
    }

    public function onStep2(){
        
        $data = $this->form->getData();

        $data->field3 = 'Oi ' . $data->field1;
        $this->form->setData($data);

        $this->notebook->setCurrentPage( 1 );
    }

    public function onStep1(){

        $this->notebook->setCurrentPage( 0 );
        $this->form->setData($this->form->getData() );

    }

    public function onSave(){

        $this->notebook->setCurrentPage( 1 );
        $data = $this->form->getData();
        $this->form->setData( $data );

        new TMessage('info', json_encode($data));

    }

}


?>