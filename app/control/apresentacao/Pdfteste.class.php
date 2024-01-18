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

        $quantidade = new TNumeric('quantidade[]', 0, ',', '.');
        $preco = new TNumeric('preco[]', 2, ',', '.');
        $total = new TNumeric('total[]', 2, ',', '.');

        $preco->setEditable(FALSE);
        $total->setEditable(FALSE);

        $produtos->setChangeAction(new TAction([$this, 'onChange']));
        $quantidade->setExitAction(new TAction([$this, 'onChange']));

        $quantidade->setSize('100%');
        $preco->setSize('100%');
        $total->setSize('100%');

        $this->produto = new TFieldList;
        $this->produto->style = ('width: 100%');
        $this->produto->addField('<b>Produtos</b>', $produtos, ['width' => '50%']);
        $this->produto->addField('<b>Qntd</b>', $quantidade, ['width' => '10%']);
        $this->produto->addField('<b>Preço unitário</b>', $preco, ['width' => '20%']);
        $this->produto->addField('<b>Total</b>', $total, ['width' => '20%']);

        $this->form->addField($produtos);
        $this->form->addField($quantidade);
        $this->form->addField($preco);
        $this->form->addField($total);

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

            foreach ($produtosSelecionados as $index => $produtoId) {
                $produto = new Product($produtoId);
                $produto->quantidade = $data->quantidade[$index] ?? 1;
                $this->addProduto($produto);
            }

            $this->addRodapeProduto();

            $this->addRodapeNota();

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

        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->SetX(20);
        $this->pdf->Cell(300, 6, 'DADOS DO PRODUTO: ', 0, 0, 'L');

        $this->pdf->Ln(12);
        $this->pdf->SetX(20);
        $this->pdf->SetFillColor(230, 230, 230);
        $this->pdf->Cell(40, 14, utf8_decode('Código'),     1, 0, 'C', 1);
        $this->pdf->Cell(330, 14, utf8_decode('Nome'),  1, 0, 'L', 1);
        $this->pdf->Cell(55, 14, 'Qntd', 1, 0, 'L', 1);
        $this->pdf->Cell(60, 14, 'Valor', 1, 0, 'L', 1);
        $this->pdf->Cell(70, 14, 'Total', 1, 0, 'L', 1);
    }

    public function addProduto($produto)
    {
        $this->pdf->Ln(12);
        $this->pdf->SetX(20);
        $this->pdf->SetFillColor(230, 230, 230);
        $total = $produto->preco * $produto->quantidade;

        $this->pdf->Cell(40, 18, $produto->id, 'LR', 0, 'C');
        $this->pdf->Cell(330, 18, $produto->nome, 'LR', 0, 'L');
        $this->pdf->Cell(55, 18, $produto->quantidade, 'LR', 0, 'C');
        $this->pdf->Cell(60, 18, number_format($produto->preco, 2), 'LR', 0, 'R');
        $this->pdf->Cell(70, 18, number_format($total, 2), 'LR', 0, 'R');

        $this->total_produtos += $total;
    }

    public function addRodapeProduto()
    {
        if ($this->count_produtos < 20) {
            for ($n = 0; $n < 20 - $this->count_produtos; $n++) {
                $this->pdf->Ln(12);
                $this->pdf->SetX(20);
                $this->pdf->Cell(40,  12, '', 'LR', 0, 'C');
                $this->pdf->Cell(330, 12, '', 'LR', 0, 'L');
                $this->pdf->Cell(55,  12, '', 'LR', 0, 'L');
                $this->pdf->Cell(60,  12, '', 'LR', 0, 'R');
                $this->pdf->Cell(70,  12, '', 'LR', 0, 'R');
            }
        }
        $this->pdf->Ln(12);
        $this->pdf->Line(20, $this->pdf->GetY(), 575, $this->pdf->GetY());
    }

    public function addRodapeNota()
    {
        $this->pdf->Ln(28);

        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->SetX(20);
        $this->pdf->Cell(320, 8, 'DADOS ADICIONAIS: ', 0, 0, 'L');

        $this->pdf->Ln(12);
        $this->pdf->SetTextColor(100, 100, 100);
        $this->pdf->SetX(20);
        $this->pdf->Cell(285, 14, utf8_decode('Informações complementares'), 'LTR', 0, 'L');
        $this->pdf->Cell(270, 14, 'Reservado ao fisco', 'LTR', 0, 'L');

        $this->pdf->Ln(8);

        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->SetX(20);
        $this->pdf->Cell(285, 48, '', 'LBR', 0, 'L');
        $this->pdf->Cell(270, 48, '', 'LBR', 0, 'L');

        $this->pdf->Ln(52);

        $this->pdf->SetX(20);
        $this->pdf->Cell(300, 19, utf8_decode('INFORMAÇÕES DE RECEBIMENTO: '), 0, 0, 'L');

        $this->pdf->Ln(18);
        $this->pdf->SetTextColor(100, 100, 100);
        $this->pdf->SetX(20);
        $this->pdf->Cell(400, 16, 'Recebemos de Tutor os produtos constantes na nota fiscal indicada ao lado', 'LTR', 0, 'L');
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->Cell(155, 16, 'NOTA FISCAL', 'LTR', 0, 'C');

        $this->pdf->Ln(4);
        $this->pdf->SetX(20);
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->Cell(400, 12, '', 'LBR', 0, 'L');

        $this->pdf->Ln(12);

        $this->pdf->SetX(20);
        $this->pdf->SetTextColor(100, 100, 100);
        $this->pdf->Cell(200, 14, 'Data do recebimento', 'LTR', 0, 'L');
        $this->pdf->Cell(200, 14, utf8_decode('Identificação e assinatura do recebedor'), 'LTR', 0, 'L');
        $this->pdf->Cell(155, 14, '', 'LR', 0, 'L');

        $this->pdf->Ln(8);
        $this->pdf->SetX(20);
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->Cell(200, 35, '', 'LBR', 0, 'L');
        $this->pdf->Cell(200, 35, '', 'LBR', 0, 'L');
        $this->pdf->Cell(155, 35, '', 'LBR', 0, 'L');
    }

    public static function onChange($param)
    {
        try {
            TTransaction::open('sale');
            $preco = [];
            $total = [];

            $produtosSelecionados = $param['produtos'];

            foreach ($produtosSelecionados as $i => $produtos) {
                $produto = new Product($produtos);
                $quantidade = (float) str_replace(',', '.', str_replace('.', '', ($param['quantidade'][$i] ?? 1)));

                $preco[] = number_format($produto->preco, 2, ',', '.');
                $total[] = number_format($produto->preco * $quantidade, 2, ',', '.');
            }
            $data = new stdClass;
            $data->preco = $preco;
            $data->total = $total;

            TForm::sendData('form_pdf3', $data, false, true);
            TTransaction::close();
        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }
}
