<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends Auth_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library('form_validation');
        //$this->load->library('recaptcha');
        if (Settings_model::$db_config['recaptchav2_enabled'] == 1) {
            $this->load->library('recaptchaV2');
        }
        //$this->lang->load('recaptcha');
    }

    public function index() {

        $data = array();

        // if OAuth2 enabled
        if (Settings_model::$db_config['oauth2_enabled']) {
            // generate all active OAuth2 Providers
            $this->load->model('auth/Oauth2_model');
            $data['providers'] = $this->Oauth2_model->get_all();
        }

        $this->template->set_js('js', base_url() .'assets/js/vendor/parsley.min.js');

        $this->quick_page_setup(Settings_model::$db_config['active_theme'], 'main',  $this->lang->line('login'), 'login', 'header', 'footer', '', $data);
    }

    /**
     *
     * validate: validate login after input fields have met requirements
     *
     *
     */
    public function validate() {

        if ($this->session->userdata('login_attempts') == false) {
            $v = 0;
        }else{
            $v = $this->session->userdata('login_attempts');
        }

        // form input validation
        $this->form_validation->set_error_delimiters('<p>', '</p>');
        $this->form_validation->set_rules('username', $this->lang->line('username'), 'trim|required|max_length[16]');
        $this->form_validation->set_rules('password', $this->lang->line('password'), 'trim|required|max_length[64]');
        if ($v >= Settings_model::$db_config['login_attempts'] && Settings_model::$db_config['recaptchav2_enabled'] == true) {
            //$this->form_validation->set_rules('recaptcha_response_field', 'captcha response field', 'required|check_captcha');

            // this is the Recaptcha V2 code, above is for V1 but it's commented out, same in register view
            $this->form_validation->set_rules('g-recaptcha-response', $this->lang->line('recaptchav2_response'), 'required|check_captcha');
        }

        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('login');
        }

        $this->load->model('auth/login_model');

        // check max login attempts first
        if ($this->login_model->check_max_logins($this->input->post('username'))) {
            $this->session->set_flashdata('error', $this->lang->line('max_login_attempts_reached'));
            redirect('login');
        }

        // database work
        $data = $this->login_model->validate_login($this->input->post('username'), $this->input->post('password'));

        if ($data == "banned") { // check banned
            $this->session->set_flashdata('error', '<p>'. $this->lang->line('account_access_denied') .'</p>');
            redirect('login');
        }elseif (is_array($data)) {
            if ($data['active'] == 0) { // check active
                $this->session->set_flashdata('error', '<p>'. $this->lang->line('account_activate') .'</p>');
                redirect('login');
            }else{

                // user is fine, now load roles and set session data
                $this->permissions_roles($data['user_id']);
                // let administrators through, the other roles will be redirected when checks below match
                if (!self::check_roles(1)) {
                    if (Settings_model::$db_config['disable_all'] == 1) {
                        $this->session->set_flashdata('error', '<p>'. $this->lang->line('site_disabled') .'</p>');
                        redirect('login');
                    }elseif(Settings_model::$db_config['login_enabled'] == 0) {
                        $this->session->set_flashdata('error', '<p>'. $this->lang->line('login_disabled') .'</p>');
                        redirect('login');
                    }
                }

                // set the cookie if remember me option is set
                $this->load->helper('cookie');
                $cookie_domain = config_item('cookie_domain');
                if ($this->input->post('remember_me') && !get_cookie('unique_token') && Settings_model::$db_config['remember_me_enabled'] == true) {
                    setcookie("unique_token", $data['nonce'] . substr(uniqid(mt_rand(), true), -10) . $data['cookie_part'], time() + Settings_model::$db_config['cookie_expires'], '/', $cookie_domain, false, false);
                }

                // set session data
                $this->session->set_userdata('logged_in', true);
                $this->session->set_userdata('user_id', $data['user_id']);
                $this->session->set_userdata('username', $data['username']);
                // reset login attempts to 0
                $this->login_model->reset_login_attempts($data['username']);
                $this->session->set_userdata('login_attempts', 0);

                if ($this->input->post('previous_url') != "" && Settings_model::$db_config['previous_url_after_login'] == true) {
                    redirect(base64_decode($this->input->post('previous_url')));
                }
                redirect('membership/'. strtolower(Settings_model::$db_config['home_page']));
            }
        }else{
            $this->session->set_flashdata('error', $this->lang->line('login_incorrect'));
            $this->session->set_userdata('login_attempts', $data);
            redirect('login');
        }
    }

}
