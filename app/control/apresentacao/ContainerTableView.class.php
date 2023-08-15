<?php

use Adianti\Control\TPage;

class ContainerTableView extends TPage{

    public function __construct(){

        parent:: __construct();
    
        $table = new TTable;
        $table->border = 5 ;

        $row = $table->addRow();
        $row->bgcolor = 'lightgray';
        $cell = $row->addCell('a');
        $cell->colspan = 2;

        $lbl_codigo = new TLabel('Código');
        $lbl_nome = new TLabel('Nome');
        $lbl_codigo->setFontSize(14);
        $lbl_nome->setFontSize(14);
        $lbl_codigo->setFontColor('red');


        $row = $table->addRow();
        $row->addCell($lbl_codigo);
        $row->addCell(new TEntry ('id'));

        //$cell->style = 'width:200px';

        $row = $table->addRow();
        $row->addCell($lbl_nome);
        $row->addCell(new TEntry ('nome'));


        parent::add($table);

    }
}
















?>