<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Issues extends Controller_Auth_User {
    /**
     * Displays ALL issues.
     */
    public function action_index()
    {
        $issues = Model_Issue::findAll();

        $this->template->content = $view = View::factory('issues/index');

        $view->title = 'All Tickets';
        $view->subtitle = 'View and Manage All Tickets';

        $view->statuses = ORM::factory('Issue_Status')->find_all();
        $view->priorities = ORM::factory('Issue_Priority')->find_all();
        $view->types = ORM::factory('Issue_Type')->find_all();
        $view->projects = ORM::factory('Project')->find_all();
    }

    /**
     * Displays ALL issues REPORTED by ME.
     */
    public function action_pending()
    {
        $this->action_index();

        $this->template->content->title = 'Pending Tickets';
        $this->template->content->subtitle = 'View and Manage Pending Tickets';
    }

    /**
     * Displays ALL issues REPORTED by ME.
     */
    public function action_reported_by_me()
    {
        $this->action_index();

        $this->template->content->title = 'Reported by Me';
        $this->template->content->subtitle = 'View and Manage Tickets Reported by You';
    }

    /**
     * Returns a filtered table of issues.
     *
     * @uses    ajax
     * @return  html
     */
    public function action_filter()
    {
        $this->auto_render = TRUE;
        $valid_filters = array('priority_id', 'project_id', 'type_id', 'status_id', 'reporter_user_id');
        $input = $this->request->query();

        $issues = ORM::factory('Issue');

        foreach($input as $filter => $values) {
            if (in_array($filter, $valid_filters)) {
                $issues->where($filter, 'IN', $values);
            }
        }

        $issues = $issues->find_all();

        $this->template = View::factory('issues/_index_table')
            ->set('issues', $issues);
    }

    public function action_view()
    {
        $id = $this->request->param('id');
        $issue = ORM::factory('Issue', $id);

        if ( ! $issue->loaded()) {
            $this->session->flashError('generic.read_fail');
            $this->redirect('issues');
        }

        $comments = Model_Issue_Comment::findByIssueId($issue->id, 0, 5);

        $this->template->content = View::factory('issues/view')
            ->set('issue', $issue)
            ->set('comments', $comments);
    }

    /**
     * Creates a NEW issue.
     */
    public function action_add()
    {
        $issue = ORM::factory('Issue');

        if ($post = $this->request->post()) {
            $post['issue']['status_id'] = Model_Issue_Status::OPEN;
            $post['issue']['reporter_user_id'] = $this->auth_user->id;
            $post['issue']['due_date'] = empty($post['issue']['due_date']) ? NULL : date('Y-m-d', strtotime($post['issue']['due_date']));
            $post['issue']['due_time'] = empty($post['issue']['due_time']) ? NULL : date('H:i:s', strtotime($post['issue']['due_time']));

            // Assign support requests to DEV
            if ($post['issue']['type_id'] == Model_Issue_Type::SUPPORT_REQUEST) {
                $post['issue']['assigned_department_id'] == Model_Department::DEV;
            }

            // Create issue
            $issue->values($post['issue'])->save();

            try {
                // Proccess uploads
                Model_Issue_File::processTempUpload($post['attachment_temp_dir'], $issue->id, $this->auth_user->id);
            }
            catch(Exception $ex) {
                $this->log->add(Log::ERROR, $ex->getMessage());
                return $this->session->flashError('Error: ' . $ex->getMessage());
            }

            // Notify users
            Mailer_Issue::factory($issue)->sendCreated();

            $this->redirect('issues/view/' . $issue->id);
        }

        $this->template->content = View::factory('issues/form')
            ->set('issue', $issue)
            ->set('attachment_temp_dir', time());
    }

    /**
     * @uses    ajax
     * @return  json
     */
    public function action_update_editable_field()
    {
        if ($post = $this->request->post()) {
            $issue = ORM::factory('Issue', $post['pk']);

            if ( ! $issue->loaded())
                return $this->response->notFound('Invalid ticket ID');

            $column = trim($post['name']);
            $value = $post['value'];

            try {
                // Update issue
                $issue->last_updated_by_user_id = $this->auth_user->id;
                $issue->$column = $value;
                $issue->save();

                // Log update
                $issue->logUpdate($column, $value);

                // Notify users of status changes
                if ($column == 'status_id') {
                   Mailer_Issue::factory($issue)->sendStatusUpdated();
                }
            }
            catch(Exception $e) {
                echo $e->getMessage();
                return $this->response->badRequest("The field $column does not exist");
            }
        }
        else {
            $this->response->badRequest();
        }
    }

    /**
     * Returns the options for the issue status dropdown in the issue page.
     *
     * @uses    ajax
     * @return  json
     */
    public function action_status_options()
    {
        $id = $this->request->param('id');
        $issue = ORM::factory('Issue', $id);

        if ( ! $issue->loaded())
            return $this->response->notFound('Invalid ticket ID');

        $json = array();

        if ($issue->status_id == Model_Issue_Status::CLOSED) {
            $statuses = ORM::factory('Issue_Status')
                ->where('id', 'IN', array(Model_Issue_Status::CLOSED, Model_Issue_Status::REOPENED))
                ->find_all();
        }
        else if ($issue->status_id == Model_Issue_Status::RESOLVED) {
            $statuses = ORM::factory('Issue_Status')
                ->where('id', 'IN', array(Model_Issue_Status::CLOSED, Model_Issue_Status::REOPENED, Model_Issue_Status::RESOLVED))
                ->find_all();
        }
        else if ($issue->status_id == Model_Issue_Status::REOPENED) {
            $statuses = ORM::factory('Issue_Status')
                ->where('id', '<>', Model_Issue_Status::OPEN)
                ->find_all();
        }
        else {
            $statuses = ORM::factory('Issue_Status')
                ->where('id', '<>', Model_Issue_Status::REOPENED)
                ->find_all();
        }

        foreach($statuses as $status) {
            $json[$status->id] = $status->name;
        }

        $this->response->json($json);
    }

    /**
     * Returns the options for the issue type dropdown in the issue page.
     *
     * @uses    ajax
     * @return  json
     */
    public function action_type_options()
    {
        $json = array();
        $records = ORM::factory('Issue_Type')->find_all();

        foreach($records as $record) {
            $json[$record->id] = $record->name;
        }

        $this->response->json($json);
    }

    /**
     * Returns the options for the issue type dropdown in the issue page.
     *
     * @uses    ajax
     * @return  json
     */
    public function action_type_pms()
    {
        $json = array();
        $records = ORM::factory('Pms')->find_all();

        foreach($records as $record) {
            $json[$record->id] = $record->name;
        }

        $this->response->json($json);
    }

    /**
     * Returns the options for the issue priority dropdown in the issue page.
     *
     * @uses    ajax
     * @return  json
     */
    public function action_priority_options()
    {
        $json = array();
        $records = ORM::factory('Issue_Priority')->find_all();

        foreach($records as $record) {
            $json[$record->id] = $record->name;
        }

        $this->response->json($json);
    }

    /**
     * Returns the options for duplicate dropdown in the issue page.
     *
     * @uses    ajax
     * @return  json
     */
    public function action_duplicate_options()
    {
        $id = $this->request->param('id');
        $json = array();

        // Exclude id from the choices
        $records = ORM::factory('Issue')
            ->select('id')
            ->where('id', '<>', $id)
            ->where('duplicate_id', '<', 1)
            ->order_by('id', 'DESC')
            ->find_all();

        $json[0] = 'No';
        foreach($records as $record) {
            $json[$record->id] = $record->trackingId();
        }

        $this->response->json($json);
    }
}
