<?php

use Adianti\Control\TPage;

class FormSelectionView extends TPage{

    private $form;

    public function __construct(){

        parent:: __construct();

        $this->form = new TQuickForm('form1');
        $this->form->class = 'tform';
        $this->form->style = 'width: 615px';
        $this->form->setFormTitle('Seletores');


        $radio = new TRadioGroup('radio');
        $check = new TCheckGroup('check');
        $combo = new TCombo('combo');
        $select = new TSelect('select');

        $this->form->addQuickField('Radio', $radio);
        $this->form->addQuickField('Check', $check);
        $this->form->addQuickField('Combo', $combo);
        $this->form->addQuickField('Select', $select);

        $radio->setLayout('horizontal');
        $check->setLayout('horizontal');
        $combo->setSize(300);
        $select->setSize(300, 150);

        $opcoes = array();
        $opcoes ['E'] = 'Esporte';
        $opcoes ['S'] = 'SUV';
        $opcoes ['U'] = 'Utilitário';
        $opcoes ['V'] = 'Van';

        $radio->addItems ($opcoes);
        $check->addItems ($opcoes);
        $combo->addItems ($opcoes);
        $select->addItems ($opcoes);

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