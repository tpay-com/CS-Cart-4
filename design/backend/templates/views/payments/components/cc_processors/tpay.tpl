<head><meta http-equiv="content-type" content="text/html; charset=utf-8"></head>

<div class="form-field">
    <label for="seller_id">Identyfikator sprzedawcy:</label>
    <input type="text" name="payment_data[processor_params][seller_id]" id="seller_id" value="{$processor_params.seller_id}" class="input-text" />
</div>

<div class="form-field">
    <label for="key">Klucz:</label>
    <input type="text" name="payment_data[processor_params][key]" id="key" value="{$processor_params.key}" class="input-text" />
</div>
<div class="form-field">
    <label  for="view">Miejsce wyboru kanałów płatności:</label>
    <div class="form-field">
        <select name="payment_data[processor_params][view]" id="view">
            <option value="0" {if $processor_params.view == "0"}selected="selected"{/if}>Sklep</option>
            <option value="1" {if $processor_params.view == "1"}selected="selected"{/if}>Tpay.com</option>
        </select>
    </div>
</div>
