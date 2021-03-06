<?php

/* --------------------------------------------------------------
   $Id: campaigns.php 1117 2005-07-25 21:02:11Z mz $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2005 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce coding standards; www.oscommerce.com

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

require ('includes/application_top.php');

  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

switch ($_GET['action']) {
	case 'insert' :
	case 'save' :
            $error = array();
		$campaigns_id = xtc_db_prepare_input($_GET['cID']);
		$campaigns_name = xtc_db_prepare_input($_POST['campaigns_name']);
		$campaigns_refID = xtc_db_prepare_input($_POST['campaigns_refID']);
		$sql_data_array = array ('campaigns_name' => $campaigns_name, 'campaigns_refID' => $campaigns_refID);
                $url_action = 'new';
                if ($_GET['action'] == 'insert') {
                    $check_if_name_exist = xtc_db_find_database_field(TABLE_CAMPAIGNS, 'campaigns_name', $campaigns_name, 'campaigns_name');
                } elseif ($_GET['action'] == 'save') {
                    $url_action = 'edit';
                    $check_if_name_exist = xtc_db_find_database_field(TABLE_CAMPAIGNS, 'campaigns_name', $campaigns_name);
                }
                
                if(!$campaigns_name || $check_if_name_exist){
                    if($_GET['action'] == 'save'){
                        if($check_if_name_exist['campaigns_id'] != $campaigns_id){
                            $error[] = ERROR_TEXT_NAME;
                        }
                    } else {
                        $error[] = ERROR_TEXT_NAME;
                    }
                }

                if(empty($error)){
		if ($_GET['action'] == 'insert') {
                        
			$insert_sql_data = array ('date_added' => 'now()');
			$sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
			xtc_db_perform(TABLE_CAMPAIGNS, $sql_data_array);
			$campaigns_id = xtc_db_insert_id();
		}
		elseif ($_GET['action'] == 'save') {
			$update_sql_data = array ('last_modified' => 'now()');
			$sql_data_array = xtc_array_merge($sql_data_array, $update_sql_data);
			xtc_db_perform(TABLE_CAMPAIGNS, $sql_data_array, 'update', "campaigns_id = '".xtc_db_input($campaigns_id)."'");
		}

		xtc_redirect(xtc_href_link(FILENAME_CAMPAIGNS, 'page='.$_GET['page'].'&cID='.$campaigns_id));
                } else {
                            $_SESSION['repopulate_form'] = $_REQUEST;
                            $_SESSION['errors'] = $error;
                            xtc_redirect(xtc_href_link(FILENAME_CAMPAIGNS, 'page='.$_GET['page'].'&cID='.$campaigns_id.'&action='.$url_action.'&errors=1'));
                }
		break;

	case 'deleteconfirm' :

		$campaigns_id = xtc_db_prepare_input($_GET['cID']);

		xtc_db_query("delete from ".TABLE_CAMPAIGNS." where campaigns_id = '".xtc_db_input($campaigns_id)."'");
		xtc_db_query("delete from ".TABLE_CAMPAIGNS_IP." where campaign = '".xtc_db_input($campaigns_id)."'");

		if ($_POST['delete_refferers'] == 'on') {

			xtc_db_query("update ".TABLE_ORDERS." set refferers_id = '' where refferers_id = '".xtc_db_input($campaigns_id)."'");
			xtc_db_query("update ".TABLE_CUSTOMERS." set refferers_id = '' where refferers_id = '".xtc_db_input($campaigns_id)."'");
		}

		xtc_redirect(xtc_href_link(FILENAME_CAMPAIGNS, 'page='.$_GET['page']));
		break;
}
require (DIR_WS_INCLUDES.'head.php');
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<div class='row'>
    
<!-- body_text //-->
        <!-- body_text //-->
        <div class='col-xs-12'>
            <p class="h2">
                <?php echo HEADING_TITLE; ?>
            </p>
        </div>
        <?php include DIR_WS_INCLUDES.FILENAME_ERROR_DISPLAY; ?>
        <div class='col-xs-12'> <br> </div>
        <div class='col-xs-12'>
        <div id='responsive_table' class='table-responsive pull-left col-sm-12'>
        <table class='table table-bordered table-striped'>
              <thead class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CAMPAIGNS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </thead>
<?php

$campaigns_query_raw = "select * from ".TABLE_CAMPAIGNS." order by campaigns_name";
$campaigns_split = new splitPageResults($_GET['page'], '20', $campaigns_query_raw, $campaigns_query_numrows);
$campaigns_query = xtc_db_query($campaigns_query_raw);
while ($campaigns = xtc_db_fetch_array($campaigns_query)) {
	if (((!$_GET['cID']) || (@ $_GET['cID'] == $campaigns['campaigns_id'])) && (!$cInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
		$cInfo = new objectInfo($campaigns);
	}

	if ((is_object($cInfo)) && ($campaigns['campaigns_id'] == $cInfo->campaigns_id)) {
		echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'pointer\'" onclick="document.location.href=\''.xtc_href_link(FILENAME_CAMPAIGNS, 'page='.$_GET['page'].'&cID='.$campaigns['campaigns_id'].'&action=edit').'\'">'."\n";
	} else {
		echo '              <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'pointer\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\''.xtc_href_link(FILENAME_CAMPAIGNS, 'page='.$_GET['page'].'&cID='.$campaigns['campaigns_id']).'\'">'."\n";
	}
?>
                <td class="dataTableContent"><?php echo $campaigns['campaigns_name']; ?></td>
<!-- BOF - Tomcraft - 2009-06-10 - added some missing alternative text on admin icons -->
<!--
                <td class="dataTableContent" align="right"><?php if ( (is_object($cInfo)) && ($campaigns['campaigns_id'] == $cInfo->campaigns_id) ) { echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . xtc_href_link(FILENAME_CAMPAIGNS, 'page=' . $_GET['page'] . '&cID=' . $campaigns['campaigns_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
-->
                <td class="dataTableContent" align="right"><?php if ( (is_object($cInfo)) && ($campaigns['campaigns_id'] == $cInfo->campaigns_id) ) { echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ICON_ARROW_RIGHT); } else { echo '<a href="' . xtc_href_link(FILENAME_CAMPAIGNS, 'page=' . $_GET['page'] . '&cID=' . $campaigns['campaigns_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
<!-- EOF - Tomcraft - 2009-06-10 - added some missing alternative text on admin icons -->
              </tr>
<?php

}
?>      </table>
            <div class='col-xs-12'>
                <div class="smallText col-xs-6"><?php echo $campaigns_split->display_count($campaigns_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CAMPAIGNS); ?></div>
                <div class="smallText col-xs-6 text-right"><?php echo $campaigns_split->display_links($campaigns_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></div>
            </div>
<?php

if ($_GET['action'] != 'new') {
?>
                <div class="smallText col-xs-12 text-right"><?php echo xtc_button_link(BUTTON_INSERT, xtc_href_link(FILENAME_CAMPAIGNS, 'page=' . $_GET['page'] . '&cID=' . $cInfo->campaigns_id . '&action=new')); ?></div>
<?php

}
?>
        </div>
<?php

$heading = array ();
$contents = array ();
switch ($_GET['action']) {
	case 'new' :
                if(isset($_SESSION['repopulate_form'])){
                    $c_name = ($_SESSION['repopulate_form']['campaigns_name']) ? $_SESSION['repopulate_form']['campaigns_name'] : '';
                    $c_refID = ($_SESSION['repopulate_form']['campaigns_refID']) ? $_SESSION['repopulate_form']['campaigns_refID'] : '';
                    unset($_SESSION['repopulate_form']);
                }
		$heading[] = array ('text' => '<b>'.TEXT_HEADING_NEW_CAMPAIGN.'</b>');

		$contents = array ('form' => xtc_draw_form('campaigns', FILENAME_CAMPAIGNS, 'action=insert', 'post', 'enctype="multipart/form-data"'));
		$contents[] = array ('text' => TEXT_NEW_INTRO);
		$contents[] = array ('text' => '<br />'.TEXT_CAMPAIGNS_NAME.'<br />'.xtc_draw_input_field('campaigns_name', $c_name));
		$contents[] = array ('text' => '<br />'.TEXT_CAMPAIGNS_REFID.'<br />'.xtc_draw_input_field('campaigns_refID', $c_refID));
		$contents[] = array ('align' => 'center', 'text' => '<br />'.xtc_button(BUTTON_SAVE).'&nbsp;'.xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_CAMPAIGNS, 'page='.$_GET['page'].'&cID='.$_GET['cID'])));
		break;

	case 'edit' :
		$heading[] = array ('text' => '<b>'.TEXT_HEADING_EDIT_CAMPAIGN.'</b>');

		$contents = array ('form' => xtc_draw_form('campaigns', FILENAME_CAMPAIGNS, 'page='.$_GET['page'].'&cID='.$cInfo->campaigns_id.'&action=save', 'post', 'enctype="multipart/form-data"'));
		$contents[] = array ('text' => TEXT_EDIT_INTRO);
		$contents[] = array ('text' => '<br />'.TEXT_CAMPAIGNS_NAME.'<br />'.xtc_draw_input_field('campaigns_name', $cInfo->campaigns_name));
		$contents[] = array ('text' => '<br />'.TEXT_CAMPAIGNS_REFID.'<br />'.xtc_draw_input_field('campaigns_refID', $cInfo->campaigns_refID));
		$contents[] = array ('align' => 'center', 'text' => '<br />'.xtc_button(BUTTON_SAVE).'&nbsp;'.xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_CAMPAIGNS, 'page='.$_GET['page'].'&cID='.$cInfo->campaigns_id)));
		break;

	case 'delete' :
		$heading[] = array ('text' => '<b>'.TEXT_HEADING_DELETE_CAMPAIGN.'</b>');

		$contents = array ('form' => xtc_draw_form('campaigns', FILENAME_CAMPAIGNS, 'page='.$_GET['page'].'&cID='.$cInfo->campaigns_id.'&action=deleteconfirm'));
		$contents[] = array ('text' => TEXT_DELETE_INTRO);
		$contents[] = array ('text' => '<br /><b>'.$cInfo->campaigns_name.'</b>');

		if ($cInfo->refferers_count > 0) {
			$contents[] = array ('text' => '<br />'.xtc_draw_checkbox_field('delete_refferers').' '.TEXT_DELETE_REFFERERS);
			$contents[] = array ('text' => '<br />'.sprintf(TEXT_DELETE_WARNING_REFFERERS, $cInfo->refferers_count));
		}

		$contents[] = array ('align' => 'center', 'text' => '<br />'.xtc_button(BUTTON_DELETE).'&nbsp;'.xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_CAMPAIGNS, 'page='.$_GET['page'].'&cID='.$cInfo->campaigns_id)));
		break;

	default :
		if (is_object($cInfo)) {
			$heading[] = array ('text' => '<b>'.$cInfo->campaigns_name.'</b>');

			$contents[] = array ('align' => 'center', 'text' => xtc_button_link(BUTTON_EDIT, xtc_href_link(FILENAME_CAMPAIGNS, 'page='.$_GET['page'].'&cID='.$cInfo->campaigns_id.'&action=edit')).'&nbsp;'.xtc_button_link(BUTTON_DELETE, xtc_href_link(FILENAME_CAMPAIGNS, 'page='.$_GET['page'].'&cID='.$cInfo->campaigns_id.'&action=delete')));
			$contents[] = array ('text' => '<br />'.TEXT_DATE_ADDED.' '.xtc_date_short($cInfo->date_added));
			if (xtc_not_null($cInfo->last_modified))
				$contents[] = array ('text' => TEXT_LAST_MODIFIED.' '.xtc_date_short($cInfo->last_modified));
			$contents[] = array ('text' => TEXT_REFERER.'?refID='.$cInfo->campaigns_refID);
		}
		break;
}

if ((xtc_not_null($heading)) && (xtc_not_null($contents))) {
	echo '            <div class="col-md-4 col-sm-12 col-xs-12 pull-right">'."\n";

	$box = new box;
	echo $box->infoBox($heading, $contents);

	echo '            </div>'."\n";
        ?>
        <script>
            //responsive_table
            $('#responsive_table').addClass('col-md-8');
        </script>               
        <?php
}
?>
</div>
<!-- body_text_eof //-->
</div>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br />
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
