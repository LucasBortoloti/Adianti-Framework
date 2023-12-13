<?php

use Adianti\Control\TPage;

class GraficoColuna extends TPage
{
    function __construct( $show_breadcrumb = true )
    {
        parent::__construct();
        
        $html = new THtmlRenderer('app/resources/google_bar_chart.html');
        $data = array();
        $data[] = [ 'Mês',  'Xbox', 'Playstation', 'Nintendo' ];
        $data[] = [ 'Janeiro',    50,       80,       90 ];
        $data[] = [ 'Fevereiro',  60,       100,      75 ];
        $data[] = [ 'Março',      54,       82,      100 ];
        
        // replace the main section variables
        $html->enableSection('main', array('data'   => json_encode($data),
                                           'width'  => '100%',
                                           'height'  =>'300px',
                                           'title'  => 'Vendas',
                                           'ytitle' => 'Vendas', 
                                           'xtitle' => 'Mês',
                                           'uniqid' => uniqid()));
        
        // add the template to the page
        $container = new TVBox;
        $container->style = 'width: 100%';
        if ($show_breadcrumb)
        {
            $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        }
        $container->add($html);
        parent::add($container);
    }
}
