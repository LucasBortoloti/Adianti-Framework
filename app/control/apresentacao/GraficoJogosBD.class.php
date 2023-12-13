<?php

use Adianti\Control\TPage;

class GraficoJogosBD extends TPage
{
    public function __construct()
    {
        parent::__construct();

        parent::add(new TLabel('Jogos'));
        $table = new TTable;

        parent::add($table);

        $this->onGenerator();
    }

    function onGenerator()
    {
        $html = new THtmlRenderer('app/resources/google_pie_chart.html');

        TTransaction::open('jogos');
        $jogos = TTransaction::get();

        $colunas = $jogos->query('SELECT nome, quantidade_avaliacoes FROM jogos.jogos');
        $dados[] = ['Nome', 'Quantidade Avaliações'];

        foreach ($colunas as $coluna) {
            $dados[] = [$coluna[0], (float)$coluna[1]];
        }

        $div = new TElement('div');
        $div->id = 'container';
        $div->style = 'width:1500px;height:1150px';
        $div->add($html);

        $html->enableSection('main', array(
            'data' => json_encode($dados),
            'width' => '100%',
            'height' => '1000px',
            'title'  => 'Jogos e suas Avaliações'
        ));


        TTransaction::close();
        parent::add($div);
    }
}
