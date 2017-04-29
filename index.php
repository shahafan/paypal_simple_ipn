<?php
require 'Paypal.php';

$my_url = 'http://localhost/'; // the url of your site
$order_id = 1; // the order id


$p = new Paypal;             // initiate an instance of the class
//$p->paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';   // testing paypal url
$p->paypal_url = 'https://www.paypal.com/cgi-bin/webscr';     // paypal url

// if there is not action variable, set the default action of 'process'
if (empty($_GET['action'])) $_GET['action'] = 'process';

switch ($_GET['action']) {

  case 'process':      // Process the order...

    header('Content-Type: text/html; charset=utf-8');

    $key = md5(date("Y-m-d:").rand());

    $p->add_field('charset', 'utf-8');
    $p->add_field('business', 'yourpaypal@email.com');
    $p->add_field('return', $my_url.$order_id.'?action=success');
    $p->add_field('cancel_return', $my_url.$order_id.'?action=cancel');
    $p->add_field('notify_url', $my_url.$order_id.'?action=ipn');

    $p->add_field('currency_code', 'ILS');
    $p->add_field('key', $key);
    $p->add_field('upload', '1');
    $p->add_field('item_number_1', $order_id);

    $p->add_field('item_name_1', 'some name');
    $p->add_field('amount_1', 80);
    $p->add_field('quantity_1', 1);

    $p->submit_paypal_post(); // submit the fields to paypal
    //$p->dump_fields();      // for debugging, output a table of all the fields
    break;

  case 'success':      // Order was successful...

    // redirected  here after successful payment

    break;

  case 'cancel':       // Order was canceled...

    // redirected  here after canceled payment

    break;

  case 'ipn':          // Paypal is calling page for IPN validation...

    if ($p->validate_ipn()) {

      $PaymentStatus =  $p->ipn_data['payment_status'];

      if($PaymentStatus == 'Completed'){
        // payment success - update database
      }else if($PaymentStatus == 'Pending'){
        // payment in progress - update database
      }else{
        // payment was canceled - update database
      }

    }

    break;

}
