<?php

use Adianti\Control\TPage;

class MultiStepPageView extends TPage{

    private $form;

    public function __construct(){

        parent:: __construct();

        $this->form = new TQuickForm('form1');
        $this->form->class = 'tform';
        $this->form->style = 'width: 615px';
        $this->form->setFormTitle('Formulário passo a passo - parte 1');

        $email = new TEntry('email');
        $password = new TPassword('password');
        $confirm = new TPassword('confirm');

        $this->form->addQuickField('Email', $email, 200);
        $this->form->addQuickField('Senha', $password, 200);
        $this->form->addQuickField('Confirmar', $confirm, 200);

        $email->addValidation('Email', new TRequiredValidator);
        $email->addValidation('Email', new TEmailValidator);
        $password->addValidation('Senha', new TRequiredValidator);
        $confirm->addValidation('Confirmar', new TRequiredValidator);


        $action = new TAction( array( $this, 'onNext'));
        $this->form->addQuickAction('Avançar', $action, 'ico_apply.png' );

        parent::add($this->form);

    }

    public function onNext(){
        
        try{
            $data = $this->form->getData();

            $this->form->Validate();
        
            $this-> form->setData( $data ); 
                
            if ($data->password !== $data->confirm){

                throw new Exception('Senha não confere');
            }

            TSession::setValue('form1', $data);
            TApplication::loadPage('MultiStepPage2View','onLoadForm1', (array) $data);

        }
        catch(Exception $e){
            
            new TMessage('error', $e->getMessage());

        }

        
    }

}
?>