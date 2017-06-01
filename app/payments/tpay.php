<?php

use Tygh\Registry;

include_once 'tpay/_class_tpay/exception.php';
include_once 'tpay/_class_tpay/lang.php';
include_once 'tpay/_class_tpay/paymentBasic.php';
include_once 'tpay/_class_tpay/util.php';
include_once 'tpay/_class_tpay/validate.php';
if (!defined('BOOTSTRAP')) {
    die('Access denied');
}


if ($mode == "place_order") {

    $paymentId = $_REQUEST['payment_id'];
    $processor_data = fn_get_payment_method_data($paymentId);
    $orderId = $order_info['order_id'];
    $orderData = fn_get_order_info($orderId);
    $confData = $orderData['payment_method']['processor_params'];
    $tpay = new tpay\PaymentBasic((int)$confData['seller_id'], $confData['key']);

    $current_location = Registry::get('config.http_location');
    $return = fn_url("payment_notification.result&payment=tpay");
    $return_url = fn_url("payment_notification.notify&payment=tpay");

    $data['kwota'] = $orderData['total'];
    $data['opis'] = "Zamówienie " . $orderId;
    $data['email'] = $orderData['email'];
    $data['nazwisko'] = $orderData['lastname'];
    $data['imie'] = $orderData['firstname'];
    $data['adres'] = $orderData['b_address'] . $orderData['b_address_2'];
    $data['miasto'] = $orderData['b_city'];
    $data['kraj'] = $orderData['b_country'];
    $data['kod'] = $orderData['b_zipcode'];
    $data['jezyk'] = strtoupper(CART_LANGUAGE);
    $data['crc'] = base64_encode($orderId);
    $data['telefon'] = $orderData['b_phone'];
    $data['pow_url'] = $return_url . "&success=true&order=" . $orderId;
    $data['pow_url_blad'] = $return_url . "&success=false&order=" . $orderId;
    $data['wyn_url'] = $return . "&validate=true";
    if (isset($_REQUEST['payment_info']['kanal'])) {
    $data['kanal'] = (int)$_REQUEST['payment_info']['kanal'];
    }
    if (isset($_REQUEST['payment_info']['regulamin'])) {
    $data['akceptuje_regulamin'] = $_REQUEST['payment_info']['regulamin'] === 'on' ? 1 : 0;
    }
    $form = $tpay->getTransactionForm($data);
    fn_change_order_status($orderId, "O");
    echo $form;
    echo '
    <script type=text/javascript>
    document.getElementById("tpay-payment").submit();
    </script>
    ';
    exit;
} else {
    if (isset($_REQUEST['success']) && $_REQUEST['success'] == "true") {
        $orderId = $_REQUEST['order'];

        fn_order_placement_routines('route', $orderId);
    } else {
        if (isset($_REQUEST['success']) && $_REQUEST['success'] == "false") {
            $orderId = $_REQUEST['order'];
            fn_change_order_status($orderId, "F");
            fn_order_placement_routines('route', $orderId);
        } else {
            if (isset($_REQUEST['validate']) && $_REQUEST['validate'] == "true") {

                $orderId = base64_decode($_POST['tr_crc']);
                $orderData = fn_get_order_info($orderId);
                $confData = $orderData['payment_method']['processor_params'];
                $tpay = new tpay\PaymentBasic((int)$confData['seller_id'], $confData['key']);
                $res = $tpay->checkPayment();

                if ((double)$orderData['total'] === (double)$res['tr_amount']) {
                    if ($res['tr_status'] == 'TRUE' && $res['tr_error'] === 'none') {
                        $pp_response['order_status'] = 'P';
                    } else {
                        $pp_response['order_status'] = 'F';
                    }

                    $pp_response['reason_text'] = 'Transakcja ' . $res['tr_id'];
                    if (isset($res['test_mode']) && $res['test_mode'] === 1) {
                        $pp_response['reason_text'] .= ' opłacona w trybie testowym!';
                    }
                    fn_finish_payment($orderId, $pp_response);
                    fn_order_placement_routines($orderId);
                }
            }
        }
    }

    exit;
}
?>
