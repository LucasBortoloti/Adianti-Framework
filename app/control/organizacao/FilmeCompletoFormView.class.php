<?php

use Adianti\Control\TPage;

class FilmeCompletoFormView extends TPage{

    private $form;

    public function __construct(){

        parent:: __construct();

        $this->form = new TForm('form_filmes');
        $this->form->class = 'tform';
        $this->form->style = 'width: 600px';

        $table = new TTable;
        $table->width = '100%';
        $this->form->add($table);

        $id = new TEntry('id');
        $titulo = new TEntry('titulo');
        $duracao = new TEntry('duracao');
        $dt_lcto = new TDate('dt_lcto');
        $orcamento = new TEntry('orcamento');
        $distribuidor_id = new TDBCombo('distribuidor_id', 'filme', 'Distribuidor', 'id', 'nome', 'nome');
        $genero_id = new TDBCombo('genero_id', 'filme', 'Genero', 'id', 'nome', 'nome');

        $id->setSize(100);
        $id->setEditable(FALSE);

        $dt_lcto->setSize(80);
        $duracao->setSize(80);
        $genero_id->setSize(150);
        $distribuidor_id->setSize(150);
        $duracao->setMask('999');
        $orcamento->setNumericMask(2, ',', '.');

        $row = $table->addRow();
        $row->class = 'tformtitle';
        $cell = $row->addCell( new TLabel('Cadastro completo de filmes'));
        $cell->colspan= 2;

        $table->addRowSet( new TLabel('ID'), $id );
        $table->addRowSet( new TLabel('Titulo'), $titulo );
        $table->addRowSet( new TLabel('Duracao'), $duracao );
        $table->addRowSet( new TLabel('Dt Lanc.'), $dt_lcto );
        $table->addRowSet( new TLabel('Orcam.'), $orcamento );
        $table->addRowSet( new TLabel('Distribuidor'), $distribuidor_id);
        $table->addRowSet( new TLabel('Genero'), $genero_id );


        $save_button = new TButton('save');
        $save_button->setAction(new TAction(array($this, 'onSave')), 'Salvar');
        $save_button->setImage('ico_save.png');

        $new_button = new TButton('novo');
        $new_button->setAction( new TAction(array($this, 'onEdit')), 'Novo');
        $new_button->setImage('ico_new.png');

        $list_button = new TButton('list');
        $list_button->setAction( new TAction(array('FilmeCompleteDataGridView', 'onReload')), 'Lista');
        $list_button->setImage('ico_datagrid.png');

        $this->form->setFields( array($id, $titulo, $duracao, $dt_lcto, $orcamento, $distribuidor_id, $genero_id, $save_button, $new_button, $list_button));
        
        $table_buttons = new TTable;
        $row = $table_buttons->addRow();
        $row->addCell($save_button);
        $row->addCell($new_button);
        $row->addCell($list_button);


        $row = $table->addRow();
        $row->class = 'tformaction';
        $cell= $row->addCell( $table_buttons );
        $cell->colspan = 2;

        parent::add($this->form);

    }
    public function onSave( $param ){
        try{
            TTransaction::open('filme');

            $filme = $this->form->getData('Filme');
            
            $orcamento_original = $filme->orcamento;
            $orcamento = str_replace('.', '', $filme->orcamento);
            $orcamento = str_replace(',', '.', $orcamento);
            
            $filme->orcamento = $orcamento;
            $filme->store();

            $filme->orcamento = $orcamento_original;

            /*
            $filme = new Filme;
            $filme->fromArray( (array) $data);
            $filme->store();
            */

            $this->form->setData($filme);
            TTransaction::close();

        }
        catch (Exception $e){
            
            new TMessage('error', $e->getMessage());
            TTransaction::rollback(); 
        }
    }

    //Link para acessar o método: http://localhost/template/index.php?class=FilmeCompletoFormView&method=onEdit&key=8
    public function onEdit( $param ){

        try{

            if (isset($param['key'])){

                TTransaction::open('filme');

                $filme = new Filme($param['key']);

                $filme->orcamento = number_format( $filme->orcamento, 2, ',', '.');
                $this->form->setData($filme);

                TTransaction::close();
            }
            else{
                $this->form->clear();
            }

        }
        catch(Exception $e){
        
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();

        }
    }

}


?>