<?php

use Adianti\Control\TPage;
use Adianti\Database\TDatabase;
use Adianti\Database\TTransaction;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TRadioGroup;
use Adianti\Widget\Template\THtmlRenderer;
use BaconQrCode\Renderer\Image\TransformationMatrix;

class SinistroList7 extends TPage
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
        $this->form->setFormTitle(('Sinistros ordenados pelos solicitantes'));

        $date_from = new TDate('date_from');
        $date_to = new TDate('date_to');

        $sinistro_id = new TDBUniqueSearch('sinistro_id', 'defciv', 'Sinistro', 'id', 'descricao');
        $sinistro_id->setMinLength(1);
        $sinistro_id->setMask('{descricao} ({id})');

        $this->form->addFields([new TLabel('De')], [$date_from]);
        $this->form->addFields([new TLabel('Até')], [$date_to]);

        $date_from->setSize('50%');
        $date_to->setSize('50%');

        $date_from->setSize('100%');
        $date_to->setSize('100%');

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

            $source = TTransaction::open('defciv');

            $query = "    SELECT        o.bairro_id as bairro_id,
                                        b.nome as bairro_nome,
                                        o.sinistro_id as sinistro_id,
                                        s.descricao as descricao,
                                        s.descricao as sinistro_descricao,
                                        o.solicitante as solicitante,
                                        o.cpf as cpf,
                                        o.fone1 as fone,
                                        o.data_cadastro as data_cadastro,
                                        o.data_evento as data_evento,
                                        o.cobrade_id as cobrade,
                                        o.status as situacao,
                                        count(*) as QTDE,
                                        o.OCO_DHDESALOJADOS as DESALOJADOS,
                                        o.OCO_DHDESABRIGADOS as DESABRIGADOS
                                    from defciv.ocorrencia o
                                    left join defciv.sinistro s on s.id = o.sinistro_id
                                    left join vigepi.bairro b on b.id = o.bairro_id
                                        where o.data_evento >= '{$date_from}' and o.data_evento <= '{$date_to}'
                                    group by o.solicitante ,o.bairro_id, b.nome, o.sinistro_id, s.descricao
                                    order by o.solicitante, b.nome, s.descricao";

            $rows = TDatabase::getData($source, $query, null, null);

            $array_object['sinistro'] = '';

            $html = new THtmlRenderer('app/resources/sinistro4.html');

            $html->enableSection('main', $array_object);

            $replace = array();

            if ($rows) {

                $bairros = [];
                $sinistros = [];

                foreach ($rows as $row) {
                    if (!isset($sinistros[$row['sinistro_id']])) {
                        $sinistros[$row['sinistro_id']] = ["id" => $row['sinistro_id'], "sinistro_descricao" => $row['sinistro_descricao']];
                    }

                    if (!isset($bairros[$row['sinistro_id']][$row['bairro_id']])) {
                        $bairros[$row['sinistro_id']][$row['bairro_id']] = [
                            "solicitante" => $row['solicitante'],
                            "cpf" => $row['cpf'],
                            "fone" => $row['fone'],
                            "data_cadastro" => $row['data_cadastro'],
                            "data_evento" => $row['data_evento'],
                            "cobrade" => $row['cobrade'],
                            "status" => $row['situacao'],
                            "QTDE" => $row['QTDE'],
                        ];
                    }
                }

                $date_from_formatado = date('d/m/Y', strtotime($date_from));
                $date_to_formatado = date('d/m/Y', strtotime($date_to));
                $data = date('d/m/Y   h:i:s');

                $registrogeral = array();
                $content = ' <html>
                <head> <title>Ocorrencias</title>
                    <link href="app/resources/sinistro.css" rel="stylesheet" type="text/css" media="screen"/>
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
                                <td class="data_hora"><b>' . $data . '</b></td>
                            </tr>
                            <tr>
                                <td>(047) 2106-8000</td>
                                <td class="cor_ocorrencia colspan=4">Ocorrência de ' . $date_from_formatado . ' até ' . $date_to_formatado . '</td>                    
                            </tr>
                        </table>
                    </div>';

                foreach ($sinistros as $sinistro) {

                    $totalQtde = 0;

                    $content .= '<table class="customform" style="width:100%">';
                    $content .= '<tr><td class="sinistro" colspan=8>' . $sinistro["sinistro_descricao"] . '</td></tr>';
                    $content .= '<tr><td class="borda_horizontal"><b>Solicitante</b></td><td class="borda_horizontal"><b>CPF</b></td><td class="borda_horizontal"><b>Fone</b></td><td class="borda_horizontal"><b>Dt.Cad</b></td>'
                        . '<td class="borda_horizontal"><b>Dt.Evento</b></td><td class="borda_horizontal"><b>Cobrade</b></td><td class="status" colspan=2><b>Status</b></td></tr>';

                    if (isset($bairros[$sinistro["id"]])) {
                        foreach ($bairros[$sinistro["id"]] as $bairro) {

                            if (!empty($bairro['data_cadastro'])) {
                                $bairro['data_cadastro'] = date('d/m/Y', strtotime($bairro['data_cadastro']));
                            } else {
                                $bairro['data_cadastro'] = '';
                            }

                            if (!empty($bairro['data_evento'])) {
                                $bairro['data_evento'] = date('d/m/Y', strtotime($bairro['data_evento']));
                            } else {
                                $bairro['data_evento'] = '';
                            }


                            $content .= "<tr>
                                            <td class='solicitante' style='text-transform: uppercase'>{$bairro['solicitante']}</td>
                                            <td class='coluna_resultados'>{$bairro['cpf']}</td>
                                            <td class='coluna_resultados'>{$bairro['fone']}</td>
                                            <td class='coluna_resultados'>{$bairro['data_cadastro']}</td>
                                            <td class='coluna_resultados'>{$bairro['data_evento']}</td>
                                            <td class='coluna_resultados'>{$bairro['cobrade']}</td>
                                            <td class='centralizar' colspan=2>{$bairro['status']}</td>
                                         </tr>";
                            $totalQtde += $bairro['QTDE'];
                        }
                    }

                    $content .= "<tr>
                                    <td class='total' colspan=8>Total: $totalQtde</td>
                                </tr>
                                </table>
                                <br>";
                }

                $content .= "</body>
                    </html>";

                $html->enableSection('registros', $registrogeral, TRUE);
            }

            $vbox = new TVBox;
            $vbox->style = 'width: 100%';
            $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $vbox->add($this->html);
            parent::add($vbox);

            $contents = $content;

            $options = new \Dompdf\Options();
            $options->setChroot(getcwd());

            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($contents);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            file_put_contents('app/output/document.pdf', $dompdf->output());

            $window = TWindow::create(('Document HTML->PDF'), 0.8, 0.8);
            $object = new TElement('object');
            $object->data  = 'app/output/document.pdf';
            $object->type  = 'application/pdf';
            $object->style = "width: 100%; height:calc(100% - 10px)";
            $object->add('O navegador não suporta a exibição deste conteúdo, <a style="color:#007bff;" target=_newwindow href="' . $object->data . '"> clique aqui para baixar</a>...');

            $window->add($object);
            $window->show();

            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }
}
