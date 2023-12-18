<?php

use Adianti\Control\TPage;
use Adianti\Database\TTransaction;
use Adianti\Widget\Form\TEntry;

/**
 * @author Lucas Bortoloti <bortoloti91@gmail.com
 */
class Pdfdois extends TPage
{
    private $form; // form

    use Adianti\Base\AdiantiStandardFormTrait; // Standard form methods

    function __construct()
    {

        parent::__construct();

        $this->setDatabase('jogos');              // defines the database
        $this->setActiveRecord('Jogos');

        $this->form = new BootstrapFormBuilder('form_pdf2');
        $this->form->setFormTitle('Gerador de PDF 2');

        $jogos = new TDBUniqueSearch('jogos', 'jogos', 'Jogos', 'id', 'nome');
        $jogos->setMinLength(0);
        $jogos->setSize('100%');
        $jogos->setMask('({id}) {nome}');

        $ano = new TDBUniqueSearch('ano', 'jogos', 'Jogos', 'id', 'ano_lancamento');
        $ano->setMinLength(0);
        $ano->setSize('100%');

        $avaliacoes = new TDBUniqueSearch('avaliacoes', 'jogos', 'Jogos', 'id', 'quantidade_avaliacoes');
        $avaliacoes->setMinLength(0);
        $avaliacoes->setSize('100%');

        $vendas = new TDBUniqueSearch('vendas', 'jogos', 'Jogos', 'id', 'vendas');
        $vendas->setMinLength(0);
        $vendas->setSize('100%');

        $bom = new TDBUniqueSearch('bom', 'jogos', 'Jogos', 'id', 'avaliacoes');
        $bom->setMinLength(0);
        $bom->setSize('100%');

        $this->games = new TFieldList;
        $this->games->style = ('width: 100%');
        $this->games->addField('<b>Jogos</b>', $jogos, ['width' => '50%']);

        $this->form->addField($jogos);

        $this->games->addHeader();
        $this->games->addDetail(new stdClass);

        $row = $this->form->addContent([$this->games]);
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
            TTransaction::open('jogos');
            $jogos = new Jogos($param['jogos']);
            $ano = new Jogos($param['jogos']);
            $avaliacoes = new Jogos($param['jogos']);
            $vendas = new Jogos($param['jogos']);
            $bom = new Jogos($param['jogos']);

            $designer = new TPDFDesigner;
            $designer->fromXml('app/reports/pdf2.pdf.xml');
            $designer->generate();

            $designer->SetFont('Arial', 'B', 12);
            $designer->writeAtAnchor('nome', utf8_decode($jogos->nome));
            $designer->writeAtAnchor('ano', utf8_decode($ano->ano_lancamento));
            $designer->writeAtAnchor('avaliacoes', utf8_decode($avaliacoes->quantidade_avaliacoes));
            $designer->writeAtAnchor('vendas', utf8_decode($vendas->vendas));
            $designer->writeAtAnchor('bom', utf8_decode($bom->avaliacoes));

            $file = 'app/output/pdf2.pdf';

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
