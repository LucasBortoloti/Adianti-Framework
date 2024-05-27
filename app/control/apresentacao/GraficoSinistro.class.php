<?php

use Adianti\Control\TPage;
use Adianti\Widget\Dialog\TMessage;
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
        $this->form->addAction('Gerar em PDF', new TAction(array($this, 'onGeneratePDF')), 'fa:download blue');

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
               b.nome AS bairro_nome,
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

        $colunas = $sinistro->query($query)->fetchAll(PDO::FETCH_ASSOC);

        // var_dump($colunas);

        $dados[] = ['Sinistro', 'Quantidade'];

        foreach ($colunas as $coluna) {
            $dados[] = [$coluna['descricao'], (float)$coluna['QTDE']];
        }

        $div = new TElement('div');
        $div->id = 'container';
        $div->style = 'width:1555px;height:1150px';
        $div->add($html);

        $date_from_formatado = date('d/m/Y', strtotime($date_from));
        $date_to_formatado = date('d/m/Y', strtotime($date_to));

        $bairro_nome = !empty($colunas) ? $colunas[0]['bairro_nome'] : '';
        if (empty($bairro_id)) {
            $bairro_nome = 'Todos';
        }

        $html->enableSection('main', array(
            'data' => json_encode($dados),
            'width' => '100%',
            'height' => '1000px',
            'title'  => "Sinistros: {$date_from_formatado} até {$date_to_formatado}, Bairro: {$bairro_nome}"
        ));

        TTransaction::close();

        parent::add($div);
    }

    function onGeneratePDF()
    {
        try {
            $data = $this->form->getData();
            $date_from = $data->date_from;
            $date_to = $data->date_to;
            $bairro_id = $data->bairro_id;
            $pesquisa = $data->pesquisa;

            $this->form->setData($data);

            TTransaction::open('defciv');
            $sinistro = TTransaction::get();

            // Build the query
            $query = "SELECT
                    s.descricao,
                    b.nome AS bairro_nome,
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

            $stmt = $sinistro->query($query);
            $colunas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Prepare data for the chart
            $dados = [[]];
            foreach ($colunas as $coluna) {
                $dados[] = [$coluna['descricao'], (float)$coluna['QTDE']];
            }

            $date_from_formatado = date('d/m/Y', strtotime($date_from));
            $date_to_formatado = date('d/m/Y', strtotime($date_to));
            $bairro_nome = !empty($colunas) ? $colunas[0]['bairro_nome'] : '';

            if (empty($bairro_id)) {
                $bairro_nome = 'Todos';
            }

            $chartData = json_encode($dados);
            $chartUrl = "https://quickchart.io/chart?w=650&h=450&c=" . urlencode(json_encode([
                'type' => 'pie',
                'data' => [
                    'labels' => array_column($dados, 0),
                    'datasets' => [
                        [
                            'data' => array_column($dados, 1),
                            'borderColor' => 'black',
                            'borderWidth' => 2,
                        ],
                    ],
                ],
                'options' => [
                    'title' => [
                        'display' => true,
                        'text' => "Sinistros: {$date_from_formatado} até {$date_to_formatado}, Bairro: {$bairro_nome}",
                        'fontColor' => 'black',
                    ],
                    'legend' => [
                        'labels' => [
                            'fontColor' => 'black',
                        ],
                    ],
                    'plugins' => [
                        'datalabels' => [
                            'color' => 'black',
                        ],
                    ],
                ],
            ]));
            $base64Image = base64_encode(file_get_contents($chartUrl));
            $googleChartImageUrl = 'data:image/png;base64,' . $base64Image;

            $html_content = '
            <html>
            <head>
                <style>
                .cabecalho {
                    padding: 14px;
                    border: 1px solid #000000;
                    font-size: 16px;
                    font-family: Arial, Helvetica, sans-serif;
                }
                .header {
                    position: fixed;
                    top: 0cm;
                    width: 100%;
                    height: 2cm;
                    background-color: #ffffff;
                }
                
                body {
                    margin-top: 4cm;
                    margin-bottom: 1cm;
                }

                footer::after {
                    content: "Página " counter(page);
                }
                
                footer {
                    position: fixed;
                    bottom: 0cm;
                    left: 0cm;
                    right: 0cm;
                    text-align: right;
                    font-family: Sans-serif;
                    width: 100%;
                    border-top: 1px solid #000000;
                    margin-bottom: -10px;
                    padding: 6px;
                }
                </style>
            </head>
            <footer></footer>
            <body>
                <div class="header">
                    <table class="cabecalho" style="width:100%">
                <tr>
                    <td><b><i>PREFEITURA MUNICIPAL DE JARAGUÁ DO SUL</i></b></td>
                </tr>
                <tr>
                    <td> prefeitura@jaraguadosul.com.br</td>
                </tr>
                <tr>
                    <td>83.102.459/0001-23</td>
                </tr>
                <tr>
                    <td>(047) 2106-8000</td>
                </tr>
            </table>
            </div>
                <img class="img" src="' . $googleChartImageUrl . '" alt="Google Chart Image" style="width: 720px; height: 500px;">
            </body>
            </html>';

            TTransaction::close();

            // Create a Dompdf instance and load the HTML content
            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html_content);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Save the PDF file and display it in a window
            file_put_contents('app/output/document.pdf', $dompdf->output());

            $window = TWindow::create(('Document HTML->PDF'), 0.8, 0.8);
            $object = new TElement('object');
            $object->data  = 'app/output/document.pdf';
            $object->type  = 'application/pdf';
            $object->style = "width: 100%; height:calc(100% - 10px)";
            $object->add('O navegador não suporta a exibição deste conteúdo, <a style="color:#007bff;" target=_newwindow href="' . $object->data . '"> clique aqui para baixar</a>...');

            $window->add($object);
            $window->show();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }
}
