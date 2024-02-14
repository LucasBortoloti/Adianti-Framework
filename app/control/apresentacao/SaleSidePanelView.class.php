<?php

use Adianti\Control\TPage;

class SaleSidePanelView extends TPage
{
    protected $form; // form
    protected $detail_list;

    /**
     * Class constructor
     * Creates the page and the registration form
     */
    public function __construct($param)
    {
        parent::__construct();

        parent::setTargetContainer('adianti_right_panel');

        $this->form = new BootstrapFormBuilder('form_Sale_View');

        $this->form->setFormTitle('Sale');
        $this->form->setColumnClasses(2, ['col-sm-3', 'col-sm-9']);
        $this->form->addHeaderActionLink(_t('Print'), new TAction([$this, 'onPrint'], ['key' => $param['key'], 'static' => '1']), 'far:file-pdf red');
        $this->form->addHeaderActionLink(_t('Edit'), new TAction([$this, 'onEdit'], ['key' => $param['key'], 'register_state' => 'true']), 'far:edit blue');
        $this->form->addHeaderActionLink(_t('Close'), new TAction([$this, 'onClose']), 'fa:times red');

        parent::add($this->form);
    }

    public function onView($param)
    {
        try {
            TTransaction::open('sale');

            $master_object = new Sale($param['key']);

            $label_id = new TLabel('Id:', '#333333', '16px', 'b');
            $label_date = new TLabel('Date:', '#333333', '16px', 'b');
            $label_total = new TLabel('Total:', '#333333', '16px', 'b');
            $label_cliente_id = new TLabel('Cliente:', '#333333', '16px', 'b');

            $text_id  = new TTextDisplay($master_object->id, '#333333', '16px', '');
            $text_date  = new TTextDisplay($master_object->date, '#333333', '16px', '');
            $text_total  = new TTextDisplay('R$ ' . number_format($master_object->total, 2, ',', '.'));
            $text_cliente_id  = new TTextDisplay(Cliente::find($master_object->cliente_id)->nome, '#333333', '16px', '');

            $this->form->addFields([$label_id], [$text_id]);
            $this->form->addFields([$label_date], [$text_date]);
            $this->form->addFields([$label_total], [$text_total]);
            $this->form->addFields([$label_cliente_id], [$text_cliente_id]);

            $this->detail_list = new BootstrapDatagridWrapper(new TDataGrid);
            $this->detail_list->style = 'width:100%';
            $this->detail_list->disableDefaultClick();

            $product       = new TDataGridColumn('product->nome',  'Produto', 'left');
            $preco         = new TDataGridColumn('sale_price',  'Preco',    'right');
            $quantidade    = new TDataGridColumn('quantidade',  'Quantidade',    'center');
            $discount      = new TDataGridColumn('discount',  'Discount',    'right');
            $total         = new TDataGridColumn('=({quantidade} * {sale_price}) - {discount}', 'Total', 'right');

            $this->detail_list->addColumn($product);
            $this->detail_list->addColumn($preco);
            $this->detail_list->addColumn($quantidade);
            $this->detail_list->addColumn($discount);
            $this->detail_list->addColumn($total);

            $format_value = function ($value) {
                if (is_numeric($value)) {
                    return 'R$ ' . number_format($value, 2, ',', '.');
                }
                return $value;
            };

            $preco->setTransformer($format_value);
            $total->setTransformer($format_value);

            // define totals
            $total->setTotalFunction(function ($values) {
                return array_sum((array) $values);
            });

            $this->detail_list->createModel();

            $items = SaleItem::where('sale_id', '=', $master_object->id)->load();
            $this->detail_list->addItems($items);

            $panel = new TPanelGroup('Itens', '#f5f5f5');
            $panel->add($this->detail_list);
            $panel->getBody()->style = 'overflow-x:auto';

            $this->form->addContent([$panel]);
            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    public function onPrint($param)
    {
        try {
            $this->onView($param);

            // string with HTML contents
            $html = clone $this->form;
            $contents = file_get_contents('app/resources/styles-print.html') . $html->getContents();

            $options = new \Dompdf\Options();
            $options->setChroot(getcwd());

            // converts the HTML template into PDF
            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($contents);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $file = 'app/output/sale-export.pdf';

            // write and open file
            file_put_contents($file, $dompdf->output());

            $window = TWindow::create('Export', 0.8, 0.8);
            $object = new TElement('object');
            $object->data  = $file . '?rndval=' . uniqid();
            $object->type  = 'application/pdf';
            $object->style = "width: 100%; height:calc(100% - 10px)";
            $object->add('O navegador não suporta a exibição deste conteúdo, <a style="color:#007bff;" target=_newwindow href="' . $object->data . '"> clique aqui para baixar</a>...');

            $window->add($object);
            $window->show();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    /**
     * onEdit
     */
    public static function onEdit($param)
    {
        unset($param['static']);
        $param['register_state'] = 'false';
        AdiantiCoreApplication::loadPage('SaleForm', 'onEdit', $param);
    }

    /**
     * Close side panel
     */
    public static function onClose($param)
    {
        TScript::create("Template.closeRightPanel()");
    }
}
