jQuery(document).ready(function()
{
    jQuery(".cegg-price-alert-wrap form").submit(function(event)
    {
        event.preventDefault();
        var $form = jQuery(this);
        var $wrap = $form.closest('.cegg-price-alert-wrap');
        var data = $form.serialize() + '&nonce=' + ceggPriceAlert.nonce;
        $wrap.find('.cegg-price-loading-image').show();
        $wrap.find('.cegg-price-alert-result-error').hide();  
        $form.find('input, button').prop("disabled", true);                    
        jQuery.ajax({
            url: ceggPriceAlert.ajaxurl + '?action=start_tracking',
            type: 'post',
            dataType: 'json',
            data: data,
            success: function(result) {
                if (result.status == 'success')
                {
                    $wrap.find('.cegg-price-alert-result-succcess').show();
                    $wrap.find('.cegg-price-alert-result-succcess').html(result.message);                    
                    $wrap.find('.cegg-price-loading-image').hide();
                }
                else {                    
                    $form.find('input, button').prop("disabled", false);                    
                    $wrap.find('.cegg-price-alert-result-error').show();
                    $wrap.find('.cegg-price-alert-result-error').html(result.message);
                    $wrap.find('.cegg-price-loading-image').hide();                    
                }
            }
        });

    });
});
