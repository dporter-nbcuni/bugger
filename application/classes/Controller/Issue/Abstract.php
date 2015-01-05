<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller_Issue_Abstract extends Controller_Base {

    protected $_config = array(
        'base_url' => '',
        'model' => array(
            'name'          => '', 
            'title'         => '', 
            'title_plural'  => ''
        )
    );

    public function action_index()
    {
        $records = ORM::factory($this->_config['model']['name'])->find_all();
        $this->template->content = View::factory('issue_abstract/index')
            ->set('records', $records)
            ->set('config', $this->_config);
    }

    public function action_add()
    {
        $record = ORM::factory($this->_config['model']['name']);

        if ($post = $this->request->post()) {
            $record->values($post)->save();
            $this->redirect($this->_config['base_url']);
        }

        $this->template->content = View::factory('issue_abstract/form')
            ->set('record', $record)
            ->set('config', $this->_config);
    }

    public function action_edit()
    {
        $id = $this->request->param('id');

        $record = ORM::factory($this->_config['model']['name'], $id);

        if ( ! $record->loaded()) 
            $this->redirect($this->_config['base_url']);
        
        if ($post = $this->request->post()) {
            $record->values($post)->save();
            $this->redirect($this->_config['base_url']);
        }

        $this->template->content = View::factory('issue_abstract/form')
            ->set('record', $record)
            ->set('config', $this->_config);
    }

    public function action_delete()
    {
        $id = $this->request->param('id');

        $record = ORM::factory($this->_config['model']['title'], $id);

        if ($record->loaded()) {
            $record->delete();
        }

        $this->redirect($this->_config['base_url']);
    }
}