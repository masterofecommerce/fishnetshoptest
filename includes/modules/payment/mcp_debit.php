<?php
$method_class_file = dirname(__FILE__).'/../../external/micropayment/class.micropayment_method.php';
if (file_exists($method_class_file)) {
    require_once($method_class_file);
} else {
    echo '<font color="red">Micropayment&trade; Modul ist nicht vollst&auml;ndig installiert!</font>';
    return false;
}

class mcp_debit extends micropayment_method
{
    var $version = '1.0';
    var $code = 'mcp_debit';
    var $url = '/lastschrift/event';

    function mcp_debit()
    {
        global $order, $language;
        $this->title       = MODULE_PAYMENT_MCP_DEBIT_TEXT_TITLE;
        $this->title_extern = MODULE_PAYMENT_MCP_DEBIT_TEXT_TITLE_EXTERN;
        $this->description = MODULE_PAYMENT_MCP_DEBIT_TEXT_DESCRIPTION;
        $this->sort_order  = MODULE_PAYMENT_MCP_DEBIT_SORT_ORDER;
        $this->info        = MODULE_PAYMENT_MCP_DEBIT_TEXT_INFO;
        $this->url         = '/lastschrift/event/';
        parent::micropayment_method();
    }

    function install()
    {
        if(!$this->check_is_service_installed()) {
            parent::install();
        }

        $lastStatusArray = xtc_db_query('SELECT MAX(`orders_status_id`) last_id FROM ' . TABLE_ORDERS_STATUS);
        $t = xtc_db_fetch_array($lastStatusArray);
        $lastStatusId = $t['last_id'] + 1;
        xtc_db_query("INSERT INTO " . TABLE_ORDERS_STATUS . " (`orders_status_id`,`language_id`,`orders_status_name`) VALUES ('" . $lastStatusId . "',1,'Cancelled micropayment debit')");
        xtc_db_query("INSERT INTO " . TABLE_ORDERS_STATUS . " (`orders_status_id`,`language_id`,`orders_status_name`) VALUES ('" . $lastStatusId . "',2,'Storniert micropayment debit')");
        
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " ( `configuration_key`, `configuration_value`, `configuration_group_id`, `sort_order`, `set_function`, `date_added`) VALUES ('MODULE_PAYMENT_MCP_DEBIT_STATUS', 'false', '6', '1', 'xtc_cfg_select_option(array(\'true\', \'false\'), ', NOW())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " ( `configuration_key`, `configuration_value`, `configuration_group_id`, `sort_order`, `date_added`) VALUES ('MODULE_PAYMENT_MCP_DEBIT_MINIMUM_AMOUNT', '0', '6', '0', NOW())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " ( `configuration_key`, `configuration_value`, `configuration_group_id`, `sort_order`, `date_added`) VALUES ('MODULE_PAYMENT_MCP_DEBIT_MAXIMUM_AMOUNT', '500', '6', '0', NOW())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " ( `configuration_key`, `configuration_value`, `configuration_group_id`, `sort_order`, `date_added`) VALUES ('MODULE_PAYMENT_MCP_DEBIT_SORT_ORDER', '2', '6', '0', NOW())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " ( `configuration_key`, `configuration_value`, `configuration_group_id`, `sort_order`, `date_added`) values ('MODULE_PAYMENT_MCP_DEBIT_ALLOWED', '', '6', '0', NOW())");
    }

    function keys() {
        $array = array(
            'MODULE_PAYMENT_MCP_DEBIT_STATUS',
            'MODULE_PAYMENT_MCP_DEBIT_MINIMUM_AMOUNT',
            'MODULE_PAYMENT_MCP_DEBIT_MAXIMUM_AMOUNT',
            'MODULE_PAYMENT_MCP_DEBIT_SORT_ORDER',
            'MODULE_PAYMENT_MCP_DEBIT_ALLOWED'
        );
        $array = array_merge($array,parent::keys());
        return $array;
    }

    function remove()
    {
        xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE `configuration_key` LIKE 'MODULE_PAYMENT_MCP_DEBIT_%'");
        xtc_db_query("DELETE FROM " . TABLE_ORDERS_STATUS . " WHERE orders_status_name = 'Cancelled micropayment debit' ");
        xtc_db_query("DELETE FROM " . TABLE_ORDERS_STATUS . " WHERE orders_status_name = 'Storniert micropayment debit' ");
        parent::remove();
    }
}
?>