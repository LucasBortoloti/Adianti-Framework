<?php

use Adianti\Control\TPage;
use Adianti\Database\TDatabase;
use Adianti\Database\TTransaction;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TRadioGroup;
use Adianti\Widget\Template\THtmlRenderer;

class SinistroListTres extends TPage
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
        $this->form->setFormTitle(('Ocorrencias 3'));

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

            $html = new THtmlRenderer('app/resources/sinistro3.html');

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
                                    where o.data_evento >= '{$date_from}' and o.data_evento <= '{$date_to}'
                                group by o.bairro_id, b.nome, o.sinistro_id, s.descricao, o.logradouro_id, l.nome
                                order by b.nome, s.descricao, l.nome";

            $rows = TDatabase::getData($source, $query, null, null);

            $array_object['sinistro'] = '';

            $html->enableSection('main', $array_object);

            $replace = array();

            if ($rows) {

                $totalQtde = 0;
                $totalDesalojados = 0;
                $totalDesabrigados = 0;

                echo "<pre>";
                print_r($rows);
                echo "<pre>";

                foreach ($rows as $row) {

                    $totalQtde += $row['QTDE'];
                    $totalDesalojados += $row['DESALOJADOS'];
                    $totalDesabrigados += $row['DESABRIGADOS'];

                    $replace[] = array(
                        'bairro_id' => $row['bairro_id'],
                        'bairro_nome' => $row['bairro_nome'],
                        'sinistro_id' => $row['sinistro_id'],
                        'sinistro_descricao' => $row['sinistro_descricao'],
                        'logradouro_id' => $row['logradouro_id'],
                        'logradouro_nome' => $row['logradouro_nome'],
                        'QTDE' => $row['QTDE'],
                        'DESALOJADOS' => $row['DESALOJADOS'],
                        'DESABRIGADOS' => $row['DESABRIGADOS'],
                        'totalQtde' => $totalQtde,
                        'totalDesalojados' => $totalDesalojados,
                        'totalDesabrigados' => $totalDesabrigados
                    );
                }

                $html->enableSection('registros', $replace, TRUE);
            }

            $vbox = new TVBox;
            $vbox->style = 'width: 100%';
            $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $vbox->add($this->html);
            parent::add($vbox);

            $contents = $html->getContents();

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
