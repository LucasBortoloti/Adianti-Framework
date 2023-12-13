<?php

use Adianti\Control\TPage;

class GraficoColunaBD extends TPage
{
    public function __construct()
    {
        parent::__construct();
        
        parent::add(new TLabel('Preço dos Produtos'));
        $table = new TTable;
                        
        parent::add($table);

        $this->onGenerator();
    }

    function onGenerator()
    {
        $html = new THtmlRenderer('app/resources/google_bar_chart.html');
        
        TTransaction::open('sale');
        $sale = TTransaction::get();
        
        $colunas = $sale->query('SELECT nome, preco FROM sale.product');
        $dados[] = ['Nome','Preço'];
        
        foreach($colunas as $coluna)
        {
               $dados[] = [$coluna[0],(float)$coluna[1]];
        }

        $div = new TElement('div');
        $div->id = 'container';
        $div->style = 'width:1200px;height:1150px';
        $div->add($html);
        
        $html->enableSection('main', array('data' => json_encode($dados),
                                           'width' => '110%',
                                           'height' => '1100px',
                                           'title'  => 'Produtos',
                                           'ytitle' => 'Produtos', 
                                           'xtitle' => 'Preço',
                                           'uniqid' => uniqid()));
        
        TTransaction::close();
        parent::add($div);
    }

}
