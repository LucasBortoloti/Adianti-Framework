<?php

use Adianti\Control\TPage;

/**
 * VendaProdutoForm Registration
 * @author Lucas Bortoloti <bortoloti91@gmail.com
 */
class VendaProdutoForm extends TPage
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
        $this->setActiveRecord('VendaProduto');     // defines the active record

        // creates the form
        $this->form = new BootstrapFormBuilder('form_VendaProdutoForm');
        $this->form->setFormTitle('VendaProduto');

        // create the form fields 
        $quantidade = new TEntry('quantidade');
        $venda_id = new TDBCombo('venda_id', 'venda', 'Venda', 'id', 'cliente');
        $produto_id = new TDBCombo('produto_id', 'venda', 'Produto', 'id', 'nome');

        // add the fields 
        $this->form->addFields([new TLabel('quantidade')], [$quantidade]);
        $this->form->addFields([new TLabel('venda_id')], [$venda_id]);
        $this->form->addFields([new TLabel('produto_id')], [$produto_id]);

        if (!empty($venda_id)) {
            $venda_id->setEditable(FALSE);
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
