<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Users extends Controller_Auth_Admin {

    public function action_index()
    {
        $users = ORM::factory('User')->find_all();
        $this->template->content = View::factory('users/index')->set('users', $users);
    }

    public function action_profile()
    {
        $user = Auth::instance()->get_user();

        if ($post = $this->request->post()) {
            $user->values($post)->save();
            $this->session->flashSuccess('generic.update_ok');
        }

        $this->template->content = View::factory('users/profile')
            ->set('user', Auth::instance()->get_user());
    }

    public function action_change_password()
    {
        $post = $this->request->post();

        if ( ! empty($post['password_current']) && ! empty($post['password_new'])) {
            $auth = Auth::instance();
            $user = $auth->get_user();

            if ($user->password != $auth->hash($post['password_current'])) {
                $this->session->flashError('The current password is incorrect.');
            }
            elseif (strlen($post['password_new']) < 6) {
                $this->session->flashError('The current password is too short. It should have 6 characters or more.');
            }
            else {
                if ($post['password_current'] != $post['password_new']) {
                    $user->set('password', $post['password_new'])->save();
                }

                $this->session->flashSuccess('Password changed successfully.');
            }
        }

        $this->redirect('/users/profile');
    }

    public function action_add()
    {
        $user = ORM::factory('User');

        if ($post = $this->request->post()) {
            try {
                if (Model_User::exists('email', $post['user']['email']))
                    throw new Form_Validation_Exception(Messages::get('user.email_taken'));

                $post['user']['name'] = ucwords(strtolower($post['user']['name']));
                $post['user']['email'] = strtolower($post['user']['email']);

                if( empty($post['user']['username'])) {
                  $post['user']['username'] = strtolower($post['user']['email']);
                }

                // Create user
                $user->values($post['user'])->save();

                // Assign roles
                if (isset($post['roles'])) {
                    $user->attachRoles($post['roles']);
                }

                $this->session->flashSuccess('generic.create_ok');
                $this->redirect('users');
            }
            catch(Form_Validation_Exception $e) {
                $this->session->flashError($e->getMessage());
                // Repopulate form
                $user->values($post['user']);
            }
        }

        $this->template->content = View::factory('users/form')
            ->set('user', $user);
    }

    public function action_edit()
    {
        $id = $this->request->param('id');
        $user = ORM::factory('User', $id);

        if ( ! $user->loaded())
            $this->redirect('users');

        if ($post = $this->request->post()) {
            try {
                if ($user->email != $post['user']['email'] && Model_User::exists('email', $post['user']['email']))
                    throw new Form_Validation_Exception(Messages::get('user.email_taken'));

                // NEVER save blank passwords
                if (empty($post['user']['password'])) {
                    unset($post['user']['password']);
                }

                $post['user']['email'] = strtolower($post['user']['email']);
                $post['user']['name'] = ucwords(strtolower($post['user']['name']));

                // Save user
                $user->values($post['user'])->save();

                // Assign roles
                $user->clearRoles();
                if (isset($post['roles'])) {
                    $user->attachRoles($post['roles']);
                }

                $this->session->flashSuccess('generic.update_ok');
                $this->redirect('users');
            }
            catch(Form_Validation_Exception $e) {
                $this->session->flashError($e->getMessage());
                // Repopulate form
                $user->values($post['user']);
            }
        }

        $this->template->content = View::factory('users/form')
            ->set('user', $user);
    }
}
