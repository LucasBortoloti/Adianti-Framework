<?php

use Adianti\Control\TPage;

/**
 * ProdutoForm Registration
 * @author Lucas Bortoloti <bortoloti91@gmail.com
 */
class ProdutoForm extends TPage
{
    protected $form; // form

    use Adianti\Base\AdiantiStandardFormTrait; // Standard form methods

    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {

        parent::__construct();


        $this->setDatabase('venda');              // defines the database
        $this->setActiveRecord('Produto');     // defines the active record

        // creates the form
        $this->form = new BootstrapFormBuilder('form_Produto');
        $this->form->setFormTitle('Produto');


        // create the form fields 
        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $sale_price = new TEntry('sale_price');


        // add the fields 
        $this->form->addFields([new TLabel('id')], [$id]);
        $this->form->addFields([new TLabel('nome')], [$nome]);
        $this->form->addFields([new TLabel('preco')], [$sale_price]);



        if (!empty($id)) {
            $id->setEditable(FALSE);
        }

        /** samples
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( '100%' ); // set size
         **/

        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);


        parent::add($container);
    }
}
