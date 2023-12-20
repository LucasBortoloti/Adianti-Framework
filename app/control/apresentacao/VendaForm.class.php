<?php

use Adianti\Control\TPage;

/**
 * VendaForm Registration
 * @author Lucas Bortoloti <bortoloti91@gmail.com
 */
class VendaForm extends TPage
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
        $this->setActiveRecord('Venda');     // defines the active record

        // creates the form
        $this->form = new BootstrapFormBuilder('form_Venda');
        $this->form->setFormTitle('Venda');

        // create the form fields 
        $id = new TEntry('id');
        $data_venda = new TEntry('data_venda');
        $cliente = new TEntry('cliente');

        // add the fields 
        $this->form->addFields([new TLabel('id')], [$id]);
        $this->form->addFields([new TLabel('data_venda')], [$data_venda]);
        $this->form->addFields([new TLabel('cliente')], [$cliente]);

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
