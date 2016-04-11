<?php
class LoginController extends Controller {

    private $user_id;
    private $username;
    private $permission = array();

    public function isLogged() {
        if( isset($this->session->data['user_expired'])){
            $mUser = $this->model("User");
            $user = $mUser::where('enable', '=', 1)
                ->where('id', '=', $this->session->data['user_expired'])
                ->get();
            if( $user ) {
                $this->session->data['user_expired'] = $user->login_name;
                return true;
            } else {
                $this->logout();
                return false;
            }
        }
        return false;
    }

	public function login() {
        $request = $this->request->request;
        $username = $request['login-name'];
        $password = $request['login-pass'];

		$mUser = $this->model("User");
        $user = $mUser::where('enable', '=', 1)
            ->where('login_name', '=', $username)
            ->where('password', '=', $password)
            ->first();

        if( $user ) {
            $this->session->data['user_expired'] = $user->id;

            $this->user_id = $user->id;
            $this->username = $user->login_name;
            //$this->user_group_id = $user;

            return true;
        } else {
            return false;
        }
	}

    public function logout() {
        unset($this->session->data['user_expired']);

        $this->user_id = '';
        $this->username = '';
    }
}
