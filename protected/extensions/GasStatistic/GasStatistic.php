<?php
Yii::import('zii.widgets.CPortlet');
class GasStatistic extends CPortlet
{
    public $data;

    public function init()
    {
        parent::init();
        //$this->fullname = 'x';
    }

    public function renderContent()
    {
        $this->render('view',array('data'=>$this->data));
    }
    
}