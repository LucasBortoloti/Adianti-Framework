<?php

use Adianti\Control\TPage;

/**
 * @author Lucas Bortoloti <bortoloti91@gmail.com
 */
class Pdfhtml extends TPage
{
    private $form; // form

    use Adianti\Base\AdiantiStandardFormTrait; // Standard form methods

    function __construct()
    {

        parent::__construct();

        $this->html = new THtmlRenderer('app/resources/teste.html');

        try {

            $pdf = new stdClass;
            $pdf->id = '1';
            $pdf->name = 'Lucas';

            $replace = array();

            $replace['object'] = $pdf;

            //$replace['header'] = [[
            //    'name' => 'Field test',
            //    'value' => 'Field vaue'
            //]];

            $replace['accounts1'] = [
                [
                    'date' => '2023-01-19',
                    'value' => 100,
                    'details' => [
                        [
                            'product' => 'Chocolate',
                            'qty' => 10,
                            'value' => 5
                        ],
                        [
                            'product' => 'Milk',
                            'qty' => 10,
                            'value' => 10
                        ]
                    ]
                ],
                [
                    'date' => '2023-01-19',
                    'value' => 200,
                    'details' => [
                        [
                            'product' => 'Coffe',
                            'qty' => 10,
                            'value' => 10,
                        ],
                        [
                            'product' => 'Pizza',
                            'qty' => 5,
                            'value' => 20
                        ]
                    ],
                ]
            ];
            $replace['accounts2'] = [
                [
                    'date' => '2023-01-19',
                    'value' => 100,
                    'details' => [
                        [
                            'product' => 'Xbox Series X',
                            'qty' => 10,
                            'value' => 3600
                        ],
                        [
                            'product' => 'Moto Edge 30',
                            'qty' => 10,
                            'value' => 1500
                        ]
                    ]
                ],
                [
                    'date' => '2023-01-19',
                    'value' => 100,
                    'details' => [
                        [
                            'product' => 'PC',
                            'qty' => 10,
                            'value' => 5000
                        ],
                        [
                            'product' => 'Nintendo Switch OLED',
                            'qty' => 5,
                            'value' => 1999
                        ]
                    ]
                ]
            ];

            $this->html->enableSection('main', $replace);

            // wrap the page content using vertical box
            $vbox = new TVBox;
            $vbox->style = 'width: 100%';
            $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $vbox->add($this->html);
            parent::add($vbox);

            $contents = $this->html->getContents();

            $options = new \Dompdf\Options();
            $options->setChroot(getcwd());

            // converts the HTML template into PDF
            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($contents);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // write and open file
            file_put_contents('app/output/document.pdf', $dompdf->output());

            // open window to show pdf
            $window = TWindow::create(('Document HTML->PDF'), 0.8, 0.8);
            $object = new TElement('object');
            $object->data  = 'app/output/document.pdf';
            $object->type  = 'application/pdf';
            $object->style = "width: 100%; height:calc(100% - 10px)";
            $object->add('O navegador não suporta a exibição deste conteúdo, <a style="color:#007bff;" target=_newwindow href="' . $object->data . '"> clique aqui para baixar</a>...');

            $window->add($object);
            $window->show();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }
}
