<?php

use Adianti\Control\TPage;

/**
 * MusicaForm Registration
 * @author Lucas Bortoloti <bortoloti91@gmail.com
 */
class MusicaForm extends TPage
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
        
        
        $this->setDatabase('spotify');              // defines the database
        $this->setActiveRecord('Musica');     // defines the active record
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Musica');
        $this->form->setFormTitle('Musica');
        

        // create the form fields 
        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $genero = new TEntry('genero');
        $duracao = new TEntry('duracao');
        $cantor_id = new TDBCombo('cantor_id', 'spotify', 'Cantor', 'id', 'nome');


 
        // add the fields 
        $this->form->addFields( [ new TLabel('id')] , [ $id ] );
        $this->form->addFields( [ new TLabel('nome')] , [ $nome ] );
        $this->form->addFields( [ new TLabel('genero')] , [ $genero ] );
        $this->form->addFields( [ new TLabel('duracao')] , [ $duracao ] );
        $this->form->addFields( [ new TLabel('cantor_id')] , [ $cantor_id ] );
        

        
        if (!empty($id))
        {
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
