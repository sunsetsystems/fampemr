<?php
// Copyright (C) 2013 Rod Roark <rod@sunsetsystems.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

// This provides a function to build an array of data for printing on a checkout
// receipt.  The idea is to support a receipt-printing script that is very easy
// to understand and customize and does not have to be concerned with where the
// data comes from.

// Get a list item's title, translated if appropriate.
function getAdjustTitle($option) {
  $row = sqlQuery("SELECT title FROM list_options WHERE " .
    "list_id = 'adjreason' AND option_id = '$option'");
  if (empty($row['title'])) return $option;
  return xl_list_label($row['title']);
}

// Store a receipt line item.
//
function receiptArrayDetailLine(&$aReceipt, $code_type, $code, $description, $quantity, $charge) {
  $adjust = 0;
  $adjreason = '';

  // If an invoice level adjustment, get it into the right column.
  if ($code_type === '') {
    $adjust = 0 - $charge;
    $charge = 0;
  }
  // Otherwise pull out any adjustments matching this line item.
  else {
    if (!empty($GLOBALS['gbl_checkout_line_adjustments'])) {
      // Total and clear matching adjustments in $aReceipt['_adjusts'].
      for ($i = 0; $i < count($aReceipt['_adjusts']); ++$i) {
        if ($aReceipt['_adjusts'][$i]['code_type'] == $code_type &&
          $aReceipt['_adjusts'][$i]['code'] == $code &&
          $aReceipt['_adjusts'][$i]['adj_amount'] != 0)
        {
          $adjust += $aReceipt['_adjusts'][$i]['adj_amount'];
          if ($aReceipt['_adjusts'][$i]['memo']) {
            $adjreason = getAdjustTitle($aReceipt['_adjusts'][$i]['memo']);
          }
          $aReceipt['_adjusts'][$i]['adj_amount'] = 0;
        }
      }
    }
  }

  $charge = sprintf('%01.2f', $charge);
  $total  = sprintf('%01.2f', $charge - $adjust);
  if (empty($quantity)) $quantity = 1;
  $price = sprintf('%01.4f', $charge / $quantity);
  $tmp = sprintf('%01.2f', $price);
  if ($price == $tmp) $price = $tmp; // converts xx.xx00 to xx.xx.

  $aReceipt['items'][] = array(
    'code_type'   => $code_type,
    'code'        => $code,
    'description' => $description,
    'price'       => $price,
    'quantity'    => $quantity,
    'charge'      => $charge,
    'adjustment'  => sprintf('%01.2f', $adjust),
    'adjreason'   => $adjreason,
    'total'       => $total,
  );

  $aReceipt['total_price']       = sprintf('%01.2f', $aReceipt['total_price'      ] + $price   );
  $aReceipt['total_quantity']    = sprintf('%01.2f', $aReceipt['total_quantity'   ] + $quantity);
  $aReceipt['total_charges']     = sprintf('%01.2f', $aReceipt['total_charge'     ] + $charge  );
  $aReceipt['total_adjustments'] = sprintf('%01.2f', $aReceipt['total_adjustments'] + $adjust  );
  $aReceipt['total_totals']      = sprintf('%01.2f', $aReceipt['total_totals'     ] + $total   );
}

// Store a receipt payment line.
//
function receiptArrayPaymentLine($paydate, $amount, $description='', $method='') {
  $amount = sprintf('%01.2f', $amount);
  $aReceipt['payments'][] = array(
    'date'        => $paydate,
    'method'      => $method,
    'description' => $description,
    'amount'      => $amount,
  );
  $aReceipt['total_payments'] += $amount;
}

// Generate a receipt data array from the last-billed invoice for this patient,
// or from the specified encounter.
//
function generateReceiptArray($patient_id, $encounter=0) {

  // Get the most recent invoice data or that for the specified encounter.
  $query = "SELECT " .
    "fe.id, fe.date, fe.encounter, fe.facility_id, fe.invoice_refno, " .
    "u.fname, u.mname, u.lname " .
    "FROM form_encounter AS fe " .
    "LEFT JOIN users AS u ON u.id = fe.provider_id " .
    "WHERE fe.pid = '$patient_id' ";
  if ($encounter) {
    $query .= "AND encounter = '$encounter'";
  } else {
    $query .= "ORDER BY id DESC LIMIT 1";
  }
  $ferow = sqlQuery($query);
  if (empty($ferow)) die(xl("This patient has no activity."));
  $trans_id = $ferow['id'];
  $encounter = $ferow['encounter'];
  $svcdate = substr($ferow['date'], 0, 10);
  $invoice_refno = $ferow['invoice_refno'];
  $docname = '';
  if (!empty($ferow['fname'])) $docname = trim($ferow['fname']);
  if (!empty($ferow['mname'])) {
    if ($docname) $docname .= ' ';
    $docname .= trim($ferow['fname']);
  }
  if (!empty($ferow['lname'])) {
    if ($docname) $docname .= ' ';
    $docname .= trim($ferow['lname']);
  }

  // Get details for the visit's facility.
  $frow = sqlQuery("SELECT * FROM facility WHERE " .
    "id = '" . $ferow['facility_id'] . "'");

  $patdata = getPatientData($patient_id, 'fname,mname,lname,pubpid,street,city,state,postal_code');

  // Get text for the logged-in user's name (first middle last).
  $username = "UID: " . $_SESSION["authUserID"];
  $userrow = sqlQuery("SELECT id, fname, mname, lname FROM users " .
    "WHERE id = '" . $_SESSION["authUserID"] . "'");
  if ($userrow['id']) {
    if (!empty($userrow['fname'])) $username = $userrow['fname'];
    if (!empty($userrow['mname'])) {
      if (!empty($username)) $username .= ' ';
      $username .= $userrow['mname'];
    }
    if (!empty($userrow['lname'])) {
      if (!empty($username)) $username .= ' ';
      $username .= $userrow['lname'];
    }
  }

  // Compute numbers for summary on right side of page.
  $head_begbal = get_patient_balance($patient_id, $encounter);
  $row = sqlQuery("SELECT SUM(fee) AS amount FROM billing WHERE " .
    "pid = '$patient_id' AND encounter = '$encounter' AND activity = 1 AND " .
    "code_type != 'COPAY'");
  $head_charges = $row['amount'];
  $row = sqlQuery("SELECT SUM(fee) AS amount FROM drug_sales WHERE " .
    "pid = '$patient_id' AND encounter = '$encounter'");
  $head_charges += $row['amount'];
  $row = sqlQuery("SELECT SUM(pay_amount) AS payments, " .
    "SUM(adj_amount) AS adjustments FROM ar_activity WHERE " .
    "pid = '$patient_id' AND encounter = '$encounter'");
  $head_charges -= $row['adjustments'];
  $head_payments = $row['payments'];
  $row = sqlQuery("SELECT SUM(fee) AS amount FROM billing WHERE " .
    "pid = '$patient_id' AND encounter = '$encounter' AND activity = 1 AND " .
    "code_type = 'COPAY'");
  $head_payments -= $row['amount'];
  $head_endbal = $head_begbal + $head_charges - $head_payments;

  $aReceipt = array(
    'encounter_id'      => $encounter,
    'encounter_date'    => $svcdate,
    'invoice_refno'     => $invoice_refno,
    'patient_id'        => $patient_id,
    'patient_pubpid'    => $patdata['pubpid'],
    'patient_fname'     => $patdata['fname'],
    'patient_mname'     => $patdata['mname'],
    'patient_lname'     => $patdata['lname'],
    'patient_street'    => $patdata['street'],
    'patient_city'      => $patdata['city'],
    'patient_state'     => $patdata['state'],
    'patient_zip'       => $patdata['postal_code'],
    'facility_id'       => $frow['id'],
    'facility_name'     => $frow['name'],
    'facility_street'   => $frow['street'],
    'facility_city'     => $frow['city'],
    'facility_state'    => $frow['state'],
    'facility_zip'      => $frow['postal_code'],
    'facility_phone'    => $frow['phone'],
    'docname'           => $docname,
    'username'          => $username,
    'starting_balance'  => $head_begbal,
    'ending_balance'    => $head_endbal,
    'items'             => array(),
    'payments'          => array(),
    'total_price'       => 0,
    'total_quantity'    => 0,
    'total_charges'     => 0,
    'total_adjustments' => 0,
    'total_totals'      => 0,
    'total_payments'    => 0,
  );

  // Create array aAdjusts from ar_activity rows for $inv_encounter.
  $aReceipt['_adjusts'] = array();
  $ares = sqlStatement("SELECT " .
    "a.payer_type, a.adj_amount, a.memo, a.code_type, a.code, " .
    "s.session_id, s.reference, s.check_date " .
    "FROM ar_activity AS a " .
    "LEFT JOIN ar_session AS s ON s.session_id = a.session_id WHERE " .
    "a.pid = '$patient_id' AND a.encounter = '$encounter' AND a.adj_amount != 0");
  while ($arow = sqlFetchArray($ares)) $aReceipt['_adjusts'][] = $arow;

  // Product sales
  $inres = sqlStatement("SELECT s.sale_id, s.sale_date, s.fee, " .
    "s.quantity, s.drug_id, d.name " .
    "FROM drug_sales AS s LEFT JOIN drugs AS d ON d.drug_id = s.drug_id " .
    "WHERE s.pid = '$patient_id' AND s.encounter = '$encounter' " .
    "ORDER BY s.sale_id");
  while ($inrow = sqlFetchArray($inres)) {
    receiptArrayDetailLine($aReceipt, 'PROD', $inrow['drug_id'], $inrow['name'],
      $inrow['quantity'], $inrow['fee']);
  }

  // Service items.
  $inres = sqlStatement("SELECT * FROM billing WHERE " .
    "pid = '$patient_id' AND encounter = '$encounter' AND " .
    "code_type != 'COPAY' AND code_type != 'TAX' AND activity = 1 " .
    "ORDER BY id");
  while ($inrow = sqlFetchArray($inres)) {
    receiptArrayDetailLine($aReceipt, $inrow['code_type'], $inrow['code'],
      $inrow['code_text'], $inrow['units'], $inrow['fee']);
  }

  // Write any adjustments left in the aAdjusts array.
  foreach ($aReceipt['_adjusts'] as $arow) {
    if ($arow['adj_amount'] == 0) continue;
    // $payer = empty($arow['payer_type']) ? 'Pt' : ('Ins' . $arow['payer_type']);
    receiptArrayDetailLine($aReceipt, '', xl('Adjustment'),
      getAdjustTitle($arow['memo']), 1, 0 - $arow['adj_amount']);
  }

  // Tax items.
  $inres = sqlStatement("SELECT * FROM billing WHERE " .
    "pid = '$patient_id' AND encounter = '$encounter' AND " .
    "code_type = 'TAX' AND activity = 1 " .
    "ORDER BY id");
  while ($inrow = sqlFetchArray($inres)) {
    receiptArrayDetailLine($aReceipt, $inrow['code_type'], $inrow['code'],
      $inrow['code_text'], 1, $inrow['fee']);
  }

  $payments = 0;

  // Get co-pays.
  $inres = sqlStatement("SELECT fee, code_text FROM billing WHERE " .
    "pid = '$patient_id' AND encounter = '$encounter' AND " .
    "code_type = 'COPAY' AND activity = 1 AND fee != 0 " .
    "ORDER BY id");
  while ($inrow = sqlFetchArray($inres)) {
    $payments -= sprintf('%01.2f', $inrow['fee']);
    receiptArrayPaymentLine($aReceipt, $svcdate, 0 - $inrow['fee'],
      $inrow['code_text'], 'COPAY');
  }

  // Get other payments.
  $inres = sqlStatement("SELECT " .
    "a.code, a.modifier, a.memo, a.payer_type, a.adj_amount, a.pay_amount, " .
    "s.payer_id, s.reference, s.check_date, s.deposit_date " .
    "FROM ar_activity AS a " .
    "LEFT JOIN ar_session AS s ON s.session_id = a.session_id WHERE " .
    "a.pid = '$patient_id' AND a.encounter = '$encounter' AND " .
    "a.pay_amount != 0 " .
    "ORDER BY s.check_date, a.sequence_no");
  $payer = empty($inrow['payer_type']) ? 'Pt' : ('Ins' . $inrow['payer_type']);
  while ($inrow = sqlFetchArray($inres)) {
    $payments += sprintf('%01.2f', $inrow['pay_amount']);
    receiptArrayPaymentLine($aReceipt, $svcdate, $inrow['pay_amount'],
      $payer . ' ' . $inrow['reference'], $inrow['memo']);
  }

  return $aReceipt;
}
?>
