<?php

use Adianti\Control\TPage;

class RelatorioSaleList extends TPage
{
    private $form; // form
    private $pdf;
    private $total_produtos;
    private $count_produtos;

    public function __construct($param)
    {
        parent::__construct($param);

        $this->form = new BootstrapFormBuilder('form_nota');
        $this->form->setFormTitle('Nota fiscal completa');
        $this->form->setFieldSizes('100%');

        $cliente_id = new TDBUniqueSearch('cliente_id', 'sale', 'Cliente', 'id', 'nome');
        $cliente_id->setMinLength(1);
        $cliente_id->setValue(1);
        $cliente_id->setMask('{nome} ({id})');

        $cnpj = new TEntry('cnpj');
        $id = new TEntry('id');

        $id->setMask('9999');
        $cnpj->setMask('99.999.999/9999-99');

        $id->setValue('001');

        $this->form->addAction('Generate', new TAction([$this, 'onGenerator'], ['static' => 1]), 'fa:cogs');

        $product = new TDBUniqueSearch('product_id[]', 'sale', 'Product', 'id', 'nome');
        $product->setMinLength(0);
        $product->setSize('100%');
        $product->setMask('({id}) {nome}');

        $quantidade = new TNumeric('quantidade[]', 0, ',', '.');
        $preco = new TNumeric('preco[]', 2, ',', '.');
        $total = new TNumeric('total[]', 2, ',', '.');

        $preco->setEditable(FALSE);
        $total->setEditable(FALSE);

        $product->setChangeAction(new TAction([$this, 'onChange']));
        $quantidade->setExitAction(new TAction([$this, 'onChange']));

        $quantidade->setSize('100%');
        $preco->setSize('100%');
        $total->setSize('100%');

        $this->products = new TFieldList;
        $this->products->style = ('width: 100%');
        $this->products->addField('<b>Product</b>', $product, ['width' => '50%']);
        $this->products->addField('<b>Qntd</b>', $quantidade, ['width' => '10%', 'sum' => true]);
        $this->products->addField('<b>Preco</b>', $preco, ['width' => '20%', 'sum' => true]);
        $this->products->addField('<b>Total</b>', $total, ['width' => '20%', 'sum' => true]);

        $this->products->enableSorting();

        $this->form->addField($product);
        $this->form->addField($quantidade);
        $this->form->addField($preco);
        $this->form->addField($total);

        //alternativa de fazer a página continuar funcionando da mesma maneira
        //$emptyDetail = new TElement('div');
        //$this->products->addDetail($emptyDetail);

        $this->products->addHeader();
        $this->products->addDetail(new stdClass);
        $this->products->addCloneAction();

        $row = $this->form->addFields([new TLabel('Numero'), $id], [new TLabel('Cliente'), $cliente_id]);
        $row->layout = ['col-sm-2', 'col-sm-10'];

        $row = $this->form->addContent([$this->products]);
        $row->layout = ['col-sm-12'];

        //$row = $this->form->addFields( [new TLabel('Produto'), $product], [new TLabel('Qntd'), $quantidade], [new TLabel('Preco'), $preco], [new TLabel('Total'), $total]);
        //$row->layout = ['col-sm-5', 'col-sm-4', 'col-sm-4', 'col-sm-2'];

        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        //$vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        parent::add($vbox);
    }

    public function onGenerator()
    {
        try {
            TTransaction::open('sale');
            $data = $this->form->getData();

            //echo "<pre>";
            //print_r($data);
            //echo "</pre>";

            $cliente = Cliente::find($data->cliente_id);

            $this->pdf = new FPDF('P', 'pt');
            $this->pdf->SetMargins(2, 2, 2); // define margins
            $this->pdf->AddPage();
            $this->pdf->Ln();
            $this->pdf->Image('app/images/logo.png', 10, 20, 90);
            $this->pdf->SetLineWidth(1);
            $this->pdf->SetTextColor(0, 0, 0);
            $this->pdf->SetFont('Arial', 'B', 10);

            $this->pdf->SetXY(470, 27);
            $this->pdf->Cell(100, 20, 'NOTA FISCAL: ' . $data->id, 1, 0, 'L');

            $this->addCliente($cliente);
            $this->addCabecalhoProduto();

            if ($data->product_id) {
                foreach ($data->product_id as $index => $product_id) {
                    $produto = Product::find($product_id);
                    $produto->quantidade = $data->quantidade[$index] ?? 1;
                    $this->addProduto($produto);
                }
            }

            $this->addRodapeProduto();

            $this->addRodapeNota();

            $file = 'app/output/danfe.pdf';

            if (!file_exists($file) or is_writable($file)) {
                $this->pdf->Output($file);

                $window = TWindow::create(_t('Designed Danfe'), 0.8, 0.8);
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

    public function addCliente($cliente)
    {
        $this->pdf->SetY(130);

        $this->pdf->SetFont('Arial', '', 8);
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->SetX(20);
        $this->pdf->Cell(300, 12, utf8_decode('DESTINATÁRIO/REMETENTE: '), 0, 0, 'L');

        $this->pdf->Ln(12);

        $this->pdf->SetTextColor(100, 100, 100);
        $this->pdf->SetX(20);
        $this->pdf->Cell(300, 12, utf8_decode('Nome/Razão Social: '), 'LTR', 0, 'L');
        $this->pdf->Cell(150, 12, utf8_decode('CNPJ/CPF: '), 'LTR', 0, 'L');
        $this->pdf->Cell(100, 12, utf8_decode('Data de emissão: '), 'LTR', 0, 'L');

        $this->pdf->Ln(8);

        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->SetX(20);
        $this->pdf->Cell(300, 16, utf8_decode($cliente->nome), 'LBR', 0, 'L');
        $this->pdf->Cell(150, 16, $cliente->cnpj, 'LBR', 0, 'L');
        $this->pdf->Cell(100, 16, date('d/m/Y'), 'LBR', 0, 'L');

        $this->pdf->Ln(16);

        $this->pdf->SetTextColor(100, 100, 100);
        $this->pdf->SetX(20);
        $this->pdf->Cell(180, 12, utf8_decode('Município: '), 'LTR', 0, 'L');
        $this->pdf->Cell(70,  12, utf8_decode('Fone/Fax: '), 'LTR', 0, 'L');
        $this->pdf->Cell(100, 12, utf8_decode('UF: '), 'LTR', 0, 'L');
        $this->pdf->Cell(100, 12, utf8_decode('Inscrição Estadual: '), 'LTR', 0, 'L');
        $this->pdf->Cell(100, 12, utf8_decode('Hora Saída: '), 'LTR', 0, 'L');

        $this->pdf->Ln(8);

        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->SetX(20);
        $this->pdf->Cell(180, 16, utf8_decode($cliente->cidade), 'LBR', 0, 'L');
        $this->pdf->Cell(70,  16, utf8_decode($cliente->fone), 'LBR', 0, 'L');
        $this->pdf->Cell(100, 16, utf8_decode($cliente->estado), 'LBR', 0, 'L');
        $this->pdf->Cell(100, 16, '', 'LBR', 0, 'L');
        $this->pdf->Cell(100, 16, date('H:i'), 'LBR', 0, 'L');

        $this->pdf->Ln(16);
    }

    public function addCabecalhoProduto()
    {
        $this->pdf->SetY(220);

        $this->pdf->SetFont('Arial', '', 8);
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->SetX(20);
        $this->pdf->Cell(300, 12, 'DADOS DO PRODUTO: ', 0, 0, 'L');

        $this->pdf->Ln(12);
        $this->pdf->SetX(20);
        $this->pdf->SetFillColor(230, 230, 230);
        $this->pdf->Cell(40,  12, utf8_decode('Código'),     1, 0, 'L', 1);
        $this->pdf->Cell(330, 12, utf8_decode('Nome'),  1, 0, 'L', 1);
        $this->pdf->Cell(55,  12, 'Qntd', 1, 0, 'L', 1);
        $this->pdf->Cell(60,  12, 'Valor',      1, 0, 'L', 1);
        $this->pdf->Cell(65,  12, 'Total',      1, 0, 'L', 1);
        //$this->pdf->Cell(30,  12, 'ICMS',       1, 0, 'L', 1);
    }

    public function addProduto($produto)
    {
        $this->pdf->Ln(12);
        $this->pdf->SetX(20);
        $this->pdf->SetFillColor(230, 230, 230);
        $total = $produto->preco * $produto->quantidade;

        $this->pdf->Cell(40,  12, $produto->id, 'LR', 0, 'C');
        $this->pdf->Cell(330, 12, $produto->nome, 'LR', 0, 'L');
        $this->pdf->Cell(55,  12, $produto->quantidade, 'LR', 0, 'C');
        $this->pdf->Cell(60,  12, number_format($produto->preco, 2), 'LR', 0, 'R');
        $this->pdf->Cell(65,  12, number_format($total, 2), 'LR', 0, 'R');
        //$this->pdf->Cell(30,  12, number_format(6, 2),       'LR', 0, 'C');

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
                $this->pdf->Cell(55,  12, '', 'LR', 0, 'C');
                $this->pdf->Cell(60,  12, '', 'LR', 0, 'R');
                $this->pdf->Cell(65,  12, '', 'LR', 0, 'R');
                //$this->pdf->Cell(30,  12, '', 'LR', 0, 'C');
            }
        }
        $this->pdf->Ln(12);
        $this->pdf->Line(20, $this->pdf->GetY(), 570, $this->pdf->GetY());
    }

    public function addRodapeNota()
    {
        $this->pdf->Ln(20);

        $this->pdf->SetFont('Arial', '', 8);
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->SetX(20);
        $this->pdf->Cell(300, 12, 'DADOS ADICIONAIS: ', 0, 0, 'L');

        $this->pdf->Ln(12);
        $this->pdf->SetTextColor(100, 100, 100);
        $this->pdf->SetX(20);
        $this->pdf->Cell(280, 12, utf8_decode('Informações complementares'), 'LTR', 0, 'L');
        $this->pdf->Cell(270, 12, 'Reservado ao fisco', 'LTR', 0, 'L');

        $this->pdf->Ln(8);

        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->SetX(20);
        $this->pdf->Cell(280, 48, '', 'LBR', 0, 'L');
        $this->pdf->Cell(270, 48, '', 'LBR', 0, 'L');

        $this->pdf->Ln(52);

        $this->pdf->SetX(20);
        $this->pdf->Cell(300, 12, utf8_decode('INFORMAÇÕES DE RECEBIMENTO: '), 0, 0, 'L');

        $this->pdf->Ln(12);
        $this->pdf->SetTextColor(100, 100, 100);
        $this->pdf->SetX(20);
        $this->pdf->Cell(400, 12, 'Recebemos de Tutor os produtos constantes na nota fiscal indicada ao lado', 'LTR', 0, 'L');
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->Cell(150, 12, 'NOTA FISCAL', 'LTR', 0, 'C');

        $this->pdf->Ln(12);

        $this->pdf->SetX(20);
        $this->pdf->SetTextColor(100, 100, 100);
        $this->pdf->Cell(200, 12, 'Data do recebimento', 'LTR', 0, 'L');
        $this->pdf->Cell(200, 12, utf8_decode('Identificação e assinatura do recebedor'), 'LTR', 0, 'L');
        $this->pdf->Cell(150, 12, '', 'LR', 0, 'L');

        $this->pdf->Ln(8);
        $this->pdf->SetX(20);
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->Cell(200, 30, '', 'LBR', 0, 'L');
        $this->pdf->Cell(200, 30, '', 'LBR', 0, 'L');
        $this->pdf->Cell(150, 30, '', 'LBR', 0, 'L');
    }

    public static function onChange($param)
    {
        try {
            TTransaction::open('sale');
            $preco = [];
            $total = [];

            foreach ($param['product_id'] as $i => $product_id) {
                $product = new Product($product_id);
                $quantidade = (float) str_replace(',', '.', str_replace('.', '', ($param['quantidade'][$i] ?? 1)));

                $preco[] = number_format($product->preco, 2, ',', '.');
                $total[] = number_format($product->preco * $quantidade, 2, ',', '.');
            }
            $data = new stdClass;
            $data->preco = $preco;
            $data->total = $total;

            TForm::sendData('form_nota', $data, false, true);
            TTransaction::close();
        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }
}
