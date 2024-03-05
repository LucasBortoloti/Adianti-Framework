<?php

use Adianti\Control\TPage;
use Adianti\Database\TTransaction;
use Adianti\Widget\Form\TEntry;

/**
 * @author Lucas Bortoloti <bortoloti91@gmail.com
 */
class Pdfhtml2 extends TPage
{
    private $form; // form

    use Adianti\Base\AdiantiStandardFormTrait; // Standard form methods

    function __construct()
    {

        parent::__construct();

        $this->setDatabase('sale');              // defines the database
        $this->setActiveRecord('Product');

        $this->form = new BootstrapFormBuilder('form_pdf_html2');
        $this->form->setFormTitle('Gerador PDF html teste 2 ');

        $produtos = new TDBUniqueSearch('produtos[]', 'sale', 'Product', 'id', 'nome');
        $produtos->setMinLength(0);
        $produtos->setSize('100%');
        $produtos->setMask('({id}) {nome}');

        $quantidade = new TNumeric('quantidade[]', 0, ',', '.');

        $this->produto = new TFieldList;
        $this->produto->style = ('width: 100%');
        $this->produto->addField('<b>Produtos</b>', $produtos, ['width' => '50%']);
        $this->produto->addField('<b>Qntd</b>', $quantidade, ['width' => '10%']);

        $this->form->addField($produtos);
        $this->form->addField($quantidade);

        $this->produto->addHeader();
        $this->produto->addDetail(new stdClass);
        $this->produto->addCloneAction();

        $row = $this->form->addContent([$this->produto]);
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
            $data = $this->form->getData();

            $produtosSelecionados = $param['produtos'];

            $this->html = new THtmlRenderer('app/resources/teste2.html');

            foreach ($produtosSelecionados as $index => $produtoId) {
                $produtos = new Product($produtoId);
                $produtos->quantidade = $data->quantidade[$index] ?? 1;
                $this->addProduto($produtos);
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

    public function addProduto($produto)
    {
        $pdf = new stdClass;
        $pdf->name = 'Lucas';

        $array_object['name'] = $pdf->name;

        $this->html->enableSection('main', $array_object);

        $replace = array();

        $replace[] = array(
            'nome' => $produto->nome,
            'quantidade' => $produto->quantidade,
            'preco' => $produto->preco
        );

        $this->html->enableSection('produtos', $replace, TRUE);
    }

    /*public function addProduto($produtos)
    {

        $object = new stdClass;
        $object->name = 'Lucas';

        $array_object['name'] = $object->name;

        $this->html->enableSection('main', $array_object);

        $replace = array();

        $produtos = $object->getProduct();

        foreach ($produtos as $produto) {

            $replace[] = array(
                'nome' => $produto->nome,
                'quantidade' => $produto->quantidade,
                'preco' => $produto->preco
            );

            $this->html->enableSection('produtos', $replace, TRUE);
        }
    }*/
}
