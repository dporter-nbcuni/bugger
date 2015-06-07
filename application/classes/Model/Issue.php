<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Issue extends Model_Abstract {

    protected $_table_name = 'issues';

    protected $_table_columns = array(
        'id' => NULL,
        'duplicate_id' => NULL,
        'status_id' => NULL,
        'type_id' => NULL,
        'project_id' => NULL,
        'priority_id' => NULL,
        'pms_id' => NULL,
        'reporter_user_id' => NULL,
        'assigned_department_id' => NULL,
        'summary' => NULL,
        'description' => NULL,
        'example_url' => NULL,
        'external_cms_url' => NULL,
        'external_cms_id' => NULL,
        'due_date' => NULL,
        'due_time' => NULL,
        'last_updated_by_user_id' => NULL,
        'created_at' => NULL,
        'updated_at' => NULL
    );

    protected $_created_column = array(
        'column' => 'created_at',
        'format' => 'Y-m-d H:i:s'
    );
    protected $_updated_column = array(
        'column' => 'updated_at',
        'format' => 'Y-m-d H:i:s'
    );

    protected $_has_many = array(
        'comments' => array('model' => 'Issue_Comment', 'foreign_key' => 'issue_id'),
        'files' => array('model' => 'Issue_File', 'foreign_key' => 'issue_id')
    );

    protected $_belongs_to = array(
        'priority' => array('model' => 'Issue_Priority', 'foreign_key' => 'priority_id'),
        'project' => array('model' => 'Project', 'foreign_key' => 'project_id'),
        'reporter' => array('model' => 'User', 'foreign_key' => 'reporter_user_id'),
        'type' => array('model' => 'Issue_Type', 'foreign_key' => 'type_id'),
        'status' => array('model' => 'Issue_Status', 'foreign_key' => 'status_id'),
        'assigned_department' => array('model' => 'Department', 'foreign_key' => 'assigned_department_id'),
        'pms' => array('model' => 'Pms', 'foreign_key' => 'pms_id'),
        'last_updated_by' => array('model' => 'User', 'foreign_key' => 'last_updated_by_user_id'),
        'duplicate' => array('model' => 'Issue', 'foreign_key' => 'duplicate_id'),
    );

    public function url($full = FALSE)
    {
        $url = '/issues/view/' . $this->id;

        if ($full)
            return APP_BASE_URL . $url;

        return $url;
    }

    /**
     * Overrides parent method.
     */
    public function save(Validation $validation = NULL)
    {
        $this->_beforeSave();

        return parent::save($validation);
    }

    private function _beforeSave(Validation $validation = NULL)
    {
        $re = "/^(?:f|ht)tps?:\\/\\//i";
      // Prepend http:// to example URLs
        if ( ! empty($this->_object['example_url']) && !preg_match($re, $this->_object['example_url'])) {
            $this->_object['example_url'] = 'http://' . $this->_object['example_url'];
        }
            // Prepend http:// to external cms URLs
        if ( ! empty($this->_object['external_cms_url']) && !preg_match($re, $this->_object['external_cms_url']) ) {
            $this->_object['external_cms_url'] = 'htdptp://' . $this->_object['external_cms_url'];
        }
    }

    public function trackingId()
    {
        return strtoupper($this->project->name) . '-' . $this->id;
    }

    /**
     * Returns an array of ALL issues or count of NON-CLOSED issues.
     *
     * @param   int  $count Whether or not return a count of requests.
     * @return  int|Model_Issue[]
     */
    public static function findAll($count = FALSE)
    {
        $requests = ORM::factory('Issue')
            ->order_by('updated_at', 'DESC');

        if ($count) {
            // ONLY count NON-CLOSED issues.
            $requests->where('status_id', '<>', Model_Issue_Status::CLOSED);
            return $requests->count_all();
        }

        return $requests->find_all();
    }

    /**
     * Returns an array of ALL issues or a count of NON-CLOSED issues
     * that have been reported by the logged in user.
     *
     * @param   int  $count Whether or not return a count of issues.
     * @return  int|Model_Issue[]
     */
    public static function findReportedByMe($count = FALSE)
    {
        $auth = Auth::instance();
        $auth_user = $auth->get_user();

        $issues = ORM::factory('Issue')
            ->order_by('updated_at', 'DESC')
            ->where('reporter_user_id', '=', $auth_user->id);

        if ($count) {
            // ONLY count open issues.
            $issues->where('status_id', '<>', Model_Issue_Status::CLOSED);
            return $issues->count_all();
        }

        return $issues->find_all();
    }

    /**
     * Logs updates in the comments table.
     *
     * @param   string      $column     The updated column name.
     * @param   int         $value      The new value.
     * @return  bool
     */
    public function logUpdate($column, $value)
    {
        switch($column) {
            case 'type_id':
            case 'status_id':
            case 'priority_id':
                $column_parts = explode('_', $column);
                $record = ORM::factory('Issue_' . ucfirst($column_parts[0]), $value);
                $comment = sprintf('Changed %s to "%s"', $column_parts[0], $record->name);
                break;

            case 'assigned_department_id':
                $department = ORM::factory('Department', $value);
                $comment = sprintf('Changed assignee to "%s"', $department->name);
                break;
        }

        if (isset($comment)) {
            ORM::factory('Issue_Comment')
                ->values( array(
                    'user_id' => Auth::instance()->get_user()->id,
                    'issue_id' => $this->id,
                    'can_edit' => FALSE,
                    'comment' => $comment
                ) )
                ->save();

            return TRUE;
        }

        return FALSE;
    }

    /**
     * Gives ticket count based on status for dashboard pie charts.
     *
     * @param  int      $reporter_user_id    A reporter user ID to filter by.
     * @return array
     */
    public static function getStatusBreakdown($reporter_user_id = NULL)
    {
        $where = array();

        // Don't take duplicate tickets into account
        $where[] = 'issues.duplicate_id < 1';

        if ($reporter_user_id) {
            $where[] = 'issues.reporter_user_id = ' . (int) $reporter_user_id;
        }

        $query = DB::query(Database::SELECT, '
            SELECT
                issue_statuses.name AS label,
                issue_statuses.name AS status_name,
                issue_statuses.color AS color,
                issues.status_id AS status_id,
                COUNT(issues.id) AS data
            FROM issues
            JOIN issue_statuses ON issues.status_id = issue_statuses.id
            WHERE ' . implode(' AND ', $where) . '
            GROUP BY issues.status_id
            HAVING data > 0
        ');

        $result = $query->execute()->as_array();

        foreach($result as &$row) {
            $row['label'] .= ',' . $row['status_id'];
        }

        return $result;
    }

    /**
     * Returns an array of ALL requests or count of NON-CLOSED requests.
     *
     * @param   int  $count Whether or not return a count of requests.
     * @return  int|Model_Issue[]
     */
    public static function findSupportRequests($count = FALSE)
    {
        $auth = Auth::instance();

        $requests = ORM::factory('Issue')
            ->order_by('updated_at', 'DESC');

        if ( ! $auth->logged_in('admin')) {
            // Admins may see ALL requests. Regular users may only see their own requests.
            $requests->where('reporter_user_id', '=', $auth->get_user()->id);
        }

        if ($count) {
            // ONLY count open issues.
            $requests->where('status_id', '<>', Model_Issue_Status::CLOSED);
            return $requests->count_all();
        }

        return $requests->find_all();
    }

    /**
     * Returns an array or count of NON-CLOSED issues.
     *
     * @param   int  $count Whether or not return a count of issues.
     * @return  int|Model_Issue[]
     */
    public static function findAssignedToMe($count = FALSE)
    {
        $auth = Auth::instance();
        $auth_user = $auth->get_user();

        $issues = ORM::factory('Issue')
            ->order_by('updated_at', 'DESC')
            ->where('status_id', '<>', Model_Issue_Status::CLOSED)
            ->where('assigned_department_id', '=', $auth_user->department_id);

        if ($count)
            return $issues->count_all();

        return $issues->find_all();
    }
}
