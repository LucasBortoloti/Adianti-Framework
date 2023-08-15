<?php

use Adianti\Control\TPage;

class FullCalendar extends TPage
{
    private $fc;
    
    public function __construct()
    {
        parent::__construct();
        $this->fc = new TFullCalendar(date('Y-m-d'), 'month');
        $this->fc->setReloadAction(new TAction(array($this, 'getEvents')));
        $this->fc->setDayClickAction(new TAction(array('Eventos', 'onStartEdit')));
        $this->fc->setEventClickAction(new TAction(array('Eventos', 'onEdit')));
        $this->fc->setEventUpdateAction(new TAction(array('Eventos', 'onUpdateEvent')));
        
        $this->fc->setOption('businessHours', [ [ 'dow' => [ 1, 2, 3, 4, 5 ], 'start' => '08:00', 'end' => '18:00' ]]);
        //$this->fc->setTimeRange('10:00', '18:00');
        //$this->fc->disableDragging();
        //$this->fc->disableResizing();
        parent::add( $this->fc );
    }

    public static function getEvents($param=NULL)
    {
        $return = array();
        try
        {
            TTransaction::open('calendar');
            
            $events = Calendar::where('start_time', '<=', $param['end'])
                                   ->where('end_time',   '>=', $param['start'])->load();
            
            if ($events)
            {
                foreach ($events as $event)
                {
                    $event_array = $event->toArray();
                    $event_array['start'] = str_replace( ' ', 'T', $event_array['start_time']);
                    $event_array['end']   = str_replace( ' ', 'T', $event_array['end_time']);
                    
                    $popover_content = $event->render("<b>Título</b>: {title} <br> <b>Descrição</b>: {description}");
                    $event_array['title'] = TFullCalendar::renderPopover($event_array['title'], 'Evento', $popover_content);
                    
                    $return[] = $event_array;
                }
            }
            TTransaction::close();
            echo json_encode($return);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    public function onReload($param = null)
    {
        if (isset($param['view']))
        {
            $this->fc->setCurrentView($param['view']);
        }
        
        if (isset($param['date']))
        {
            $this->fc->setCurrentDate($param['date']);
        }
    }
}