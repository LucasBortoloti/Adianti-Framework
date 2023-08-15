<?php

use Adianti\Control\TPage;

class ContainerTableColumnsView extends TPage{

    public function __construct(){

        parent:: __construct();
    
        $table = new TTable;
        $table->border = 5 ;

        /*
        $lbl_codigo = new TLabel('Código');
        $lbl_nome = new TLabel('Nome');
        $lbl_codigo->setFontSize(14);
        $lbl_nome->setFontSize(14);
        $lbl_codigo->setFontColor('red');
        */
        $data1 = new TDate('data1');
        $data2 = new TDate('data2');
        $data1->setSize(60);
        $data2->setSize(60);

        $row = $table->addRow();
        $cell = $row->addCell('Título');
        $cell->colspan = 2;
        
        $table->addRowSet('Código', new TEntry('id'));
        $table->addRowSet('Nome', new TEntry('nome'));
        $table->addRowSet('Data', array ($data1,'até', $data2));

        parent::add($table);

    }
}
















?>