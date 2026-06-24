<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity_logs extends MY_Controller
{
    public function index()
    {
        $this->render('activity_logs/index', array('title' => 'Activity Logs', 'rows' => $this->App_model->all('activity_logs', 'id DESC', 500)));
    }
}
