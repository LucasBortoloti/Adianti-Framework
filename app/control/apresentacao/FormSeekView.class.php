<?php

use Adianti\Control\TPage;

class FormSeekView extends TPage{

    private $form;

    public function __construct(){

        parent:: __construct();

        $this->form = new TQuickForm('form1');
        $this->form->class = 'tform';
        $this->form->style = 'width: 615px';
        $this->form->setFormTitle('Seletores');

        $genero_id = new TDBSeekButton('genero_id', 'filme', 'form1', 'Genero', 'nome', 'genero_id', 'genero_nome');
        $genero_nome = new TEntry('genero_nome');
        $genero_nome->setEditable(FALSE);

        $this->form->addQuickField('Codigo do gênero', $genero_id, 100);
        $this->form->addQuickField('Nome do gênero', $genero_nome, 200);

        $action = new TAction( array( $this, 'onSave'));
        $this->form->addQuickAction('Salvar', $action, 'ico_save.png' );

        parent::add($this->form);

    }

    public function onSave(){
        
        try{
            $data = $this->form->getData();

            $this->form->Validate();
        
            $this-> form->setData( $data ); 
                
        
            $mensagem = 'Gênero ID: ' . $data->genero_id . '<br>'.
                        'Nome: ' . $data->genero_nome;
             new TMessage('info', $mensagem);
        }
        catch(Exception $e){
            
            new TMessage('error', $e->getMessage());

        }

        
    }

}
?>