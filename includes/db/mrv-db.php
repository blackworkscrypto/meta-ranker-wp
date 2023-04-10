<?php

class MRV_Database
{

    /**
     * Get things started
     *
     * @access  public
     * @since   1.0
     */
    public function __construct()
    {

        global $wpdb;

        $this->table_name = $wpdb->base_prefix . 'mrv_rank_logs';
        $this->primary_key = 'id';
        $this->version = '1.0';

    }
//Insert voter data
    public function insert($transactions)
    {
        if (is_array($transactions) && count($transactions) >= 1) {

            return $this->wp_insert_rows($transactions, $this->table_name, true);
        }
    }

    public function wp_insert_rows($row_arrays, $wp_table_name, $update = false, $primary_key = null)
    {
        global $wpdb;
        $wp_table_name = esc_sql($wp_table_name);
        // Setup arrays for Actual Values, and Placeholders
        $values = array();
        $place_holders = array();
        $query = "";
        $query_columns = "";

        $query .= "INSERT INTO `{$wp_table_name}` (";

        foreach ($row_arrays as $key => $value) {
            if ($query_columns) {
                $query_columns .= ", " . $key . "";
            } else {
                $query_columns .= "" . $key . "";
            }

            $values[] = $value;

            $symbol = "%s";
            if (is_numeric($value)) {
                $symbol = "%d";
            }

            if (isset($place_holders[$key])) {
                $place_holders[$key] .= ", '$symbol'";
            } else {
                $place_holders[$key] = "( '$symbol'";
            }

            $place_holders[$key] .= ")";
        }

        $query .= " $query_columns ) VALUES (";

        $query .= implode(', ', $place_holders) . ')';

        $sql = $wpdb->prepare($query, $values);
        if ($wpdb->query($sql)) {
            return true;
        } else {
            return false;
        }
    }
    //Fetch List with not sent data
    public function get_list()
    {
        global $wpdb;
        $list = $wpdb->get_results("SELECT * FROM $this->table_name WHERE `data_status`='not_sent'", ARRAY_A);

        return $list;

    }
    //Check user if alredy voted
    public function check_alredy_voted_list($list_id, $user)
    {
        global $wpdb;
        $list_id = (int) $list_id;
        $list = $wpdb->get_results("SELECT * FROM $this->table_name WHERE `list_id`=$list_id AND `user_wallet`='$user'", ARRAY_A);

        return $list;

    }
    //Check user ip to show votes after voting

    public function check_user_ip($list_id, $ip)
    {
        global $wpdb;
        $list_id = (int) $list_id;
        $list = $wpdb->get_results("SELECT * FROM $this->table_name WHERE `list_id`=$list_id AND `ip_address`='$ip'", ARRAY_A);

        return $list;

    }
//Update list after sending the data
    public function update_list()
    {
        global $wpdb;
        $list = $wpdb->get_results("UPDATE $this->table_name  SET data_status = 'sent' WHERE `data_status`='not_sent'");

        return $list;

    }
//Create Table
    public function create_table()
    {

        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        //IF NOT EXISTS - condition not required

        $sql = "CREATE TABLE IF NOT EXISTS " . $this->table_name . " (
		id bigint(20) NOT NULL AUTO_INCREMENT,
        list_name varchar(100) NOT NULL,
        list_item varchar(100) NOT NULL,
        list_id bigint(20) NOT NULL,
        up_vote bigint(20) NOT NULL,
        down_vote bigint(20) NOT NULL,
        total_vote bigint(20) NOT NULL,
        user_wallet varchar(100) NOT NULL,
        vote_type varchar(50) NOT NULL,
        ip_address varchar(100) NOT NULL,
        wallet_name varchar(50) NOT NULL,
        article_url varchar(150) NOT NULL,
        user_agent varchar(150) NOT NULL,
        data_status varchar(20) NOT NULL DEFAULT 'not_sent',
        last_updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id)
	    ) CHARACTER SET utf8 COLLATE utf8_general_ci;";

        dbDelta($sql);

        update_option($this->table_name . '_db_version', $this->version);
    }

    /**
     * Remove table linked to this database class file
     */
    public function drop_table()
    {
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS " . $this->table_name);
    }

}
