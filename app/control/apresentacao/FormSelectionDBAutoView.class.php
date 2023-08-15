<?php

use Adianti\Control\TPage;

class FormSelectionDBAutoView extends TPage{

    private $form;

    public function __construct(){

        parent:: __construct();

        $this->form = new TQuickForm('form1');
        $this->form->class = 'tform';
        $this->form->style = 'width: 615px';
        $this->form->setFormTitle('Seletores');


        $radio = new TDBRadioGroup('radio', 'filme', 'Genero', 'id', 'nome');
        $check = new TDBCheckGroup('check', 'filme', 'Genero', 'id', 'nome');
        $combo = new TDBCombo('combo', 'filme', 'Genero', 'id', 'nome');
        $select = new TDBSelect('select', 'filme', 'Genero', 'id', 'nome');

        $this->form->addQuickField('Radio', $radio);
        $this->form->addQuickField('Check', $check);
        $this->form->addQuickField('Combo', $combo);
        $this->form->addQuickField('Select', $select);

        $radio->setLayout('horizontal');
        $check->setLayout('horizontal');
        $combo->setSize(300);
        $select->setSize(300, 150);

        $radio->setValue('U');
        $check->setValue( array('S', 'V'));

        $action = new TAction( array( $this, 'onSave'));
        $this->form->addQuickAction('Salvar', $action, 'ico_save.png' );

        parent::add($this->form);

    }

    public function onSave(){
        
        try{
            $data = $this->form->getData();

            $this->form->Validate();
        
            $this-> form->setData( $data ); 
                
        
            $mensagem = 'Radio: ' . $data->radio . '<br>'. 
                        'Combo: ' . $data->combo . '<br>'.
                        'Check: ' . implode(',', $data-> check) . '<br>'.
                        'Select: ' . implode(',', $data-> select) . '<br>';
        
             new TMessage('info', $mensagem);
        }
        catch(Exception $e){
            
            new TMessage('error', $e->getMessage());

        }

        
    }


}
?>