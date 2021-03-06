<?php
/* --------------------------------------------------------------
   $Id: stats_campaigns.php 1179 2005-08-25 12:37:13Z mz $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2005 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce coding standards; www.oscommerce.com

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contribution:

   stats_sales_report (c) Charly Wilhelm  charly@yoshi.ch

   Released under the GNU General Public License
   --------------------------------------------------------------*/

require ('includes/application_top.php');

require (DIR_WS_CLASSES.'currencies.php');
$currencies = new currencies();

require (DIR_WS_CLASSES.'campaigns.php');
$campaign = new campaigns($_GET);

$orders_statuses = array ();
$orders_status_array = array ();
$orders_status_query = xtc_db_query("select orders_status_id, orders_status_name from ".TABLE_ORDERS_STATUS." where language_id = '".$_SESSION['languages_id']."'");
while ($orders_status = xtc_db_fetch_array($orders_status_query)) {
	$orders_statuses[] = array ('id' => $orders_status['orders_status_id'], 'text' => $orders_status['orders_status_name']);
	$orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
}

$campaigns = array ();
$campaign_query = "SELECT * FROM ".TABLE_CAMPAIGNS;
$campaign_query = xtc_db_query($campaign_query);
while ($campaign_data = xtc_db_fetch_array($campaign_query)) {
	$campaigns[] = array ('id' => $campaign_data['campaigns_refID'], 'text' => $campaign_data['campaigns_name']);
}

// report views (1: yearly 2: monthly 3: weekly 4: daily)
if (($_GET['report']) && (xtc_not_null($_GET['report']))) {
	$srView = $_GET['report'];
}
if ($srView < 1 || $srView > 4) {
	$srView = $srDefaultView;
}

// check start and end Date
$startDate = "";
$startDateG = 0;
if (($_GET['startD']) && (xtc_not_null($_GET['startD']))) {
	$sDay = $_GET['startD'];
	$startDateG = 1;
} else {
	$sDay = 1;
}
if (($_GET['startM']) && (xtc_not_null($_GET['startM']))) {
	$sMon = $_GET['startM'];
	$startDateG = 1;
} else {
	$sMon = 1;
}
if (($_GET['startY']) && (xtc_not_null($_GET['startY']))) {
	$sYear = $_GET['startY'];
	$startDateG = 1;
} else {
	$sYear = date("Y");
}
if ($startDateG) {
	$startDate = mktime(0, 0, 0, $sMon, $sDay, $sYear);
} else {
	$startDate = mktime(0, 0, 0, date("m"), 1, date("Y"));
}

$endDate = "";
$endDateG = 0;
if (($_GET['endD']) && (xtc_not_null($_GET['endD']))) {
	$eDay = $_GET['endD'];
	$endDateG = 1;
} else {
	$eDay = 1;
}
if (($_GET['endM']) && (xtc_not_null($_GET['endM']))) {
	$eMon = $_GET['endM'];
	$endDateG = 1;
} else {
	$eMon = 1;
}
if (($_GET['endY']) && (xtc_not_null($_GET['endY']))) {
	$eYear = $_GET['endY'];
	$endDateG = 1;
} else {
	$eYear = date("Y");
}
if ($endDateG) {
	$endDate = mktime(0, 0, 0, $eMon, $eDay +1, $eYear);
} else {
	$endDate = mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"));
}

require (DIR_WS_INCLUDES.'head.php');
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<?php


require (DIR_WS_INCLUDES.'header.php');
?>
<!-- header_eof //-->

<!-- body //-->
<div class='row'>
                <div class='col-xs-12'>
                    <div class="col-xs-3 col-sm-1 text-right"><?php echo xtc_image(DIR_WS_ICONS.'heading_modules.gif'); ?></div>
                    <div class="col-xs-9 col-sm-11"><p class="h2"><?php echo HEADING_TITLE; ?></p> Statistics</div>
                </div>
                <div class='col-xs-12'><br></div>

<?php

if ($srExp < 1) {
?>
        <div class='col-xs-12' style="border: 1px solid; border-color: #cccccc; background-color: #fcfcfc;">   
            <div class='col-xs-12'><br></div>
            <div class='col-xs-12 col-sm-4 '>
                    <input type="radio" name="report" value="1" <?php if ($srView == 1) echo "checked"; ?>><?php echo REPORT_TYPE_YEARLY; ?><br />
                    <input type="radio" name="report" value="2" <?php if ($srView == 2) echo "checked"; ?>><?php echo REPORT_TYPE_MONTHLY; ?><br />
                    <input type="radio" name="report" value="3" <?php if ($srView == 3) echo "checked"; ?>><?php echo REPORT_TYPE_WEEKLY; ?><br />
                    <input type="radio" name="report" value="4" <?php if ($srView == 4) echo "checked"; ?>><?php echo REPORT_TYPE_DAILY; ?><br />
            </div>
            <div class='col-xs-12 hidden-sm hidden-md hidden-lg'><hr></div>
            <div class='col-xs-12 col-sm-4 '>
<?php echo REPORT_START_DATE; ?><br />
                    <select name="startD" size="1">
<?php

	if ($startDate) {
		$j = date("j", $startDate);
	} else {
		$j = 1;
	}
	for ($i = 1; $i < 32; $i ++) {
?>
                        <option<?php if ($j == $i) echo " selected"; ?>><?php echo $i; ?></option>
<?php

	}
?>
                    </select>
                    <select name="startM" size="1">
<?php

	if ($startDate) {
		$m = date("n", $startDate);
	} else {
		$m = 1;
	}
	for ($i = 1; $i < 13; $i ++) {
?>
                      <option<?php if ($m == $i) echo " selected"; ?> value="<?php echo $i; ?>"><?php echo strftime("%B", mktime(0, 0, 0, $i, 1)); ?></option>
<?php

	}
?>
                    </select>
                    <select name="startY" size="1">
<?php

	if ($startDate) {
		$y = date("Y") - date("Y", $startDate);
	} else {
		$y = 0;
	}
	for ($i = 10; $i >= 0; $i --) {
?>
                      <option<?php if ($y == $i) echo " selected"; ?>><?php echo date("Y") - $i; ?></option>
<?php

	}
?>
                    </select>
<br><br>
<?php echo REPORT_END_DATE; ?><br />
                    <select name="endD" size="1">
<?php

	if ($endDate) {
		$j = date("j", $endDate -60 * 60 * 24);
	} else {
		$j = date("j");
	}
	for ($i = 1; $i < 32; $i ++) {
?>
                      <option<?php if ($j == $i) echo " selected"; ?>><?php echo $i; ?></option>
<?php

	}
?>
                    </select>
                    <select name="endM" size="1">
<?php

	if ($endDate) {
		$m = date("n", $endDate -60 * 60 * 24);
	} else {
		$m = date("n");
	}
	for ($i = 1; $i < 13; $i ++) {
?>
                      <option<?php if ($m == $i) echo " selected"; ?> value="<?php echo $i; ?>"><?php echo strftime("%B", mktime(0, 0, 0, $i, 1)); ?></option>
<?php

	}
?>
                    </select>
                    <select name="endY" size="1">
<?php

	if ($endDate) {
		$y = date("Y") - date("Y", $endDate -60 * 60 * 24);
	} else {
		$y = 0;
	}
	for ($i = 10; $i >= 0; $i --) {
?>
                      <option<?php if ($y == $i) echo " selected"; ?>><?php

		echo date("Y") - $i;
?></option><?php

	}
?>
                    </select>
                  </div>
            <div class='col-xs-12 hidden-sm hidden-md hidden-lg'><hr></div>
            <div class="col-xs-12 col-sm-4"> <?php echo REPORT_STATUS_FILTER; ?><br /> 
                    <?php echo xtc_draw_pull_down_menu('status', array_merge(array(array('id' => '0', 'text' => REPORT_ALL)), $orders_statuses), $_GET['status']); ?> 
                    
                    <br /><?php echo REPORT_CAMPAIGN_FILTER; ?><br /> 
<?php echo xtc_draw_pull_down_menu('campaign', array_merge(array(array('id' => '0', 'text' => REPORT_ALL)), $campaigns), $_GET['campaign']); ?> 
                    

                

                </div> 
            <div class='col-xs-12 hidden-sm hidden-md hidden-lg'><hr></div>
                  <div class="col-xs-12 text-right">
                  <?php echo '<input type="submit" class="btn btn-default" onclick="this.blur();" value="' . BUTTON_UPDATE . '"/>'; ?>
                  </div>
            </div>
<?php

} // end of ($srExp < 1)
?>

 <?php

if (count($campaign->result)) {
?>               
                
                
    
    
                <div class='table-responsive col-xs-12'>
 <table class='table table-bordered'>
 
   <tr class="dataTableHeadingRow"> 
    <td class="dataTableHeadingContent hidden-xs" colspan="2" width="25%"><?php echo HEADING_TOTAL; ?></td>
    <td class="dataTableHeadingContent" width="10%">&nbsp;</td>
    <td class="dataTableHeadingContent hidden-xs" width="15%"><?php echo $campaign->total['leads']; ?></td>
    <td class="dataTableHeadingContent hidden-xs" colspan="2" width="30%"><?php echo $campaign->total['sells']; ?></td>
    <td class="dataTableHeadingContent" width="20%"><?php echo $campaign->total['sum']; ?></td>
  </tr>
  <tr class="dataTableHeadingRow"> 
    <td class="dataTableHeadingContent hidden-xs" colspan="2" width="25%">&nbsp;</td>
    <td class="dataTableHeadingContent" width="10%"><?php echo HEADING_HITS; ?></td>
    <td class="dataTableHeadingContent hidden-xs" width="15%"><?php echo HEADING_LEADS; ?></td>
    <td class="dataTableHeadingContent hidden-xs" width="15%"><?php echo HEADING_SELLS; ?></td>
    <td class="dataTableHeadingContent hidden-xs" width="15%"><?php echo HEADING_LATESELLS; ?></td>
    <td class="dataTableHeadingContent" width="20%"><?php echo HEADING_SUM; ?></td>
  </tr>
  
 <?php

	// show campaigns

	for ($n = 0; $n < count($campaign->result); $n ++) {
?>
  
  
  
  
  <tr class="dataTableRow"> 
    <td class="main" colspan="7" style="border-bottom: 2px solid;"><br /><?php echo $campaign->result[$n]['text'].' '.TEXT_REFERER .' ('.$campaign->result[$n]['id'].')'; ?></td>
  </tr>
  
  <?php
		// show values
		for ($nn = 0; $nn < count($campaign->result[$n]['result']); $nn ++) {
?>
  
  <tr class="dataTableRow"> 
    <td class="dataTableContent">&nbsp;</td>
    <td class="dataTableContent"><?php echo $campaign->result[$n]['result'][$nn]['range']; ?></td>
    <td class="dataTableContent"><?php echo $campaign->result[$n]['result'][$nn]['hits']; ?></td>
    <td class="dataTableContent"><?php echo $campaign->result[$n]['result'][$nn]['leads'].' ('.$campaign->result[$n]['result'][$nn]['leads_p'].'%)'; ?></td>
    <td class="dataTableContent"><?php echo $campaign->result[$n]['result'][$nn]['sells'].' ('.$campaign->result[$n]['result'][$nn]['sells_p'].'%)'; ?></td>
    <td class="dataTableContent"><?php echo $campaign->result[$n]['result'][$nn]['late_sells'].' ('.$campaign->result[$n]['result'][$nn]['late_sells_p'].'%)'; ?></td>
    <td class="dataTableContent"><?php echo $campaign->result[$n]['result'][$nn]['sum'].' ('.$campaign->result[$n]['result'][$nn]['sum_p'].'%)'; ?></td>
 </tr>
  
  <?php

		}
?>
  
  
    <tr class="dataTableRow"> 
    <td class="dataTableContent"><strong><?php echo HEADING_SUM; ?></strong></td>
    <td class="dataTableContent">&nbsp;</td>
    <td class="dataTableContent"><strong><?php echo $campaign->result[$n]['hits_s']; ?></strong></td>
    <td class="dataTableContent"><strong><?php echo $campaign->result[$n]['leads_s'].' ('.($campaign->total['leads']> 0 ? ($campaign->result[$n]['leads_s']/$campaign->total['leads']*100):'0').'%)'; ?></strong></td>
    <td class="dataTableContent"><strong><?php echo $campaign->result[$n]['sells_s'].' ('.($campaign->total['sells']> 0 ? ($campaign->result[$n]['sells_s']/$campaign->total['sells']*100):'0').'%)'; ?></strong></td>
    <td class="dataTableContent"><strong><?php echo $campaign->result[$n]['late_sells_s'].' ('.($campaign->total['sells']> 0 ? ($campaign->result[$n]['late_sells_s']/$campaign->total['sells']*100):'0').'%)'; ?></strong></td>
    <td class="dataTableContent"><strong><?php echo $campaign->result[$n]['sum_s'].' ('.($campaign->total['sum_plain']> 0 ? round(($campaign->result[$n]['sum_s']/$campaign->total['sum_plain']*100),0):'0').'%)'; ?></strong></td>
  </tr>
  
  
  <?php


	}
?>
</table>
                    </div>
<?php } ?>
</div>
<!-- body_eof //-->

<!-- footer //-->
<?php


	require (DIR_WS_INCLUDES.'footer.php');
?>
<!-- footer_eof //-->
</body>
</html>
<?php

	require (DIR_WS_INCLUDES.'application_bottom.php');
?>
