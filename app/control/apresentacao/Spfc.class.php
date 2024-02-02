<?php

use Adianti\Control\TPage;

class Spfc extends TPage
{
    public function __construct()
    {
        parent::__construct();

        //$this->setTargetContainer('adianti_right_panel');
        $panel = new TPanelGroup('<b>Maior do Brasil: São Paulo FC</b>');
        $panel->style = 'width:75%';

        $image6 = new TImage('app/images/brasileirao.png');
        $image6->style = 'width: 10%;';
        $image6->style = 'height: 110px;';

        $image7 = new TImage('app/images/libertadores.png');
        $image7->style = 'width: 10%;';
        $image7->style = 'height: 180px;';

        $image8 = new TImage('app/images/mundial.png');
        $image8->style = 'width: 10%;';
        $image8->style = 'height: 125px;';

        $image9 = new TImage('app/images/paulista.png');
        $image9->style = 'width: 10%;';
        $image9->style = 'height: 114px;';

        $image10 = new TImage('app/images/sulamericana.png');
        $image10->style = 'width: 10%;';
        $image10->style = 'height: 180px;';

        $image23 = new TImage('app/images/cpdb.png');
        $image23->style = 'width: 10%;';
        $image23->style = 'height: 190px;';

        $image11 = new TImage('app/images/2023.png');
        $image11->style = 'width: 10%;';
        $image11->style = 'height: 300px;';

        $table = new TTable;
        $table->border = 0;
        $table->style = 'border-collapse:collapse';
        $table->width = '100%';
        $table->addRowSet('ㅤ', '', '', 'ㅤ');
        $table->addRowSet('<b>Títulos:</b>', '<b>Brasileirão Série A</b>', '|<b> 6 Títulos</b>', $image6);
        $table->addRowSet('', '<b>Libertadores', '| <b>3 Títulos</b>', $image7);
        $table->addRowSet('', '<b>Mundiais', '| <b>3 Títulos</b>', $image8);
        $table->addRowSet('', '<b>Campeonato Paulista', '| <b>22 Títulos</b>', $image9);
        $table->addRowSet('', '<b>Copa Sul-Americana', '| <b>1 Título</b>', $image10);
        $table->addRowSet('', '<b>Copa do Brasil', '| <b>1 Título</b>', $image23);
        $table->addRowSet('', '<b>Último título:ㅤㅤㅤㅤㅤㅤㅤCopa do Brasil 2023</b>', $image11);

        $image = new TImage('app/images/capa.png');
        $image->style = 'width: 75%;';
        $image->style = 'height: 435px;';

        $panel->add($image);
        $panel->add($table);

        $link = new THyperLink('<b>Instagram</b>', 'https://www.instagram.com/saopaulofc/', '#DC143C', 12, 'Italic');
        $link2 = new THyperLink('<b>Youtube</b>', 'https://www.youtube.com/@saopaulofc', '#000000', 12, 'Italic');

        $object = new TElement('iframe');
        $object->width       = '75%';
        $object->height      = '655px';
        $object->src         = '//www.youtube.com/embed/T_HYY9jQnF4';
        $object->frameborder = '0';
        $object->allow       = 'accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture';

        parent::add($panel);

        parent::add($object);

        $panel = new TPanelGroup('');
        $panel->style = 'width:75%';

        $table = new TTable;
        $table->border = 0;
        $table->style = 'border-collapse:collapse';
        $table->width = '100%';
        $table->addRowSet('<b>Escalação Atual:</b>');

        $image7 = new TImage('app/images/rafael.png');
        $image7->style = 'width: 10%;';
        $image7->style = 'height: 120px;';

        $image8 = new TImage('app/images/arboleda.png');
        $image8->style = 'width: 10%;';
        $image8->style = 'height: 120px;';

        $image9 = new TImage('app/images/beraldo.png');
        $image9->style = 'width: 10%;';
        $image9->style = 'height: 120px;';

        $image10 = new TImage('app/images/caio.png');
        $image10->style = 'width: 10%;';
        $image10->style = 'height: 120px;';

        $image11 = new TImage('app/images/rafinha.png');
        $image11->style = 'width: 10%;';
        $image11->style = 'height: 123px;';

        $image12 = new TImage('app/images/alisson.png');
        $image12->style = 'width: 10%;';
        $image12->style = 'height: 120px;';

        $image13 = new TImage('app/images/pablomaia.png');
        $image13->style = 'width: 10%;';
        $image13->style = 'height: 119px;';

        $image14 = new TImage('app/images/nestor.png');
        $image14->style = 'width: 10%;';
        $image14->style = 'height: 127px;';

        $image15 = new TImage('app/images/lucas.png');
        $image15->style = 'width: 10%;';
        $image15->style = 'height: 125px;';

        $image16 = new TImage('app/images/rato.png');
        $image16->style = 'width: 10%;';
        $image16->style = 'height: 120px;';

        $image17 = new TImage('app/images/calleri.png');
        $image17->style = 'width: 10%;';
        $image17->style = 'height: 120px;';

        $image18 = new TImage('app/images/dorivrau.png');
        $image18->style = 'width: 10%;';
        $image18->style = 'height: 145px;';

        $table->addRowSet('', '<b>Goleiro: Rafael</b>', $image7);
        $table->addRowSet('ㅤ', '', '', '', '');
        $table->addRowSet('', '<b>Zagueiros: Arboleda e Beraldo</b>', $image8, $image9);
        $table->addRowSet('ㅤ', '', '', '', '');
        $table->addRowSet('', '<b>Laterais: Caio Paulista e Rafinha</b>', $image10, $image11);
        $table->addRowSet('ㅤ', '', '', '', '');
        $table->addRowSet('', '<b>Volantes: Alisson e Pablo Maia </b>', $image12, $image13);
        $table->addRowSet('ㅤ', '', '', '', '');
        $table->addRowSet('', '<b>Pontas/Meias: Rodrigo Nestor e Wellington Rato</b>', $image14, $image16);
        $table->addRowSet('ㅤ', '', '', '', '');
        $table->addRowSet('', '<b>Atacantes: Lucas e Calleri</b>', $image15, $image17);
        $table->addRowSet('ㅤ', '', '', '', '');
        $table->addRowSet('', '<b>Técnico: Dorival Jr</b>', $image18);
        $table->addRowSet('ㅤ', '', '', '', '');
        $table->addRowSet('ㅤ', '', '', '', '');
        $table->addRowSet('ㅤ', '', '', '', '');

        $image2 = new TImage('app/images/logo.png');
        $image2->style = 'width: 10%;';
        $image2->style = 'height: 50px;';

        $image3 = new TImage('app/images/log.png');
        $image3->style = 'width: 10%;';
        $image3->style = 'height: 100px;';

        $image4 = new TImage('app/images/corint.png');
        $image4->style = 'width: 10%;';
        $image4->style = 'height: 110px;';

        $image5 = new TImage('app/images/contra.png');
        $image5->style = 'width: 10%;';
        $image5->style = 'height: 42px;';

        $imagefooter = new TImage('app/images/sao.png');
        $imagefooter->style = 'width: 10%;';
        $imagefooter->style = 'height: 70px;';

        $table->addRowSet('', '', '<b>Próximo jogo:</b>', $image3, $image5, $image4);
        $table->addRowSet('ㅤ', '', '', '', '');
        $table->addRowSet('', '', '', '<b>ㅤMorumbi</b>', '<b>16/08ㅤ</b>', '<b>ㅤㅤ19:30h</b>');
        $table->addRowSet('ㅤ', '', '', '', '');
        $table->addRowSet('ㅤ', '', '', '', '');
        $table->addRowSet('ㅤ', '', '', '', '');
        $table->addRowSet('', '', '', '', '', '', $link);
        $table->addRowSet('', '', '', '', '', '', $link2);

        $panel->add($table);

        $panel->addFooter($imagefooter);
        $panel->add('<b>São Paulo FC</b>');

        parent::add($panel);
    }

    public static function onGotoVideo($param = NULL)
    {
        $source = $param['source'];
        TScript::create("window.open('https://www.youtube.com/watch?v={$source}')");
    }
}
