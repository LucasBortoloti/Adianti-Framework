<?php

use Adianti\Control\TPage;

/**
 * @author Lucas Bortoloti <bortoloti91@gmail.com
 */
class Pdf extends TPage
{
    private $form; // form

    use Adianti\Base\AdiantiStandardFormTrait; // Standard form methods

    function __construct()
    {

        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_pdf');
        $this->form->setFormTitle('Gerador de PDF');

        $image11 = new TImage('app/images/2023.png');
        $image11->style = 'width: 10%;';
        $image11->style = 'height: 350px;';

        $this->form->addAction('Gerar', new TAction([$this, 'onGenerate'], ['static' => 1]), 'fa:cogs');

        $table = new TTable;
        $table->border = 0;
        $table->style = 'border-collapse:collapse';
        $table->width = '100%';
        $table->addRowSet($image11);

        parent::add($this->form);

        parent::add($table);
    }

    function onGenerate()
    {
        try {
            $designer = new TPDFDesigner;
            $designer->fromXml('app/reports/pdf.pdf.xml');
            $designer->generate();

            $designer->SetFont('Arial', 'B', 10);
            $designer->writeAtAnchor('nome', utf8_decode('Lucas Bortoloti'));
            $designer->writeAtAnchor('dt', utf8_decode('18/10/2003'));
            $designer->writeAtAnchor('hb', utf8_decode('Jogar no pc'));
            $designer->writeAtAnchor('coracao', utf8_decode('São Paulo FC'));
            $designer->writeAtAnchor('jogo', utf8_decode('The Last of Us'));
            $designer->writeAtAnchor('xbox', utf8_decode('Xbox'));

            $file = 'app/output/pdf.pdf';

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
