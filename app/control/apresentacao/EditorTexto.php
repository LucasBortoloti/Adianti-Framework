<?php

use Adianti\Control\TPage;
use MarceloNees\TTinyMceAdianti\TTinyMceEditor\TTinyMceEditor;

class EditorTexto extends TPage
{
    private $form;

    /**
     * Page constructor
     */
    function __construct()
    {
        parent::__construct();

        // create the form
        $this->form = new BootstrapFormBuilder('my_html_form');
        $this->form->setFormTitle(('Editor de Texto '));

        // create the form fields
        $html = new TTinyMceEditor('html_text');
        $html->setSize('100%', 200);

        $this->form->addFields([$html]);
        $this->form->addAction('Show', new TAction(array($this, 'onShow')), 'far:check-circle blue');
        $this->form->addAction('Insert text', new TAction(array($this, 'onInsert')), 'fa:plus green');

        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        parent::add($vbox);
    }

    public static function onInsert($param)
    {
        TTinyMceEditor::insertText('my_html_form', 'html_text', 'São Paulo FC, maior do Brasil ! Três mundiais e Três Libertadores.');
    }

    public function onShow($param)
    {
        $data = $this->form->getData();
        $this->form->setData($data); // put the data back to the form

        // show the message
        new TMessage('info', $data->html_text);
    }
}
