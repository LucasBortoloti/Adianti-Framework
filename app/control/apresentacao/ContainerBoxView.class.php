<?php

use Adianti\Control\TWindow;

class ContainerBoxView extends TWindow{

    public function __construct(){

        parent:: __construct();
        
        $vbox = new TVBox;

                //Inicio do painel
                $painel = new TPanel(500, 300);
                $lbl_codigo = new TLabel('Código');
                $lbl_nome = new TLabel('Nome');
                $lbl_codigo->setFontSize(14);
                $lbl_nome->setFontSize(14);
                $lbl_codigo->setFontColor('blue');
        
                $painel->put($lbl_codigo, 40, 40);
                $painel->put($lbl_nome, 40, 150);
        
                $painel->put ( new TEntry('id'), 120, 40);
                $painel->put ( new TEntry('nome'), 120, 150);
        
                //Inicio da tabela
                $table = new TTable;
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


                $vbox->add($painel);
                $vbox->add($table);


                 parent::add($vbox);

    }
}

?>