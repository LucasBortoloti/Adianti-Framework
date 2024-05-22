<?php

use Adianti\Control\TPage;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Widget\Wrapper\TDBUniqueSearch;

class GraficoSinistro extends TPage
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;

    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();

        // $this->setDatabase('defciv');          // defines the database
        // $this->setActiveRecord('Ocorrencia');         // defines the active record
        $this->setDefaultOrder('id', 'asc');    // defines the default order
        $this->addFilterField('id', '=', 'id'); // filterField, operator, formField

        $this->addFilterField('date', '>=', 'date_from', function ($value) {
            return TDate::convertToMask($value, 'dd/mm/yyyy', 'yyyy-mm-dd');
        });

        $this->addFilterField('date', '<=', 'date_to', function ($value) {
            return TDate::convertToMask($value, 'dd/mm/yyyy', 'yyyy-mm-dd');
        });

        $this->form = new BootstrapFormBuilder('form_search_Ocorrencias');
        $this->form->setFormTitle(('Gráfico de Sinistros'));

        // $id = new TEntry('id');
        $bairro_id = new TDBCombo('bairro_id', 'vigepi', 'Bairro', 'id', 'nome');
        $bairro_id->enableSearch();

        $date_from = new TDate('date_from');
        $date_to = new TDate('date_to');
        $pesquisa = new TRadioGroup('pesquisa');

        $this->form->addFields([new TLabel('Bairro')], [$bairro_id]);
        $this->form->addFields([new TLabel('De')], [$date_from]);
        $this->form->addFields([new TLabel('Até')], [$date_to]);
        $this->form->addFields([new TLabel('Tipo de pesquisa')], [$pesquisa]);

        //$this->form->addFields([new TLabel('Id')], [$id]);

        $date_from->setSize('100%');
        $date_to->setSize('100%');

        $pesquisa->setUseButton();
        $options = ['data_cadastro' => 'Data do Cadastro', 'data_evento' => 'Data do Evento', 'created_at' => 'Data de Criação'];
        $pesquisa->addItems($options);
        $pesquisa->setLayout('horizontal');

        // $date_from->setMask('dd/mm/yyyy');
        // $date_to->setMask('dd/mm/yyyy');

        $this->form->addAction('Gerar', new TAction(array($this, 'onGenerate')), 'fa:download blue');

        $table = new TTable;
        $table->border = 0;
        $table->style = 'border-collapse:collapse';
        $table->width = '100%';

        parent::add($this->form);

        parent::add($table);
    }

    function onGenerate()
    {
        $html = new THtmlRenderer('app/resources/google_pie_chart.html');

        $data = $this->form->getData();
        $date_from = $data->date_from;
        $date_to = $data->date_to;
        $bairro_id = $data->bairro_id;
        $pesquisa = $data->pesquisa;

        $this->form->setData($data);

        TTransaction::open('defciv');

        $sinistro = TTransaction::get();

        $query = "SELECT
                s.descricao,
                count(*) as QTDE
              FROM
                ocorrencia o
              LEFT JOIN
                sinistro s ON s.id = o.sinistro_id
              LEFT JOIN vigepi.bairro b on b.id = o.bairro_id
              WHERE
                o.{$pesquisa} >= '{$date_from}' AND
                o.{$pesquisa} <= '{$date_to}'";

        if (!empty($bairro_id)) {
            $query .= " AND o.bairro_id = '{$bairro_id}'";
        }

        $query .= " GROUP BY s.descricao ORDER BY s.descricao;";

        $colunas = $sinistro->query($query);

        // var_dump($colunas);

        $dados[] = ['Sinistro', 'Quantidade'];

        foreach ($colunas as $coluna) {
            $dados[] = [$coluna['descricao'], (float)$coluna['QTDE']];
        }

        $div = new TElement('div');
        $div->id = 'container';
        $div->style = 'width:1500px;height:1150px';
        $div->add($html);

        $html->enableSection('main', array(
            'data' => json_encode($dados),
            'width' => '100%',
            'height' => '1000px',
            'title'  => 'Sinistros'
        ));

        TTransaction::close();
        parent::add($div);
    }
}
