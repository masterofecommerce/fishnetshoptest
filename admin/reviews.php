<?php
/* --------------------------------------------------------------
   $Id: reviews.php 4255 2013-01-11 16:04:14Z web28 $   

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(reviews.php,v 1.40 2003/03/22); www.oscommerce.com 
   (c) 2003	 nextcommerce (reviews.php,v 1.9 2003/08/18); www.nextcommerce.org

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

  require('includes/application_top.php');

  if ($_GET['action']) {
    switch ($_GET['action']) {
      case 'update':
        $reviews_id = xtc_db_prepare_input($_GET['rID']);
        $reviews_rating = xtc_db_prepare_input($_POST['reviews_rating']);
        $last_modified = xtc_db_prepare_input($_POST['last_modified']);
        $reviews_text = xtc_db_prepare_input($_POST['reviews_text']);

        xtc_db_query("update " . TABLE_REVIEWS . " set reviews_rating = '" . xtc_db_input($reviews_rating) . "', last_modified = now() where reviews_id = '" . xtc_db_input($reviews_id) . "'");
        xtc_db_query("update " . TABLE_REVIEWS_DESCRIPTION . " set reviews_text = '" . xtc_db_input($reviews_text) . "' where reviews_id = '" . xtc_db_input($reviews_id) . "'");

        xtc_redirect(xtc_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $reviews_id));
        break;

      case 'deleteconfirm':
        $reviews_id = xtc_db_prepare_input($_GET['rID']);

        xtc_db_query("delete from " . TABLE_REVIEWS . " where reviews_id = '" . xtc_db_input($reviews_id) . "'");
        xtc_db_query("delete from " . TABLE_REVIEWS_DESCRIPTION . " where reviews_id = '" . xtc_db_input($reviews_id) . "'");

        xtc_redirect(xtc_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page']));
        break;
    }
  }
require (DIR_WS_INCLUDES.'head.php');
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<div class="row">
<!-- body_text //-->
    <div class='col-xs-12'>
        <p class="h2">
            <?php echo HEADING_TITLE; ?>
        </p>
    </div>
<div class='col-xs-12'><br></div>
<div class='col-xs-12'>
<?php
  if ($_GET['action'] == 'edit') {
    $rID = xtc_db_prepare_input($_GET['rID']);

    $reviews_query = xtc_db_query("select r.reviews_id, r.products_id, r.customers_name, r.date_added, r.last_modified, r.reviews_read, rd.reviews_text, r.reviews_rating from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd where r.reviews_id = '" . xtc_db_input($rID) . "' and r.reviews_id = rd.reviews_id");
    $reviews = xtc_db_fetch_array($reviews_query);
    $products_query = xtc_db_query("select products_image from " . TABLE_PRODUCTS . " where products_id = '" . $reviews['products_id'] . "'");
    $products = xtc_db_fetch_array($products_query);

    $products_name_query = xtc_db_query("select products_name from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . $reviews['products_id'] . "' and language_id = '" . $_SESSION['languages_id'] . "'");
    $products_name = xtc_db_fetch_array($products_name_query);

    $rInfo_array = xtc_array_merge($reviews, $products, $products_name);
    $rInfo = new objectInfo($rInfo_array);
?>
    <?php echo xtc_draw_form('review', FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $_GET['rID'] . '&action=preview'); ?>
    <div class="col-xs-12">
      <div class="col-xs-12 main" ><b><?php echo ENTRY_PRODUCT; ?></b> <?php echo $rInfo->products_name; ?><br /><b><?php echo ENTRY_FROM; ?></b> <?php echo $rInfo->customers_name; ?><br /><br /><b><?php echo ENTRY_DATE; ?></b> <?php echo xtc_date_short($rInfo->date_added); ?></div>
      <div class="col-xs-12 main" ><?php echo xtc_image(HTTP_CATALOG_SERVER . DIR_WS_CATALOG_IMAGES . $rInfo->products_image, $rInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"'); ?></div>
    </div>
    <div class="col-xs-12">
          <div class="col-xs-12">
            <b><?php echo ENTRY_REVIEW; ?></b><br /><br /><?php echo xtc_draw_textarea_field('reviews_text', 'soft', '60', '15', $rInfo->reviews_text); ?>
          </div>
          <div class="col-xs-12">
            <?php echo ENTRY_REVIEW_TEXT; ?>
          </div>
    </div>
    <div class="col-xs-12">
        <br>
    </div>
    
        <div class="col-xs-12">
          <b><?php echo ENTRY_RATING; ?></b>&nbsp;<?php echo TEXT_BAD; ?>&nbsp;<?php for ($i=1; $i<=5; $i++) echo xtc_draw_radio_field('reviews_rating', $i, '', $rInfo->reviews_rating) . '&nbsp;'; echo TEXT_GOOD; ?>
        </div>
        <div class="main col-xs-12"><?php echo xtc_draw_hidden_field('reviews_id', $rInfo->reviews_id) . xtc_draw_hidden_field('products_id', $rInfo->products_id) . xtc_draw_hidden_field('customers_name', $rInfo->customers_name) . xtc_draw_hidden_field('products_name', $rInfo->products_name) . xtc_draw_hidden_field('products_image', $rInfo->products_image) . xtc_draw_hidden_field('date_added', $rInfo->date_added) . '<input type="submit" class="btn btn-default" onclick="this.blur();" value="' . BUTTON_PREVIEW . '"/> <a class="btn btn-default" onclick="this.blur();" href="' . xtc_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $_GET['rID']) . '">' . BUTTON_CANCEL . '</a>'; ?></div>
     
    </form>
<?php
  } elseif ($_GET['action'] == 'preview') {
    if ($_POST) {
      $rInfo = new objectInfo($_POST);
    } else {
      $reviews_query = xtc_db_query("select r.reviews_id, r.products_id, r.customers_name, r.date_added, r.last_modified, r.reviews_read, rd.reviews_text, r.reviews_rating from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd where r.reviews_id = '" . $_GET['rID'] . "' and r.reviews_id = rd.reviews_id");
      $reviews = xtc_db_fetch_array($reviews_query);
      $products_query = xtc_db_query("select products_image from " . TABLE_PRODUCTS . " where products_id = '" . $reviews['products_id'] . "'");
      $products = xtc_db_fetch_array($products_query);

      $products_name_query = xtc_db_query("select products_name from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . $reviews['products_id'] . "' and language_id = '" . $_SESSION['languages_id'] . "'");
      $products_name = xtc_db_fetch_array($products_name_query);

      $rInfo_array = xtc_array_merge($reviews, $products, $products_name);
      $rInfo = new objectInfo($rInfo_array);
    }
?>
      <tr><?php echo xtc_draw_form('update', FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $_GET['rID'] . '&action=update', 'post', 'enctype="multipart/form-data"'); ?>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="main" valign="top"><b><?php echo ENTRY_PRODUCT; ?></b> <?php echo $rInfo->products_name; ?><br /><b><?php echo ENTRY_FROM; ?></b> <?php echo $rInfo->customers_name; ?><br /><br /><b><?php echo ENTRY_DATE; ?></b> <?php echo xtc_date_short($rInfo->date_added); ?></td>
            <td class="main" align="right" valign="top"><?php echo xtc_image(HTTP_CATALOG_SERVER . DIR_WS_CATALOG_IMAGES . $rInfo->products_image, $rInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"'); ?></td>
          </tr>
        </table>
      </tr>
      <tr>
        <td><table witdh="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top" class="main"><b><?php echo ENTRY_REVIEW; ?></b><br /><br /><?php echo nl2br(xtc_db_output(xtc_break_string($rInfo->reviews_text, 15))); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="main"><b><?php echo ENTRY_RATING; ?></b>&nbsp;<?php echo xtc_image(HTTP_CATALOG_SERVER . DIR_WS_CATALOG . 'templates/'. CURRENT_TEMPLATE .'/img/stars_' . $rInfo->reviews_rating . '.gif', sprintf(TEXT_OF_5_STARS, $rInfo->reviews_rating)); ?>&nbsp;<small>[<?php echo sprintf(TEXT_OF_5_STARS, $rInfo->reviews_rating); ?>]</small></td>
      </tr>
      <tr>
        <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
<?php
    if ($_POST) {
      // Re-Post all POST'ed variables
      reset($_POST);
      while(list($key, $value) = each($_POST)) echo '<input type="hidden" name="' . $key . '" value="' . encode_htmlspecialchars(stripslashes($value)) . '">';
?>
      <tr>
        <td align="right" class="smallText"><?php echo '<a class="btn btn-default" onclick="this.blur();" href="' . xtc_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id . '&action=edit') . '">' . BUTTON_BACK . '</a> <input type="submit" class="btn btn-default" onclick="this.blur();" value="' . BUTTON_UPDATE . '"/> <a class="btn btn-default" onclick="this.blur();" href="' . xtc_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id) . '">' . BUTTON_CANCEL . '</a>'; ?></td>
      </form></tr>
<?php
    } else {
      if ($_GET['origin']) {
        $back_url = $_GET['origin'];
        $back_url_params = '';
      } else {
        $back_url = FILENAME_REVIEWS;
        $back_url_params = 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id;
      }
?>
      <tr>
        <td align="right"><?php echo '<a class="btn btn-default" onclick="this.blur();" href="' . xtc_href_link($back_url, $back_url_params, 'NONSSL') . '">' . BUTTON_BACK . '</a>'; ?></td>
      </tr>
<?php
    }
  } else {
?>
        <div id='responsive_table' class='table-responsive pull-left col-sm-12'>
            <table class="table table-bordered table-striped">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_RATING; ?></td>
                <td class="dataTableHeadingContent hidden-xs" align="right"><?php echo TABLE_HEADING_DATE_ADDED; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    $reviews_query_raw = "select reviews_id, products_id, date_added, last_modified, reviews_rating from " . TABLE_REVIEWS . " order by date_added DESC";
    $reviews_split = new splitPageResults($_GET['page'], '20', $reviews_query_raw, $reviews_query_numrows);
    $reviews_query = xtc_db_query($reviews_query_raw);
    while ($reviews = xtc_db_fetch_array($reviews_query)) {
      if ( ((!$_GET['rID']) || ($_GET['rID'] == $reviews['reviews_id'])) && (!$rInfo) ) {
        $reviews_text_query = xtc_db_query("select r.reviews_read, r.customers_name, length(rd.reviews_text) as reviews_text_size from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd where r.reviews_id = '" . $reviews['reviews_id'] . "' and r.reviews_id = rd.reviews_id");
        $reviews_text = xtc_db_fetch_array($reviews_text_query);

        $products_image_query = xtc_db_query("select products_image from " . TABLE_PRODUCTS . " where products_id = '" . $reviews['products_id'] . "'");
        $products_image = xtc_db_fetch_array($products_image_query);

        $products_name_query = xtc_db_query("select products_name from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . $reviews['products_id'] . "' and language_id = '" . $_SESSION['languages_id'] . "'");
        $products_name = xtc_db_fetch_array($products_name_query);

        $reviews_average_query = xtc_db_query("select (avg(reviews_rating) / 5 * 100) as average_rating from " . TABLE_REVIEWS . " where products_id = '" . $reviews['products_id'] . "'");
        $reviews_average = xtc_db_fetch_array($reviews_average_query);

        $review_info = xtc_array_merge($reviews_text, $reviews_average, $products_name);
        $rInfo_array = xtc_array_merge($reviews, $review_info, $products_image);
        $rInfo = new objectInfo($rInfo_array);
      }

      if ( (is_object($rInfo)) && ($reviews['reviews_id'] == $rInfo->reviews_id) ) {
        echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'pointer\'" onclick="document.location.href=\'' . xtc_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id . '&action=preview') . '\'">' . "\n";
      } else {
        echo '              <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'pointer\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . xtc_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $reviews['reviews_id']) . '\'">' . "\n";
      }
?>
                <td class="dataTableContent"><?php echo '<a href="' . xtc_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $reviews['reviews_id'] . '&action=preview') . '">' . xtc_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW) . '</a>&nbsp;' . xtc_get_products_name($reviews['products_id']); ?></td>
                <td class="dataTableContent" align="right"><?php echo xtc_image(HTTP_CATALOG_SERVER . DIR_WS_CATALOG . 'templates/'. CURRENT_TEMPLATE .'/img/stars_' . $reviews['reviews_rating'] . '.gif'); ?></td>
                <td class="dataTableContent hidden-xs" align="right"><?php echo xtc_date_short($reviews['date_added']); ?></td>
<!-- BOF - Tomcraft - 2009-06-10 - added some missing alternative text on admin icons -->
<!--
                <td class="dataTableContent" align="right"><?php if ( (is_object($rInfo)) && ($reviews['reviews_id'] == $rInfo->reviews_id) ) { echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . xtc_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $reviews['reviews_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
-->
                <td class="dataTableContent" align="right">
                    <span class='hidden-xs hidden-sm'>
                    <?php if ( (is_object($rInfo)) && ($reviews['reviews_id'] == $rInfo->reviews_id) ) { echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ICON_ARROW_RIGHT); } else { echo '<a href="' . xtc_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $reviews['reviews_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;
                    </span>
                    <span class='hidden-lg hidden-md'>
                    <?php echo '<a class="btn btn-default" onclick="this.blur();" href="' . xtc_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $reviews['reviews_id'] . '&action=edit') . '">' . BUTTON_EDIT . '</a>';  ?>    
                    </span>
                </td>
<!-- EOF - Tomcraft - 2009-06-10 - added some missing alternative text on admin icons -->
              </tr>
<?php
    }
?>
              </table>
                  <div class='col-xs-12'>
                    <div class="smallText col-xs-6"><?php echo $reviews_split->display_count($reviews_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_REVIEWS); ?></div>
                    <div class="smallText col-xs-6 text-right"><?php echo $reviews_split->display_links($reviews_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></div>
                  </div>
            </div>

<?php
    $heading = array();
    $contents = array();
    switch ($_GET['action']) {
      case 'delete':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_REVIEW . '</b>');

        $contents = array('form' => xtc_draw_form('reviews', FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id . '&action=deleteconfirm'));
        $contents[] = array('text' => TEXT_INFO_DELETE_REVIEW_INTRO);
        $contents[] = array('text' => '<br /><b>' . $rInfo->products_name . '</b>');
        $contents[] = array('align' => 'center', 'text' => '<br /><input type="submit" class="btn btn-default" onclick="this.blur();" value="' . BUTTON_DELETE . '"/> <a class="btn btn-default" onclick="this.blur();" href="' . xtc_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id) . '">' . BUTTON_CANCEL . '</a>');
        break;

      default:
      if (is_object($rInfo)) {
        $heading[] = array('text' => '<b>' . $rInfo->products_name . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<a class="btn btn-default" onclick="this.blur();" href="' . xtc_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id . '&action=edit') . '">' . BUTTON_EDIT . '</a> <a class="btn btn-default" onclick="this.blur();" href="' . xtc_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id . '&action=delete') . '">' . BUTTON_DELETE . '</a>');
        $contents[] = array('text' => '<br />' . TEXT_INFO_DATE_ADDED . ' ' . xtc_date_short($rInfo->date_added));
        if (xtc_not_null($rInfo->last_modified)) $contents[] = array('text' => TEXT_INFO_LAST_MODIFIED . ' ' . xtc_date_short($rInfo->last_modified));
        $contents[] = array('text' => '<br />' . xtc_product_thumb_image($rInfo->products_image, $rInfo->products_name));
        $contents[] = array('text' => '<br />' . TEXT_INFO_REVIEW_AUTHOR . ' ' . $rInfo->customers_name);
        $contents[] = array('text' => TEXT_INFO_REVIEW_RATING . ' ' . xtc_image(HTTP_CATALOG_SERVER . DIR_WS_CATALOG . 'templates/'. CURRENT_TEMPLATE .'/img/stars_' . $rInfo->reviews_rating . '.gif'));
        $contents[] = array('text' => TEXT_INFO_REVIEW_READ . ' ' . $rInfo->reviews_read);
        $contents[] = array('text' => '<br />' . TEXT_INFO_REVIEW_SIZE . ' ' . $rInfo->reviews_text_size . ' bytes');
        $contents[] = array('text' => '<br />' . TEXT_INFO_PRODUCTS_AVERAGE_RATING . ' ' . number_format($rInfo->average_rating, 2) . '%');
      }
        break;
    }

    if ( (xtc_not_null($heading)) && (xtc_not_null($contents)) ) {
      echo '<div class="col-md-3 hidden-xs hidden-sm pull-right">' . "\n";#col-sm-12 col-xs-12

      $box = new box;
      echo $box->infoBox($heading, $contents);

      echo '</div>' . "\n";
            ?>
          <script>
              //responsive_table
              $('#responsive_table').addClass('col-md-9');
          </script>               
          <?php
    }
?>
          </tr>
        </table></td>
      </tr>
<?php
  }
?>
</div></div>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br />
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
