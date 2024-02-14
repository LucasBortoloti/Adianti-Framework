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
    private $object;

    use Adianti\Base\AdiantiStandardFormTrait; // Standard form methods

    function __construct()
    {

        parent::__construct();

        $this->setDatabase('sale');          // defines the database
        $this->setActiveRecord('Sale');         // defines the active record

        $this->form = new BootstrapFormBuilder('form_pdf_html3');
        $this->form->setFormTitle('Gerador PDF html teste 3 ');

        $sale_id = new TDBUniqueSearch('sale_id', 'sale', 'Sale', 'id', 'id');
        $sale_id->setMinLength(0);
        $sale_id->setSize('100%');

        /*
        $cliente_id = new TDBUniqueSearch('cliente_id', 'sale', 'Sale', 'id', 'nome');
        $cliente_id->setMinLength(0);
        $cliente_id->setSize('100%');
        */

        $this->form->addFields([new TLabel('Nota')], [$sale_id]);

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

            $this->html = new THtmlRenderer('app/resources/teste3.html');

            $sale = new Sale($param['sale_id']);

            $array_object['name'] = $sale->cliente->nome;

            $this->html->enableSection('main', $array_object);

            $replace = array();

            $sale_items = $sale->get_saleitems();

            //$products = $sale->get_products();

            /*foreach ($products as $index => $product) {

                $replace[] = array(
                    'nome' => $product->nome,
                    'quantidade' => $sale_items[$index]->quantidade,
                    'preco' => $product->preco
                );

                //echo "<pre>";
                //print_r($replace);
                //echo "</pre>";
            }
            */

            //Existem duas maneiras de fazer:

            $total = 0;

            foreach ($sale_items as $sale_item) {

                $total += ($sale_item->product->preco * $sale_item->quantidade);

                $replace[] = array(
                    'codigo' => $sale_item->product->id,
                    'nome' => $sale_item->product->nome,
                    'quantidade' => $sale_item->quantidade,
                    'preco' => $sale_item->product->preco,
                );
            }

            $replacee[] = array(
                'totalnota' => $total
            );

            /*foreach ($sale_items as $index => $sale_item) {

                $replace[] = array(
                    'nome' => $sale_item->product->nome,
                    'quantidade' => $sale_items[$index]->quantidade,
                    'preco' => $sale_item->product->preco
                );
            }
            */

            $this->html->enableSection('produtos', $replace, TRUE);

            $this->html->enableSection('rodape', $replacee, TRUE);

            $this->html->enableSection('eae');

            $this->html->enableSection('salve');

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
