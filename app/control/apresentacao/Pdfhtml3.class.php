<?php

use Adianti\Control\TPage;
use Adianti\Database\TTransaction;
use Adianti\Widget\Form\TEntry;

/**
 * @author Lucas Bortoloti <bortoloti91@gmail.com
 */
class Pdfhtml3 extends TPage
{
    protected $form;     // registration form

    use Adianti\Base\AdiantiStandardFormTrait; // Standard form methods

    function __construct()
    {

        parent::__construct();

        $this->setDatabase('sale');          // defines the database
        $this->setActiveRecord('Sale');         // defines the active record

        $this->form = new BootstrapFormBuilder('form_pdf_html3');
        $this->form->setFormTitle('Gerador PDF html teste 3 ');

        $sale_item = new TDBUniqueSearch('sale_item', 'sale', 'SaleItem', 'id', 'id');
        $sale_item->setMinLength(0);
        $sale_item->setSize('100%');

        /*
        $cliente_id = new TDBUniqueSearch('cliente_id', 'sale', 'Sale', 'id', 'nome');
        $cliente_id->setMinLength(0);
        $cliente_id->setSize('100%');
        */

        $this->form->addFields([new TLabel('Nota')], [$sale_item]);

        $this->form->addAction('Gerar', new TAction([$this, 'onGenerate'], ['id' => '{id}'], ['static' => 1]), 'fa:cogs');

        $object = new TElement('iframe');
        $object->width       = '75%';
        $object->height      = '655px';
        $object->src         = '//www.youtube.com/embed/T_HYY9jQnF4';
        $object->frameborder = '0';
        $object->allow       = 'accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture';

        $table = new TTable;
        $table->border = 0;
        $table->style = 'border-collapse:collapse';
        $table->width = '100%';
        $table->addRowSet($object);

        parent::add($this->form);

        parent::add($table);
    }

    public function onGenerate($param)
    {

        try {

            TTransaction::open('sale');

            $master_object = new SaleItem($param['sale_item']);

            $this->html = new THtmlRenderer('app/resources/teste3.html');

            $pdf = new stdClass;
            $pdf->name = 'Lucas';

            $array_object['name'] = $pdf->name;

            //A chave estrangeira cliente_id está na tabela sale e não na sale_item, terá que ver como puxar o cliente
            //$array_object['name'] = (Cliente::find($master_object->cliente_id)->nome);

            $this->html->enableSection('main', $array_object);

            $replace = array();

            $replace[] = array(
                'nome' => (Product::find($master_object->product_id)->nome),
                'quantidade' => $master_object->quantidade,
                'preco' => $master_object->sale_price
            );

            $this->html->enableSection('produtos', $replace, TRUE);
            // wrap the page content using vertical box
            $vbox = new TVBox;
            $vbox->style = 'width: 100%';
            $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $vbox->add($this->html);
            parent::add($vbox);

            $contents = $this->html->getContents();

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
