<?php

use Adianti\Control\TPage;
use Adianti\Database\TTransaction;
use Adianti\Widget\Form\TEntry;

/**
 * @author Lucas Bortoloti <bortoloti91@gmail.com
 */
class Pdfteste extends TPage
{
    private $form; // form

    use Adianti\Base\AdiantiStandardFormTrait; // Standard form methods

    function __construct()
    {

        parent::__construct();

        $this->setDatabase('sale');              // defines the database
        $this->setActiveRecord('Product');

        $this->form = new BootstrapFormBuilder('form_pdf3');
        $this->form->setFormTitle('Gerador de PDF Teste');

        $produtos = new TDBUniqueSearch('produtos[]', 'sale', 'Product', 'id', 'nome');
        $produtos->setMinLength(0);
        $produtos->setSize('100%');
        $produtos->setMask('({id}) {nome}');

        $codigo = new TDBUniqueSearch('codigo', 'sale', 'Product', 'id', 'id');
        $codigo->setMinLength(0);
        $codigo->setSize('100%');

        $preco = new TDBUniqueSearch('preco', 'sale', 'Product', 'id', 'preco');
        $preco->setMinLength(0);
        $preco->setSize('100%');

        $this->produto = new TFieldList;
        $this->produto->style = ('width: 100%');
        $this->produto->addField('<b>Produtos</b>', $produtos, ['width' => '50%']);

        //$this->form->addField($produtos);

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

            $produtosSelecionados = $param['produtos'];

            $this->pdf = new FPDF('P', 'pt');
            $this->pdf->SetMargins(2, 2, 2); // define margins
            $this->pdf->AddPage();
            $this->pdf->Ln();
            $this->pdf->Image('app/images/logo.png', 18, 20, 90);
            $this->pdf->SetLineWidth(1);
            $this->pdf->SetTextColor(0, 0, 0);
            $this->pdf->SetFont('Arial', 'B', 10);

            $this->pdf->SetXY(485, 27);
            $this->pdf->Cell(90, 18, 'NOTA FISCAL: 1', 1, 0, 'L');

            $this->addCabecalhoProduto();

            foreach ($produtosSelecionados as $produtoId) {
                $produto = new Product($produtoId);
                $this->addProduto($produto);
            }

            $this->addRodapeProduto();

            $file = 'app/output/pdfteste.pdf';

            if (!file_exists($file) or is_writable($file)) {
                $this->pdf->Output($file);

                $window = TWindow::create(('Designed Danfe'), 0.8, 0.8);
                $object = new TElement('object');
                $object->data  = $file;
                $object->type  = 'application/pdf';
                $object->style = "width: 100%; height:calc(100% - 10px)";
                $object->add('O navegador não suporta a exibição deste conteúdo, <a style="color:#007bff;" target=_newwindow href="' . $object->data . '"> clique aqui para baixar</a>...');

                $window->add($object);
                $window->show();
            } else {
                throw new Exception(_t('Permission denied') . ': ' . $file);
            }

            TTransaction::close();
        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public function addCabecalhoProduto()
    {
        $this->pdf->SetY(160);

        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->SetX(20);
        $this->pdf->Cell(300, 6, 'DADOS DO PRODUTO: ', 0, 0, 'L');

        $this->pdf->Ln(12);
        $this->pdf->SetX(20);
        $this->pdf->SetFillColor(230, 230, 230);
        $this->pdf->Cell(80,  12, utf8_decode('Código'),     1, 0, 'L', 1);
        $this->pdf->Cell(370, 12, utf8_decode('Nome'),  1, 0, 'L', 1);
        $this->pdf->Cell(105,  12, 'Valor',      1, 0, 'L', 1);
    }

    public function addProduto($produto)
    {
        $this->pdf->Ln(12);
        $this->pdf->SetX(20);
        $this->pdf->SetFillColor(230, 230, 230);
        $total = $produto->preco * $produto->quantidade;

        $this->pdf->Cell(80,  12, $produto->id, 'LR', 0, 'C');
        $this->pdf->Cell(370, 12, $produto->nome, 'LR', 0, 'L');
        $this->pdf->Cell(105,  12, number_format($produto->preco, 2), 'LR', 0, 'R');
    }

    public function addRodapeProduto()
    {
        if ($this->count_produtos < 20) {
            for ($n = 0; $n < 20 - $this->count_produtos; $n++) {
                $this->pdf->Ln(12);
                $this->pdf->SetX(20);
                $this->pdf->Cell(80,  12, '', 'LR', 0, 'C');
                $this->pdf->Cell(370, 12, '', 'LR', 0, 'L');
                $this->pdf->Cell(105,  12, '', 'LR', 0, 'R');
            }
        }
        $this->pdf->Ln(12);
        $this->pdf->Line(20, $this->pdf->GetY(), 575, $this->pdf->GetY());
    }
}
