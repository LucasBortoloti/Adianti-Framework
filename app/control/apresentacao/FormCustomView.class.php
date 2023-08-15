<?php

use Adianti\Control\TPage;

class FormCustomView extends TPage{

    private $form;

    public function __construct(){

        parent:: __construct();

        $this->form = new TForm('form1');
        //$this->form->style = 'width: 615px';

        $notebook = new TNotebook(500, 400);
        $notebook->appendPage('Dados', $this->form);

        $table = new TTable;
        $this->form->add($table);

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

        $table->addRowSet('Código', $id);
        $table->addRowSet('Nome', $nome);
        $table->addRowSet('Email', $email);
        $table->addRowSet('Data de Nascimento', $dt_nasc);
        $table->addRowSet('CEP', array($cep, 'Senha', $senha));
        $table->addRowSet('obs', $obs);
        $table->addRowSet('tratamento', $tratamento);
        $table->addRowSet('peso', $peso);
        $table->addRowSet('altura', $altura);

        $id->setEditable(FALSE);
        $nome->setMaxLength( 40 );
        $email->setSize(300);
        $dt_nasc->setMask('dd/mm/yyyy');
        $cep->setMask('99.999-999');
        $cep->setSize(100);
        $senha->setSize(100);
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

        $button = new TButton('action1');
        $button->setAction( new TAction( array($this, 'onSave') ), 'Salvar');
        $button->setImage('ico_save.png');

        $table->addRowSet( $button );

        $this->form->setFields(array($id, $nome, $email, $dt_nasc, $cep, $senha, $obs, $tratamento, $peso, $altura, $button));

        parent::add($notebook);

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