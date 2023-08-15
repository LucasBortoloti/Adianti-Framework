<?php

use Adianti\Control\TPage;

class FormMultiFieldView extends TPage{

    private $form;

    public function __construct(){

        parent:: __construct();

        $this->form = new TQuickForm('form1');
        $this->form->class = 'tform';
        $this->form->style = 'width: 615px';
        $this->form->setFormTitle('Multifield');

        $multifield = new TMultiField('contatos');

        $nome = new TEntry('nome');
        $fone = new TEntry('fone');

        $multifield->addField('nome', 'Nome', $nome, 100);
        $multifield->addField('fone', 'Fone', $fone, 100);

        $this->form->addQuickField('Contatos', $multifield, 100);

        $action = new TAction( array( $this, 'onSave'));
        $this->form->addQuickAction('Salvar', $action, 'ico_save.png' );

        parent::add($this->form);

    }

    public function onSave(){
        
        try{
            $data = $this->form->getData();

            $this->form->Validate();
        
            $this-> form->setData( $data ); 
                
        
            $mensagem = '';
        
             new TMessage('info', $mensagem);
        }
        catch(Exception $e){
            
            new TMessage('error', $e->getMessage());

        }

        
    }


}
?>