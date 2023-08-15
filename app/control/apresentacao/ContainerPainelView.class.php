<?php

use Adianti\Control\TPage;

class ContainerPainelView extends TPage{

    public function __construct(){

        parent:: __construct();
        
        $painel = new TPanel(500, 300);
        $painel->style = 'border: 1px solid black';

        $lbl_codigo = new TLabel('Código');
        $lbl_nome = new TLabel('Nome');
        $lbl_codigo->setFontSize(14);
        $lbl_nome->setFontSize(14);
        $lbl_codigo->setFontColor('red');

        $painel->put($lbl_codigo, 40, 10);
        $painel->put($lbl_nome, 40, 40);

        $painel->put ( new TEntry('id'), 120, 40);
        $painel->put ( new TEntry('nome'), 120, 90);


        parent::add($painel);
    }
}

?>