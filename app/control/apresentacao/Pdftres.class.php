<?php

use Adianti\Control\TPage;
use Adianti\Database\TTransaction;
use Adianti\Widget\Form\TEntry;

/**
 * @author Lucas Bortoloti <bortoloti91@gmail.com
 */
class Pdftres extends TPage
{
    private $form; // form

    use Adianti\Base\AdiantiStandardFormTrait; // Standard form methods

    function __construct()
    {

        parent::__construct();

        $this->setDatabase('sale');              // defines the database
        $this->setActiveRecord('Product');

        $this->form = new BootstrapFormBuilder('form_pdf3');
        $this->form->setFormTitle('Gerador de PDF 3');

        $produtos = new TDBUniqueSearch('produtos', 'sale', 'Product', 'id', 'nome');
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

        $this->form->addField($produtos);

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

        $imagefooter = new TImage('app/images/sao.png');
        $imagefooter->style = 'width: 10%;';
        $imagefooter->style = 'height: 70px;';
    }

    function onGenerate($param)
    {
        try {
            TTransaction::open('sale');

            // Obtém a lista de produtos selecionados
            $produtosSelecionados = explode(',', $param['produtos']);

            $designer = new TPDFDesigner;
            $designer->fromXml('app/reports/pdf3.pdf.xml');
            $designer->generate();

            $designer->SetFont('Arial', '', 12);

            // Itera sobre a lista de produtos selecionados
            foreach ($produtosSelecionados as $produtoId) {
                $produto = new Product($produtoId);

                // Ajusta a posição Y para evitar a sobrescrição
                $designer->writeAtAnchor('cliente', utf8_decode('Lucas'));
                $designer->writeAtAnchor('codigo', utf8_decode($produto->id));
                $designer->writeAtAnchor('produto', utf8_decode($produto->nome));
                $designer->writeAtAnchor('preco', utf8_decode($produto->preco));
            }

            $file = 'app/output/pdf3.pdf';

            if (!file_exists($file) or is_writable($file)) {
                $designer->save($file);
                parent::openFile($file);
            } else {
                throw new Exception(t_('Permission Denied') . '; ' . $file);
            }

            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('error', '<b>Error</b>' . $e->getMessage());
            TTransaction::rollback();
        }
    }
}
