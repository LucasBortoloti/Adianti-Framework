<?php

use Adianti\Control\TPage;
use Adianti\Database\TDatabase;
use Adianti\Database\TTransaction;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TRadioGroup;
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

        $this->addFilterField('date', '>=', 'date_from', function ($value) {
            return TDate::convertToMask($value, 'dd/mm/yyyy', 'yyyy-mm-dd');
        });

        $this->addFilterField('date', '<=', 'date_to', function ($value) {
            return TDate::convertToMask($value, 'dd/mm/yyyy', 'yyyy-mm-dd');
        });

        $this->form = new BootstrapFormBuilder('form_search_Ocorrencias');
        $this->form->setFormTitle(('Ocorrencias'));

        // $id = new TEntry('id');
        $date_from = new TDate('date_from');
        $date_to = new TDate('date_to');

        $sinistro_id = new TDBUniqueSearch('sinistro_id', 'defciv', 'Sinistro', 'id', 'descricao');
        $sinistro_id->setMinLength(1);
        $sinistro_id->setMask('{descricao} ({id})');
        $pesquisa = new TRadioGroup('pesquisa');
        $output_type  = new TRadioGroup('output_type');


        $this->form->addFields([new TLabel('De')], [$date_from]);
        $this->form->addFields([new TLabel('Até')], [$date_to]);
        $this->form->addFields([new TLabel('Tipo de pesquisa')], [$pesquisa]);
        $this->form->addFields([new TLabel('Output')],   [$output_type]);

        //$this->form->addFields([new TLabel('Id')], [$id]);

        $date_from->setSize('50%');
        $date_to->setSize('50%');

        $pesquisa->setUseButton();
        $options = ['data_cadastro' => 'Data do Cadastro', 'data_evento' => 'Data do Evento', 'created_at' => 'Data de Criação'];
        $pesquisa->addItems($options);
        $pesquisa->setLayout('horizontal');

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

            $pesquisa = $data->pesquisa;

            $this->form->setData($data);

            $format = $data->output_type;

            $source = TTransaction::open('defciv');

            $query = "  SELECT      o.sinistro_id,
                                    s.descricao, 
                                    count(*) as QTDE,
                                    sum(
                                        case status
                                            when 'B' then 1
                                            when 'A' then 0
                                        end
                                    ) as BAIXADAS,
                                    sum(
                                        case status
                                            when 'B' then 0
                                            when 'A' then 1
                                        end
                                    ) as ABERTAS
                        from        ocorrencia o
                        left join   sinistro s on s.id = o.sinistro_id
                        where o.{$pesquisa} >= '{$date_from}' and o.{$pesquisa} <= '{$date_to}'
                        group by    o.sinistro_id,
                                    s.descricao
                        order by    s.descricao";

            $rows = TDatabase::getData($source, $query, null, null);

            // echo "<pre>";
            // print_r($data);
            // echo "<pre>";

            if ($rows) {
                $widths = array(30, 320, 80, 80, 80);

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

                    $table->setHeaderCallback(function ($table) {
                        $table->addRow();
                        $table->addCell('Prefeitura Municipal de Jaraguá do Sul', 'center', 'header', 5);
                        $table->addRow();
                        $table->addCell('prefeitura@jaraguadosul.com.br     83.102.459/0001-23    (047) 2106-8000', 'center', 'title', 5);
                        $table->addRow();
                        $table->addCell('Id',        'center', 'title');
                        $table->addCell('Descrição',   'left', 'title');
                        $table->addCell('Quantidade', 'center', 'title');
                        $table->addCell('Baixadas',    'center', 'title');
                        $table->addCell('Abertas',   'center', 'title');
                    });

                    $date_from_formatado = date('d/m/Y', strtotime($date_from));
                    $date_to_formatado = date('d/m/Y', strtotime($date_to));

                    $table->setFooterCallback(function ($table) use ($date_from_formatado, $date_to_formatado) {
                        $table->addRow();
                        $table->addCell("Ocorrências de {$date_from_formatado} até {$date_to_formatado} por tipo de ação (TODAS)", 'center', 'italico', 5);
                        $table->addRow();
                        $table->addCell(date('d/m/Y   h:i:s'), 'center', 'footer', 5);
                    });

                    // controls the background filling
                    $colour = FALSE;
                    foreach ($rows as $row) {
                        $style = $colour ? 'datap' : 'datai';
                        $table->addRow();
                        $table->addCell($row['sinistro_id'],  'center', $style);
                        $table->addCell($row['descricao'], 'left', $style);
                        $table->addCell($row['QTDE'],      'center',   $style);
                        $table->addCell($row['BAIXADAS'],  'center', $style);
                        $table->addCell($row['ABERTAS'],   'center',   $style);

                        $colour = !$colour;
                    }

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
