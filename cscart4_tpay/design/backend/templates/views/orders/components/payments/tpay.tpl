{if $payment_info.processor_params['view']=='0'}
<h2 class="subheader">Tpay.com</h2>
<div  class="form-field" id="regulamin">
<label for="payment_info[akceptuje_regulamin]"><a href="https://secure.tpay.com/regulamin.pdf" target="_blank">Akceptacja regulaminu </a></label>
<input type="checkbox" checked name="payment_info[akceptuje_regulamin]" />
</div>

    
    {if $payment_info.processor_params['channels']=='0'}
    <style type="text/css">                 
    .checked {
        box-shadow: 0px 0px 10px 3px #15428F;

    }
    .check {
        display: inline-block; 
        width: 145px; 
        height:75px; 
        margin: 13px 45px 17px 0; 
        text-align:center;
    }
    </style>   
    <div class="form-field" id="kanal">
    </div>
    <input type="hidden" id="channel"  name="payment_info[kanal]" value=" ">
    {literal}
    <script type="text/javascript">
   function ShowChannelsCombo()
    {
        var str = '';

        for (var i = 0; i < tr_channels.length; i++) {
            str += '<div class="check" id="' + tr_channels[i][0] + '"><img  src="' + tr_channels[i][3] + '"></div>';
        }

        var container = jQuery("#kanal");
        container.append(str);

        $(document).ready(function () {
            jQuery(".check").click(function () {
                $(".check").removeClass("checked");
                $(this).addClass("checked");
                $("html,body").animate({scrollTop: 2600}, 600);
                var kanal = 0;
                kanal = $(this).attr("id");
                $('#channel').val(kanal);

            });

            jQuery("form[name=payments_form_tab2]").submit(function (e) {

                if ($('#channel').attr("value") == " ") {

                    alert("Wybierz bank");
                    return false;

                }
                else {
                    return true;
                }
              });
        });


    }
    jQuery.getScript("https://secure.tpay.com/channels-{/literal}{$payment_info.processor_params['seller_id']}{literal}0.js", function () {
        ShowChannelsCombo()
    });
    </script>
{/literal}
    
    
    
    
    
    {else}

{literal}
<div  style="margin:20px 0 30px 0" class="form-field" id="kanal">
    <label for="customer_signature" class="cm-required">Wybierz bank:</label>
</div>
    
    <script type="text/javascript">
        function ShowChannelsCombo() {
            var str = '<select name="payment_info[kanal]">';
            for (var i = 0; i < tr_channels.length; i++) {
                str += '<option value="' + tr_channels[i][0] + '">' + tr_channels[i][1] + '</option>';
            }
            str += "</select>";
            var kanal = $("#kanal");
            kanal.append(str);
        }
        $.getScript("https://secure.tpay.com/channels-{/literal}{$payment_info.processor_params['seller_id']}{literal}0.js", function () {
            ShowChannelsCombo();
        });
        if(typeof(tr_channels !== 'undefined')){
            ShowChannelsCombo();
        }
    </script>
{/literal}
{/if}
{else}
{/if}