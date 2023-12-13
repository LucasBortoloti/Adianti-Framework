<?php

use Adianti\Control\TPage;

class EditorImagem extends TPage
{
    private $form;

    /**
     * Page constructor
     */
    function __construct()
    {
        parent::__construct();

        // create the form
        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle(('Editor de Imagem'));

        // create the form fields
        $imagecropper = new TImageCropper('imagecropper');
        $imageupload  = new TFile('imageupload');

        $imagecropper->setSize(300, 150);
        $imagecropper->setCropSize(300, 150);
        $imagecropper->setAllowedExtensions(['gif', 'png', 'jpg', 'jpeg']);
        //$imagecropper->enableFileHandling();

        $imageupload->setAllowedExtensions(['gif', 'png', 'jpg', 'jpeg']);
        //$imageupload->enableFileHandling();
        $imageupload->enableImageGallery();

        $this->form->addFields([new TLabel('Image Cropper')], [$imagecropper]);
        $this->form->addFields([new TLabel('Image Uploader')], [$imageupload]);

        $this->form->addAction('Show', new TAction(array($this, 'onShow')), 'far:check-circle green');

        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        parent::add($vbox);
    }

    public static function onChange($param)
    {
        new TMessage('info', '<b>onChange</b><br>' . str_replace(',', '<br>', json_encode($param)));
    }

    public static function onShow($param)
    {
        // show the message
        new TMessage('info', '<b>Image Crop</b>: tmp/' . $param['imagecropper'] . '<br>' .
            ' <b>Image Upload</b>: tmp/' . $param['imageupload']);
    }
}
