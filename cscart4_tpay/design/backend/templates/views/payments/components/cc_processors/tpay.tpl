<script type="text/javascript">
    $(document).ready(function () {
        jQuery("#view").change(function () {


            if ($("#view option:selected").val() == "1") {
                
                  $("#channels").attr("style", "visibility: hidden")

            }
            else {
              
                $("#channels").attr("style", "visibility: ")

            }

        });


    });
</script>

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
 {if $processor_params.view == "1"}      
<div class="form-field" id="channels" style="visibility: hidden">
    <label  for="channels">Widok kanałów płatności</label>
    <div class="form-field">
        <select name="payment_data[processor_params][channels]" >
            <option value="0" {if $processor_params.channels == "0"}selected="selected"{/if}>Kafelki</option>
            <option value="1" {if $processor_params.channels == "1"}selected="selected"{/if}>Lista</option>
        </select>
    </div>
</div>
 {else}
     
  <div class="form-field" id="channels">
    <label  for="channels">Widok kanałów płatności</label>
    <div class="form-field">
        <select name="payment_data[processor_params][channels]" >
            <option value="0" {if $processor_params.channels == "0"}selected="selected"{/if}>Kafelki</option>
            <option value="1" {if $processor_params.channels == "1"}selected="selected"{/if}>Lista</option>
        </select>
    </div>
</div>
     
   {/if}