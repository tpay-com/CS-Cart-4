<?php
use Tygh\Registry;
if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == "place_order") {
    $paymentId = $_REQUEST['payment_id'];
    $processor_data = fn_get_payment_method_data($paymentId);
    $orderId = $order_info['order_id'];
    $orderData = fn_get_order_info($orderId);
   
    $current_location = Registry::get('config.http_location');
    $return = $current_location . "/$index_script?dispatch=payment_notification.notify&payment=tpay";
    $return_url = $current_location . "/$index_script?dispatch=payment_notification.notify&payment=tpay";
    $confData = $orderData['payment_method']['processor_params'];

    $data['seller_id'] = $confData['seller_id'];
    $data['kwota'] = $orderData['total'];
    $data['opis'] = "Zamówienie " . $orderId;
    $data['email'] = $orderData['email'];
    $data['nazwisko'] = $orderData['lastname'];
    $data['imie'] = $orderData['firstname'];
    $data['adres'] = $orderData['b_address'] . $orderData['b_address2'];
    $data['miasto'] = $orderData['b_city'];
    $data['kraj'] = $orderData['b_country'];
    $data['kod'] = $orderData['b_zipcode'];
    $data['jezyk'] = CART_LANGUAGE;
    $data['crc'] = base64_encode($orderId);
    $data['md5sum'] = md5($data['seller_id'] . $data['kwota'] . $data['crc'] . $confData['key']);
    $data['telefon'] = $orderData['b_phone'];
    $data['pow_url'] = $return_url . "&success=true&order=" . $orderId;
    $data['pow_url_blad'] = $return_url . "&success=false&order=" . $orderId;
    $data['wyn_url'] = $return . "&validate=true";
    $data['kanal'] = $_REQUEST['payment_info']['kanal'];
    $data['akceptuje_regulamin'] = $_REQUEST['payment_info']['akceptuje_regulamin'] ? '<input type="hidden" name="akceptuje_regulamin" value="1" />' : '';
        
    $form = <<<FORM
    <form action="https://secure.tpay.com/" method="post" id="tr_payment" name="tr_payment">
        <input type="hidden" name="id" value="{$data['seller_id']}">
        <input type="hidden" name="kwota" value="{$data['kwota']}">
        <input type="hidden" name="opis" value="{$data['opis']}">
        <input type="hidden" name="crc" value="{$data['crc']}">
        <input type="hidden" name="wyn_url" value="{$data['wyn_url']}">
        <input type="hidden" name="pow_url" value="{$data['pow_url']}">
        <input type="hidden" name="pow_url_blad" value="{$data['pow_url_blad']}">
        <input type="hidden" name="email" value="{$data['email']}">
        <input type="hidden" name="nazwisko" value="{$data['nazwisko']}">
        <input type="hidden" name="imie" value="{$data['imie']}">
        <input type="hidden" name="adres" value="{$data['adres']}">
        <input type="hidden" name="miasto" value="{$data['miasto']}">
        <input type="hidden" name="kod" value="{$data['kod']}">
        <input type="hidden" name="kraj" value="{$data['kraj']}">
        <input type="hidden" name="telefon" value="{$data['telefon']}">
        <input type="hidden" name="md5sum" value="{$data['md5sum']}">
        <input type="hidden" name="kanal" value="{$data['kanal']}">
        <input type="hidden" name="jezyk" value="{$data['jezyk']}">
        {$data['akceptuje_regulamin']}
    </form>
  <script type="text/javascript">
        document.getElementById('tr_payment').submit();
    </script>

FORM;

    fn_change_order_status($orderId, "O");
    echo $form;
    exit;
} else {
    if ($_REQUEST['success'] == "true") {
        $orderId = $_REQUEST['order'];

        fn_order_placement_routines('route', $orderId);
    } else if ($_REQUEST['success'] == "false") {
        $orderId = $_REQUEST['order'];
        fn_change_order_status($orderId, "F");
        fn_order_placement_routines('route', $orderId);
    } else if ($_REQUEST['validate'] == "true") {
        echo "TRUE";
		$ip_table=array(
		'195.149.229.109',
		'148.251.96.163',
		'178.32.201.77',
		'46.248.167.59',
		'46.29.19.106'
		);
        if (isset($_POST['tr_id']) && !empty($_POST['tr_id']) && in_array($_SERVER['REMOTE_ADDR'], $ip_table)) {
            $orderId = base64_decode($_POST['tr_crc']);
            $sellerId = $_POST['id'];
            $transactionStatus = $_POST['tr_status'];
            $transactionId = $_POST['tr_id'];
            $total = $_POST['tr_amount'];
            $error = $_POST['tr_error'];
            $crc = $_POST['tr_crc'];
            $data['checksum'] = $_POST['md5sum'];
               

            $sum = md5($sellerId . $transactionId . $total . $crc . $confData['key']);


            $orderData = fn_get_order_info($orderId);
            $confData = $orderData['payment_method']['processor_params'];
        
            if ( $sum == $data['checksum']) {
              
                if ($_POST['tr_status'] == 'TRUE' && $_POST['tr_error'])
                    $pp_response['order_status'] = 'P';
                else
                    $pp_response['order_status'] = 'F';
                $pp_response['reason_text'] = 'Transakcja ' . $_POST['tr_id'];
                fn_finish_payment($orderId, $pp_response, false);
                fn_order_placement_routines($orderId);
                
            }
        }
    }
    
    exit;
}
?>