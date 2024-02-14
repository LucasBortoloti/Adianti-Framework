<?php

use Adianti\Control\TPage;

class RelatorioVenda extends TPage
{

    private $form;
    private $faturamento;
    private $total;
    private $valorProduto;

    public function __construct()
    {

        parent::__construct();

        $this->form = new TForm('form_Customer_Report');
        $this->form->class = 'tform';

        $table = new TTable;
        $table->width = '100%';
        $this->form->add($table);

        $quantidade = new TEntry('quantidade');
        $venda_id = new TDBCombo('venda_id', 'venda', 'Venda', 'id', 'cliente');
        $produto_id = new TDBCombo('produto_id', 'venda', 'Produto', 'id', 'nome');
        $output_type = new TRadioGroup('output_type');

        $options = array('html' => 'HTML', 'pdf' => 'PDF', 'rtf' => 'RTF');
        $output_type->addItems($options);
        $output_type->setValue('pdf');
        $output_type->setLayout('horizontal');

        $quantidade->setSize(200);
        $venda_id->setSize(250);
        $produto_id->setSize(250);

        $row = $table->addRowSet(new TLabel('Venda', ''));
        $row->class = 'tformtittle';

        $table->addRowSet([new TLabel('Quantidade')], [$quantidade]);
        $table->addRowSet([new TLabel('Venda')], [$venda_id]);
        $table->addRowSet([new TLabel('Produto')], [$produto_id]);
        $table->addRowSet([new TLabel('Output')], [$output_type]);

        $save_button = new TButton('generate');
        $save_button->setAction(new TAction(array($this, 'onGenerate')), 'Generate');
        $save_button->setImage('ico_save.png');

        $row = $table->addRowSet($save_button, '');
        $row->class = 'tformaction';

        $this->form->setFields(array($quantidade, $venda_id, $produto_id, $output_type, $save_button));

        parent::add($this->form);
    }

    function onGenerate()
    {
        try {
            // open a transaction with database 'samples'
            TTransaction::open('venda');
            $object = $this->form->getData();

            $reposity = new TRepository('VendaProduto');
            $criteria = new TCriteria;

            if ($object->quantidade) {
                $criteria->add(new TFilter('quantidade', 'like', "%{$object->quantidade}%"));
            }

            if ($object->venda_id) {
                $criteria->add(new TFilter('venda_id', 'like', "%{$object->venda_id}%"));
            }

            if ($object->produto_id) {
                $criteria->add(new TFilter('produto_id', '=', "{$object->produto_id}"));
            }

            $customers = $reposity->load($criteria);
            $format = $object->output_type;

            if ($customers) {

                $widths = array(90, 130, 80, 90, 70);
                switch ($format) {

                    case 'html':
                        $tr = new TTableWriterHTML($widths);
                        break;
                    case 'pdf':
                        $tr = new TTableWriterPDF($widths);
                        break;
                    case 'rtf':
                        $tr = new TTableWriterRTF($widths);
                        break;
                }

                $tr->addStyle('tittle', 'Arial',  '10',  '', '#ffffff', '#407B49');
                $tr->addStyle('datap',  'Arial',  '10',  '',  '#000000', '#869FBB');
                $tr->addStyle('datai',  'Arial', '10',  '',  '#000000', '#ffffff');
                $tr->addStyle('header', 'Times', '16',  '', '#ff0000', '#FFF1B2');
                $tr->addStyle('footer', 'Times', '12',  '', '#2B2B2B', '#B5FFB4');

                $tr->addRow();
                $tr->addCell('Venda', 'center', 'header', 5);

                $tr->addRow();
                $tr->addCell('Venda',           'left', 'tittle');
                $tr->addCell('Produto',         'left', 'tittle');
                $tr->addCell('Quantidade',      'left', 'tittle');
                $tr->addCell('Valor do produto', 'left', 'tittle');
                $tr->addCell('Total',           'left', 'tittle');

                $colour = FALSE;
                $total = 0;
                $faturamento = 0;
                $valorProduto = 0;

                foreach ($customers as $customer) {
                    $style = $colour ? 'datap' : 'datai';

                    $tr->addRow();
                    $tr->addCell($customer->venda->cliente, 'left', $style);
                    $tr->addCell($customer->produto->nome, 'left', $style);
                    $tr->addCell($customer->quantidade,    'left', $style);

                    $colour = !$colour;

                    $valorProduto = number_format($customer->produto->sale_price, 2, ',', '.');
                    $tr->addCell($valorProduto, 'left', $style);

                    $total = number_format($customer->produto->sale_price * $customer->quantidade, 2, ',', '.');
                    $tr->addCell($total, 'left', $style);

                    $faturamento += ($customer->produto->sale_price * $customer->quantidade);
                }

                $faturamento = number_format($faturamento, 2, ',', '.');

                $tr->addRow();
                $tr->addCell('Faturamento',           'left', 'tittle');
                $tr->addCell($faturamento,            'left', $style);
                $tr->addCell(date('Y-m-d h:i:s'), 'center', 'footer', 3);
                $tr->save("app/output/tabular3.{$format}");
                new TMessage('info', 'Relatório gerado');
            } else {
                new TMessage('error', 'Não encontrou registros');
            }
            $this->form->setData($object);
            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('error', '<b>Error</b>' . $e->getMessage());
            TTransaction::rollback();
        }
    }
}
