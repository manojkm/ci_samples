<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Activate_account_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     *
     * activate_member
     *
     * @param string $email e-mail address of member
     * @param string $nonce the member nonce
     * @return boolean
     *
     */

    public function activate_member($email, $nonce) {
        $this->db->select('email, nonce, active, banned, date_registered,
                           unix_timestamp(NOW()) - unix_timestamp(last_login) < '. Settings_model::$db_config['activation_link_expires'].' AS timediff')
            ->from(DB_PREFIX .'users')
            ->where(array('email' => $email, 'nonce' => $nonce));

        $q = $this->db->get();

        if ($q->num_rows() == 0) { // if no match account doest exist
            return "nomatch";
        }elseif($q->num_rows() == 1) { // if match then check for banned and active account

            $row = $q->row();

            // is account banned?
            if ($row->banned == 1) {
                return "banned";
            }

            // is account active?
            if ($row->active == 1) {
                return "active";
            }

            // so if it is active is the timestamp still valid?
            if ($row->timediff != 1) {
                // timestamp expired!
                return "expired";
            }else{
                // timestamp is ok -> everything is ok to activate account
                $data = array('active' => true);
                $this->db->update(DB_PREFIX .'users', $data);
                if($this->db->affected_rows() == 1) {
                    return "validated";
                }
            }
        }

        return false;
    }
}
