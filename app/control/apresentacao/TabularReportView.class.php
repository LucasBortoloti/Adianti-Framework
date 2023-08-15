<?php

use Adianti\Control\TPage;

class TabularReportView extends TPage{
    
    private $form;

        public function __construct(){

        parent:: __construct();
        
        $this->form = new TForm('form_Customer_Report');
        $this->form->class = 'tform';

        $table = new TTable;
        $table-> width = '100%';
        $this->form->add($table);

        $nome = new TEntry('nome');
        $ano_lancamento = new TEntry('ano_lancamento');
        $desenvolvedoras_id = new TDBCombo('desenvolvedoras_id', 'jogos', 'Desenvolvedoras', 'id', 'nome');
        $output_type = new TRadioGroup('output_type');

        $options = array('html' =>'HTML', 'pdf' => 'PDF', 'rtf' => 'RTF');
        $output_type->addItems($options);
        $output_type->setValue('pdf');
        $output_type->setLayout('horizontal');

        $nome->setSize(200);
        $ano_lancamento->setSize(200);
        $desenvolvedoras_id->setSize(250);

        $row = $table->addRowSet(new TLabel('Jogos', ''));
        $row->class = 'tformtittle';

        $table->addRowSet( [ new TLabel('Nome')] , [ $nome ] );
        $table->addRowSet( [ new TLabel('Ano Lancamento')] , [ $ano_lancamento ] );
        $table->addRowSet( [ new TLabel('Desenvolvedoras')] , [ $desenvolvedoras_id ] );
        $table->addRowSet( [ new TLabel('Output')] , [ $output_type ] );
        
        $save_button=new TButton('generate');
        $save_button->setAction(new TAction(array($this, 'onGenerate')), 'Generate');
        $save_button->setImage('ico_save.png');

        $row = $table->addRowSet($save_button, '');
        $row->class = 'tformaction';

        $this->form->setFields(array($nome, $ano_lancamento, $desenvolvedoras_id, $output_type, $save_button));

        parent::add($this->form);
    }

    function onGenerate()
    {
        try
        {
            // open a transaction with database 'samples'
            TTransaction::open('jogos');
            $object = $this->form->getData();

            $reposity = new TRepository('Jogos');
            $criteria = new TCriteria;

            if($object->nome){
                $criteria->add(new TFilter('nome', 'like', "%{$object->nome}%"));
            }

            if($object->ano_lancamento){
                $criteria->add(new TFilter('ano_lancamento', 'like', "%{$object->ano_lancamento}%"));
            }

            if($object->desenvolvedoras_id){
                $criteria->add(new TFilter('desenvolvedoras_id', '=', "{$object->desenvolvedoras_id}"));
            }
            
            $customers = $reposity->load($criteria);
            $format = $object->output_type;
            
            if($customers){
                
                $widths = array(60, 150, 90, 110, 120);
                switch ($format){
                    
                    case 'html':
                        $tr = new TTableWriterHTML($widths);
                        break;
                    case 'pdf':
                        $tr = new TTableWriterPDF($widths);
                        break;
                    case 'rtf':
                        $tr = new TTableWriterRTF($widths);
                        break;
                }

                $tr->addStyle('tittle', 'Arial',  '10',  '', '#ffffff', '#407B49');
                $tr->addStyle('datap',  'Arial',  '10',  '',  '#000000', '#869FBB');
                $tr->addStyle('datai',  'Arial', '10',  '',  '#000000', '#ffffff');
                $tr->addStyle('header', 'Times', '16',  '', '#ff0000', '#FFF1B2');
                $tr->addStyle('footer', 'Times', '12',  '', '#2B2B2B', '#B5FFB4');

                $tr->addRow();
                $tr->addCell('Jogos', 'center', 'header', 5);

                $tr->addRow();
                $tr->addCell('id',      'left', 'tittle');
                $tr->addCell('Nome',      'left', 'tittle');
                $tr->addCell('Ano Lancamento',  'left', 'tittle');
                $tr->addCell('Quantidade Avaliacoes',     'left', 'tittle');
                $tr->addCell('Desenvolvedora', 'left', 'tittle');

                $colour= FALSE;
                foreach ($customers as $customer){
                    $style = $colour ? 'datap' : 'datai';

                    $tr->addRow();
                    $tr->addCell($customer->id,                    'left', $style);
                    $tr->addCell($customer->nome,                  'left', $style);
                    $tr->addCell($customer->ano_lancamento,        'left', $style);
                    $tr->addCell($customer->quantidade_avaliacoes, 'left', $style);
                    $tr->addCell($customer->desenvolvedoras->nome, 'left', $style);
                    $colour = !$colour;

                } 

            $tr->addRow();
            $tr->addCell(date('Y-m-d h:i:s'), 'center', 'footer', 5);
            $tr->save("app/output/tabular.{$format}");
            new TMessage('info', 'Relatório gerado');
        }
                else{
                    new TMessage('error', 'Não encontrou registros');
                }
                $this->form->setData($object);
                TTransaction::close();
            }
            catch(Exception $e){
                new TMessage('error', '<b>Error</b>' . $e->getMessage());
                TTransaction::rollback();
            }

        }
    }  





































    