<?php

use Adianti\Control\TPage;
use Adianti\Database\TTransaction;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Template\THtmlRenderer;

class SinistroList extends TPage
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;

    use Adianti\Base\AdiantiStandardListTrait;

    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setDatabase('defciv');          // defines the database
        $this->setActiveRecord('Ocorrencia');         // defines the active record
        $this->setDefaultOrder('id', 'asc');    // defines the default order
        $this->addFilterField('id', '=', 'id'); // filterField, operator, formField
        $this->addFilterField('sinistro_id', '=', 'sinistro_id');

        $this->addFilterField('date', '>=', 'date_from', function ($value) {
            return TDate::convertToMask($value, 'dd/mm/yyyy', 'yyyy-mm-dd');
        });

        $this->addFilterField('date', '<=', 'date_to', function ($value) {
            return TDate::convertToMask($value, 'dd/mm/yyyy', 'yyyy-mm-dd');
        });

        $this->form = new BootstrapFormBuilder('form_search_Sale');
        $this->form->setFormTitle(('Ocorrencias'));

        $id = new TEntry('id');
        $date_from = new TDate('date_from');
        $date_to = new TDate('date_to');

        $sinistro_id   = new TDBUniqueSearch('sinistro_id', 'defciv', 'Sinistro', 'id', 'descricao');
        $sinistro_id->setMinLength(1);
        $sinistro_id->setMask('{descricao} ({id})');

        $this->form->addFields([new TLabel('Id')],     [$id]);
        $this->form->addFields([new TLabel('Data')], [$date_from]);
        $this->form->addFields([new TLabel('Data')], [$date_to]);

        $id->setSize('50%');
        $date_from->setSize('100%');
        $date_to->setSize('100%');
        $date_from->setMask('dd/mm/yyyy');
        $date_to->setMask('dd/mm/yyyy');

        $this->form->addAction('Gerar', new TAction([$this, 'onGenerate'], ['id' => '{sinistro_id}'], ['static' => 1]), 'fa:cogs');

        $table = new TTable;
        $table->border = 0;
        $table->style = 'border-collapse:collapse';
        $table->width = '100%';

        parent::add($this->form);

        parent::add($table);
    }

    public function onGenerate($param)
    {

        try {

            TTransaction::open('defciv');

            $this->html = new THtmlRenderer('app/resources/defciv.html');

            $sinistros = new Ocorrencia($param['id']);

            $array_object['descricao'] = $sinistros->sinistro->descricao;

            $this->html->enableSection('main', $array_object);
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }
}
