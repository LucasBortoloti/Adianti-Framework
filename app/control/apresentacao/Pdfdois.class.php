<?php

use Adianti\Control\TPage;

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

        $this->games = new TFieldList;
        $this->games->style = ('width: 100%');
        $this->games->addField('<b>Jogos</b>', $jogos, ['width' => '50%']);

        $this->form->addField($jogos);

        $this->games->addDetail(new stdClass);

        $row = $this->form->addContent([$this->games]);
        $row->layout = ['col-sm-12'];

        $this->form->addAction('Gerar', new TAction([$this, 'onGenerate'], ['static' => 1]), 'fa:cogs');

        $image11 = new TImage('app/images/2023.png');
        $image11->style = 'width: 10%;';
        $image11->style = 'height: 350px;';

        $table = new TTable;
        $table->border = 0;
        $table->style = 'border-collapse:collapse';
        $table->width = '100%';
        $table->addRowSet($image11);

        parent::add($this->form);

        parent::add($table);
    }

    function onGenerate($param)
    {
        try {
            $designer = new TPDFDesigner;
            $designer->fromXml('app/reports/pdf2.pdf.xml');
            $designer->generate();

            $designer->SetFont('Arial', 'B', 12);
            $designer->writeAtAnchor('nome', 'Party Animals');
            $designer->writeAtAnchor('genero', utf8_decode('Ação'));
            $designer->writeAtAnchor('xbox', utf8_decode('Xbox'));

            $file = 'app/output/pdf2.pdf';

            if (!file_exists($file) or is_writable($file)) {
                $designer->save($file);
                parent::openFile($file);
            } else {
                throw new Exception(t_('Permission Denied') . '; ' . $file);
            }
        } catch (Exception $e) {
            new TMessage('error', '<b>Error</b>' . $e->getMessage());
            TTransaction::rollback();
        }
    }
}
