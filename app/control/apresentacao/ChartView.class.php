<?php

use Adianti\Control\TPage;

class ChartView extends TPage{

    public function __construct(){

        parent:: __construct();

        $data['Sul'] = array(240, 302, 108, 100, 50, 134);
        $data['Sudeste'] = array(260, 189, 122, 237, 60, 138);
        $data['Norte'] = array(146, 260, 170, 89, 60, 175);

        $chart = new TBarChart( new TPChartDesigner);

        $chart->setTitle('Chuvas');
        $chart->setSize( 600, 400);
        $chart->setXLabels( array('Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'));
        $chart->setYLabels('Indice de chuvas');
        $chart->setOutputPath('app/output/chuvas.png');

        $chart->addData('Sul', $data['Sul']);
        $chart->addData('Sudeste', $data['Sudeste']);
        $chart->addData('Norte', $data['Norte']);
        $chart->generete();

        parent::add( new TImage('app/output/chuvas.png'));


    }

}


?>