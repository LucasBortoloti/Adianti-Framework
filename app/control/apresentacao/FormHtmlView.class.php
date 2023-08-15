<?php

use Adianti\Control\TPage;

class FormHtmlView extends TPage{

    private $form;

    public function __construct(){

        parent:: __construct();

        $this->form = new TQuickForm('form1');
        $this->form->class = 'tform';
        $this->form->style = 'width: 700px';
        $this->form->setFormTitle('HTML editor');

        $html = new THtmlEditor('html_text');

        $this->form->addQuickField('HTML', $html, 100);
       
        $html->setSize(500,200);

        $action = new TAction( array( $this, 'onSave'));
        $this->form->addQuickAction('Salvar', $action, 'ico_save.png' );

        parent::add($this->form);

    }

    public function onSave(){
        
        try{
            $data = $this->form->getData();

            $this->form->Validate();
        
            $this-> form->setData( $data ); 
                
        
            $mensagem = $data->html_text;

             new TMessage('info', $mensagem);
        }
        catch(Exception $e){
            
            new TMessage('error', $e->getMessage());

        }

        
    }

}
?>