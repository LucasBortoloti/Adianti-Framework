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

        $cliente = new TDBUniqueSearch('cliente[]', 'sale', 'Cliente', 'id', 'nome');
        $cliente->setMinLength(0);
        $cliente->setSize('100%');
        $cliente->setMask('{nome} ({id})');

        $this->venda = new TFieldList;
        $this->venda->style = ('width: 100%');
        $this->venda->addField('<b>Cliente</b>', $cliente, ['width' => '50%']);

        $this->form->addField($cliente);

        $this->venda->addHeader();
        $this->venda->addDetail(new stdClass);
        $this->venda->addCloneAction();

        $row = $this->form->addContent([$this->venda]);
        $row->layout = ['col-sm-12'];

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

            $clienteSelecionado = $param['cliente_id'];

            $this->html = new THtmlRenderer('app/resources/teste3.html');

            foreach ($clienteSelecionado as $clienteId) {
                $clientes = new Product($clienteId);
                $this->addProduto($clientes);
            }

            echo "<pre>";
            print_r($param);
            echo "</pre>";

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

    public function addProduto()
    {
        $data = $this->form->getData();

        TTransaction::open('sale');

        $object = Sale::find($data->cliente_id);

        $array_object['nome'] = $object->name;

        $this->html->enableSection('main', $array_object);

        $replace = array();

        $products = $object->get_product();

        foreach ($products as $product) {

            $replace[] = array(
                'nome' => $product->nome,
                'quantidade' => $product->quantidade,
                'preco' => $product->preco
            );

            $this->html->enableSection('produtos', $replace, TRUE);
        }
        TTransaction::close();
    }
}
