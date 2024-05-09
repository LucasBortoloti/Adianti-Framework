<?php

use Adianti\Control\TPage;
use Adianti\Database\TDatabase;
use Adianti\Database\TTransaction;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TRadioGroup;
use Adianti\Widget\Template\THtmlRenderer;

class SinistroList5 extends TPage
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
        $this->form->setFormTitle(('Ocorrencias 5'));

        // $id = new TEntry('id');
        $date_from = new TDate('date_from');
        $date_to = new TDate('date_to');

        $sinistro_id = new TDBUniqueSearch('sinistro_id', 'defciv', 'Sinistro', 'id', 'descricao');
        $sinistro_id->setMinLength(1);
        $sinistro_id->setMask('{descricao} ({id})');

        $this->form->addFields([new TLabel('De')], [$date_from]);
        $this->form->addFields([new TLabel('Até')], [$date_to]);

        //$this->form->addFields([new TLabel('Id')], [$id]);

        $date_from->setSize('50%');
        $date_to->setSize('50%');

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
                                    o.OCO_DHDESALOJADOS as DESALOJADOS,
                                    o.OCO_DHDESABRIGADOS as DESABRIGADOS
                                from defciv.ocorrencia o 
                                left join defciv.sinistro s on s.id = o.sinistro_id
                                left join vigepi.bairro b on b.id = o.bairro_id
                                left join vigepi.logradouro l on l.id = o.logradouro_id
                                    where o.data_evento >= '{$date_from}' and o.data_evento <= '{$date_to}'
                                group by o.bairro_id, b.nome, o.sinistro_id, s.descricao, o.logradouro_id, l.nome
                                order by b.nome, s.descricao, l.nome";

            $rows = TDatabase::getData($source, $query, null, null);

            $array_object['sinistro'] = '';

            $html = new THtmlRenderer('app/resources/sinistro4.html');

            $html->enableSection('main', $array_object);

            $replace = array();

            if ($rows) {

                $totalQtde = 0;
                $totalDesalojados = 0;
                $totalDesabrigados = 0;

                // echo "<pre>";
                // print_r($rows);
                // echo "<pre>";

                $bairros = array();
                $sinistros = array();

                $id = "";

                foreach ($rows as $row) {

                    if ($id != $row['bairro_id']) {
                        $id = $row['bairro_id'];
                        $bairros[] = ["id" => $row['bairro_id'], "bairro_nome" => $row['bairro_nome']];
                    }

                    $sinistros[] = [
                        "idpai" => $id,
                        "sinistro_id" => $row['sinistro_id'],
                        "sinistro_descricao" => $row['sinistro_descricao'],
                        "logradouro_id" => $row['logradouro_id'],
                        "logradouro_nome" => $row['logradouro_nome'],
                        "QTDE" => $row['QTDE'],
                        "DESALOJADOS" => $row['DESALOJADOS'],
                        "DESABRIGADOS" => $row['DESABRIGADOS'],

                    ];

                    // $replace[] = array(
                    //     'bairro_id' => $row['bairro_id'],
                    //     'bairro_nome' => $row['bairro_nome'],
                    //     'sinistro_id' => $row['sinistro_id'],
                    //     'sinistro_descricao' => $row['sinistro_descricao'],
                    //     'logradouro_id' => $row['logradouro_id'],
                    //     'logradouro_nome' => $row['logradouro_nome'],
                    //     'QTDE' => $row['QTDE'],
                    //     'DESALOJADOS' => $row['DESALOJADOS'],
                    //     'DESABRIGADOS' => $row['DESABRIGADOS'],
                    //     'totalQtde' => $totalQtde,
                    //     'totalDesalojados' => $totalDesalojados,
                    //     'totalDesabrigados' => $totalDesabrigados
                    // );
                }

                // print_r($bairros);

                $date_from_formatado = date('d/m/Y', strtotime($date_from));
                $date_to_formatado = date('d/m/Y', strtotime($date_to));
                $data = date('d/m/Y   h:i:s');


                $registrogeral = array();
                $content = ' <html>
                <head> <title>Ocorrencias</title>
                    <link href="app/resources/sinistro.css" rel="stylesheet" type="text/css" media="screen" />
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
                    <td class="hora"><b>' . $data . '</b></td>
                    </tr>
                    <tr>
                    <td>(047) 2106-8000</td>
                    <td class="red">Ocorrência de ' . $date_from_formatado . ' até ' . $date_to_formatado . '</td>                     
                    </tr>
                        </table>
                </div>
                    <table class="customform" style="width: 100%">';

                for ($i = 0; $i < count($bairros); $i++) {

                    $totalQtde = 0;
                    $totalDesalojados = 0;
                    $totalDesabrigados = 0;

                    $content .= "<tr>";

                    // echo $bairros[$i]["bairro_nome"] . "<br>";

                    $content .= "<td class='bairro' colspan='4'>" . $bairros[$i]["id"] . " - " . $bairros[$i]["bairro_nome"] . "</td> </tr>";
                    $r = "";

                    for ($j = 0; $j < count($sinistros); $j++) {

                        if ($sinistros[$j]["idpai"] == $bairros[$i]["id"]) {
                            $r .= "<tr> <td class='cor' colspan=4>" . $sinistros[$j]["sinistro_id"] . "  " . $sinistros[$j]["sinistro_descricao"] . "</td> </tr>" .
                                "<tr> <td class='desa'>Nome da rua</td>" . "<td class='desa'>Quantidade" . "</td>" . "<td class='desa'>Desabrigados </td>" .
                                "<td class='desa'>Desalojados</td>" . "</tr> <tr> <td>" . " " . $sinistros[$j]["logradouro_id"] . " - " . $sinistros[$j]["logradouro_nome"] .
                                "</td><td> " . $sinistros[$j]["QTDE"] . "</td> <td>" . $sinistros[$j]["DESABRIGADOS"] . "</td> <td>" . $sinistros[$j]["DESALOJADOS"] . "</td></tr>";

                            $totalQtde += $sinistros[$j]['QTDE'];
                            $totalDesalojados += $sinistros[$j]['DESALOJADOS'];
                            $totalDesabrigados += $sinistros[$j]['DESABRIGADOS'];
                        }
                    }
                    $content .= $r;

                    $content .= "<br><tr> <td class='total'>Total do bairro:</td>" . "<td class='total'> $totalQtde " . "</td>" . "<td class='total'> $totalDesalojados </td>" .
                        "<td class='total'> $totalDesabrigados </td>" . "</tr><br>";

                    $registrogeral[] = ["registro" => $content];
                    // $content = "";
                    // $registrogeral[] = [
                    //     "bairro_id" => $bairros[$i]["id"],
                    //     "bairro_nome" => $bairros[$i]["bairro_nome"],
                    //     "sini_array" => $registros
                    // ];
                    // unset($registros);
                }

                $content .= '</table> 
                </body>
                </html>
                ';

                // print_r($registrogeral);
                // echo $registrogeral[0]["registro"];

                $html->enableSection('registros', $registrogeral, TRUE);
            }

            $vbox = new TVBox;
            $vbox->style = 'width: 100%';
            $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $vbox->add($this->html);
            parent::add($vbox);

            // $contents = $html->getContents();
            $contents = $content;

            $options = new \Dompdf\Options();
            $options->setChroot(getcwd());

            // converts the HTML template into PDF
            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($contents);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // write and open file
            file_put_contents('app/output/document.pdf', $dompdf->output());

            // open window to show pdf
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
