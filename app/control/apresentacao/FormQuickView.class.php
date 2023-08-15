<?php

use Adianti\Control\TPage;

class FormQuickView extends TPage{

    private $form;

    public function __construct(){

        parent:: __construct();

        $this->form = new TQuickForm('form1');
        $this->form->class = 'tform';
        $this->form->style = 'width: 615px';
        $this->form->setFormTitle('Formulário rápido');

        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $email = new TEntry('email');
        $dt_nasc = new TDate('dt_nasc');
        $cep = new TEntry('cep');
        $senha = new TPassword('senha');
        $obs = new TText('obs');
        $tratamento = new TEntry('tratamento');
        $peso = new TSpinner('peso');
        $altura = new TSlider('altura');

        $this->form->addQuickField('Código', $id, 100);
        $this->form->addQuickField('Nome', $nome, 200);
        $this->form->addQuickField('Email', $email, 200);
        $this->form->addQuickField('Data de Nascimento', $dt_nasc, 70);
        $this->form->addQuickField('CEP', $cep, 70);
        $this->form->addQuickField('Senha', $senha, 70);
        $this->form->addQuickField('obs', $obs, 120);
        $this->form->addQuickField('tratamento', $tratamento, 70);
        $this->form->addQuickField('peso', $peso, 70);
        $this->form->addQuickField('altura', $altura, 70);

        $id->setEditable(FALSE);
        $nome->setMaxLength( 40 );
        $email->setSize(300);
        $dt_nasc->setMask('dd/mm/yyyy');
        $cep->setMask('99.999-999');
        $cep->setTip('Aqui você preenche o CEP');
        $obs->setSize(250, 100);
        $tratamento->setCompletion( array('Senhor', 'Senhora', 'Doutor'));
        $peso->setRange(0, 200, 0.1);
        $altura->setRange(0, 3, 0.1);
        $peso->setValue(70);
        $tratamento->setValue('Senhor');
        $altura->setValue(1.8);

        $nome->addValidation('Nome', new TRequiredValidator);
        $nome->addValidation('Nome', new TMinLengthValidator, array(3));
        $nome->addValidation('Nome', new TMaxLengthValidator, array(40));
        $email->addValidation('Email', new TEmailValidator);

        $action = new TAction( array( $this, 'onSave'));
        $this->form->addQuickAction('Salvar', $action, 'ico_save.png' );

        parent::add($this->form);

    }

    public function onSave(){
        
        try{
            $data = $this->form->getData();

            $this->form->Validate();
        
            $this-> form->setData( $data ); 
                
        
            $mensagem = 'Código: ' . $data->id . '<br>' . 
                        'Nome: ' . $data->nome . '<br>' .
                        'Email: ' . $data->email . '<br>' .
                        'Nascimento: ' . $data->dt_nasc . '<br>'. 
                        'CEP: ' . $data->cep . '<br>'.
                        'Senha: ' . $data->senha . '<br>'.
                        'OBS: ' . $data->obs . '<br>' .
                        'Tratamento: ' . $data->tratamento . '<br>' .
                        'Peso: ' . $data->peso . '<br>' .
                        'Altura: ' . $data->altura . '<br>' ;
        
        
             new TMessage('info', $mensagem);
        }
        catch(Exception $e){
            
            new TMessage('error', $e->getMessage());

        }

        
    }


}
?>