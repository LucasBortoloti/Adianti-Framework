<?php

use Adianti\Control\TPage;
use Adianti\Database\TDatabase;
use Adianti\Database\TTransaction;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TRadioGroup;
use Adianti\Widget\Template\THtmlRenderer;

class SinistroListDois extends TPage
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

        $this->addFilterField('date', '>=', 'date_from', function ($value) {
            return TDate::convertToMask($value, 'dd/mm/yyyy', 'yyyy-mm-dd');
        });

        $this->addFilterField('date', '<=', 'date_to', function ($value) {
            return TDate::convertToMask($value, 'dd/mm/yyyy', 'yyyy-mm-dd');
        });

        $this->form = new BootstrapFormBuilder('form_search_Ocorrencias');
        $this->form->setFormTitle(('Ocorrencias 2'));

        // $id = new TEntry('id');
        $date_from = new TDate('date_from');
        $date_to = new TDate('date_to');

        $sinistro_id = new TDBUniqueSearch('sinistro_id', 'defciv', 'Sinistro', 'id', 'descricao');
        $sinistro_id->setMinLength(1);
        $sinistro_id->setMask('{descricao} ({id})');
        $output_type  = new TRadioGroup('output_type');


        $this->form->addFields([new TLabel('De')], [$date_from]);
        $this->form->addFields([new TLabel('Até')], [$date_to]);
        $this->form->addFields([new TLabel('Output')],   [$output_type]);

        //$this->form->addFields([new TLabel('Id')], [$id]);

        $date_from->setSize('50%');
        $date_to->setSize('50%');

        $output_type->setUseButton();
        $options = ['html' => 'HTML', 'pdf' => 'PDF', 'rtf' => 'RTF', 'xls' => 'XLS'];
        $output_type->addItems($options);
        $output_type->setValue('pdf');
        $output_type->setLayout('horizontal');

        $date_from->setSize('100%');
        $date_to->setSize('100%');
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

    public function onGenerate()
    {

        try {
            $data = $this->form->getData();
            $date_from = $data->date_from;
            $date_to = $data->date_to;

            $this->form->setData($data);

            $format = $data->output_type;

            $source = TTransaction::open('defciv');

            $query = "  SELECT      o.bairro_id as bairro_id,
                                    b.nome as bairro_nome,
                                    o.sinistro_id as sinistro_id,
                                    s.descricao as descricao,
                                    o.logradouro_id as logradouro_id,
                                    l.nome as logradouro_nome,
                                    s.descricao as sinistro_descricao,
                                    l.id as logradouro_id,
                                    l.nome as logradouro_nome,
                                    count(*) as QTDE,
                                    sum(OCO_DHDESALOJADOS) as DESALOJADOS,
                                    sum(OCO_DHDESABRIGADOS) as DESABRIGADOS
                                from defciv.ocorrencia o 
                                left join defciv.sinistro s on s.id = o.sinistro_id
                                left join vigepi.bairro b on b.id = o.bairro_id
                                left join vigepi.logradouro l on l.id = o.logradouro_id
                                    where o.data_cadastro >= '{$date_from}' and o.data_cadastro <= '{$date_to}'
                                group by o.bairro_id, b.nome, o.sinistro_id, s.descricao, o.logradouro_id, l.nome
                                order by b.nome, s.descricao, l.nome
                                limit 10";

            $rows = TDatabase::getData($source, $query, null, null);

            // echo "<pre>";
            // print_r($data);
            // echo "<pre>";

            if ($rows) {
                $widths = array(40, 320, 80, 80, 80, 80, 80, 80, 80);

                switch ($format) {
                    case 'html':
                        $table = new TTableWriterHTML($widths);
                        break;
                    case 'pdf':
                        $table = new TTableWriterPDF($widths);
                        break;
                    case 'rtf':
                        $table = new TTableWriterRTF($widths);
                        break;
                    case 'xls':
                        $table = new TTableWriterXLS($widths);
                        break;
                }

                if (!empty($table)) {
                    // create the document styles
                    $table->addStyle('header', 'Helvetica', '16', 'B', '#000000', '#ffffff');
                    $table->addStyle('title',  'Helvetica', '10', '', '#000000', '#ffffff');
                    $table->addStyle('italico', 'Helvetica', '10', 'I', '#ff0000', '#ffffff');
                    $table->addStyle('datap',  'Helvetica', '10', '',  '#000000', '#ffffff', 'LR');
                    $table->addStyle('datai',  'Helvetica', '10', '',  '#000000', '#ffffff', 'LR');
                    $table->addStyle('footer', 'Helvetica', '10', 'B',  '#000000', '#ffffff');

                    $date_from_formatado = date('d/m/Y', strtotime($date_from));
                    $date_to_formatado = date('d/m/Y', strtotime($date_to));

                    $table->setHeaderCallback(function ($table) use ($date_from_formatado, $date_to_formatado) {
                        $table->addRow();
                        $table->addCell('Prefeitura Municipal de Jaraguá do Sul', 'center', 'header', 9);
                        $table->addRow();
                        $table->addCell('prefeitura@jaraguadosul.com.br     83.102.459/0001-23    (047) 2106-8000', 'center', 'title', 9);
                        $table->addRow();
                        $table->addCell("Ocorrências de {$date_from_formatado} até {$date_to_formatado} por tipo de ação (DATA DO CADASTRO)", 'center', 'italico', 9);
                        $table->addRow();
                        $table->addCell('Id', 'center', 'title');
                        $table->addCell('Nome do bairro',   'left', 'title');
                        $table->addCell('Sinistro id', 'center', 'title');
                        $table->addCell('Descrição',    'center', 'title');
                        $table->addCell('Id Rua',   'center', 'title');
                        $table->addCell('Nome da rua',   'center', 'title');
                        $table->addCell('Qtde',   'center', 'title');
                        $table->addCell('Desalojados',   'center', 'title');
                        $table->addCell('Desabrigados',   'center', 'title');
                    });

                    $totalQtde = 0;
                    $totalDesalojados = 0;
                    $totalDesabrigados = 0;

                    // controls the background filling
                    $colour = FALSE;

                    echo "<pre>";
                    print_r($rows);
                    echo "<pre>";

                    foreach ($rows as $row) {

                        $totalQtde += $row['QTDE'];
                        $totalDesalojados += $row['DESALOJADOS'];
                        $totalDesabrigados += $row['DESABRIGADOS'];

                        $style = $colour ? 'datap' : 'datai';
                        $table->addRow();
                        $table->addCell($row['bairro_id'],  'center', $style);
                        $table->addCell($row['bairro_nome'],  'center', $style);
                        $table->addCell($row['sinistro_id'], 'left', $style);
                        $table->addCell($row['sinistro_descricao'],      'center',   $style);
                        $table->addCell($row['logradouro_id'],  'center', $style);
                        $table->addCell($row['logradouro_nome'],   'center',   $style);
                        $table->addCell($row['QTDE'],   'center',   $style);
                        $table->addCell($row['DESALOJADOS'],   'center',   $style);
                        $table->addCell($row['DESABRIGADOS'],   'center',   $style);

                        $colour = !$colour;
                    }

                    $table->setFooterCallback(function ($table) use ($totalQtde, $totalDesalojados, $totalDesabrigados) {
                        $table->addRow();
                        $table->addCell('Total', 'center', 'title', 6);
                        $table->addCell("{$totalQtde}", 'center', 'footer');
                        $table->addCell("{$totalDesalojados}", 'center', 'footer');
                        $table->addCell("{$totalDesabrigados}", 'center', 'footer');
                        $table->addRow();
                        $table->addCell(date('d/m/Y   h:i:s'), 'center', 'footer', 9);
                    });

                    $output = "app/output/tabular.{$format}";

                    // stores the file
                    if (!file_exists($output) or is_writable($output)) {
                        $table->save($output);
                        parent::openFile($output);
                    } else {
                        throw new Exception(_t('Permission denied') . ': ' . $output);
                    }

                    // shows the success message
                    new TMessage('info', "Report generated. Please, enable popups in the browser. <br> <a href='$output'>Click here for download</a>");
                }
            } else {
                new TMessage('error', 'No records found');
            }

            // close the transaction
            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }
}
