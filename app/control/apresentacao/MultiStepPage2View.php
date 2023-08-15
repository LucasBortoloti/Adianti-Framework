<?php

use Adianti\Control\TPage;

class MultiStepPage2View extends TPage{

    private $form;

    public function __construct(){

        parent:: __construct();

        $this->form = new TQuickForm('form1');
        $this->form->class = 'tform';
        $this->form->style = 'width: 615px';
        $this->form->setFormTitle('Formulário passo a passo - parte 2');

        $email = new TEntry('email');
        $nome = new TEntry('nome');
        $sobrenome = new TEntry('sobrenome');

        $email->setEditable(FALSE);

        $this->form->addQuickField('Email', $email, 200);
        $this->form->addQuickField('Nome', $nome, 200);
        $this->form->addQuickField('Sobrenome', $sobrenome, 200);

        $nome->addValidation('Nome', new TRequiredValidator);
        $sobrenome->addValidation('Sobrenome', new TRequiredValidator);


        $action = new TAction( array( $this, 'onConfirm'));
        $this->form->addQuickAction('Confirmar', $action, 'ico_apply.png' );

        parent::add($this->form);

    }

public function onLoadForm1( $data ){

    $obj = new StdClass;
    $obj->email = $data['email'];
    $this->form->setData( $obj );

}

public function onConfirm(){

    try{

        $data2 = $this->form->getData();
        
        $this->form->setData( $data2 );

        $this->form->validate();

        $data1 = TSession::getValue('form1');

        new TMessage('info', json_encode($data2));

        new TMessage('info', json_encode($data1));

    }
    catch (Exception $e){
        new TMessage('error', $e->getMessage());
    }

}

}