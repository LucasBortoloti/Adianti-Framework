<?php

use Adianti\Base\TStandardForm;

class FilmeStandardFormView extends TStandardForm{

    protected $form;

    public function __construct(){

        parent::__construct();

        parent::setDatabase('filme');
        parent::setActiveRecord('Filme');

        $this->form = new TQuickForm('form_filme');
        $this->form->style = 'width: 560px';
        $this->form->class = 'tform';
        $this->form->setFormTitle('Cadastro padrão filme');

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

        $this->form->addQuickField('ID', $id, 100);
        $this->form->addQuickField('Titulo', $titulo, 200);
        $this->form->addQuickField('Duracao', $duracao, 100);
        $this->form->addQuickField('Dt. lcto', $dt_lcto, 100);
        $this->form->addQuickField('Orcamento', $orcamento, 100);
        $this->form->addQuickField('Distribuidor', $distribuidor_id, 100);
        $this->form->addQuickField('Genero', $genero_id, 100);

        $this->form->addQuickAction('Salvar', new TAction(array($this, 'onSave')), 'ico_save.png');
        $this->form->addQuickAction('Novo', new TAction(array($this, 'onEdit')), 'ico_edit.png');

        parent::add($this->form);


    }


}


?>