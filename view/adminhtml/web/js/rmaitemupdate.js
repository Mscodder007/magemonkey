define([
    "jquery",
    "jquery/ui"
], function($){
    "use strict";
 
    function main(config, element) {
        var $element = $(element);
        var rmaUrl = config.rmaurl;
        var CurrentProduct = config.CurrentProduct;
         
        $(document).ready(function(){
            
            $(document).on('click','.save_rma',function(){
                
                let rmaId = $(this).attr('data-rma-id');
                let rmaItemId = $(this).attr('data-item-id');
                let status = $('.reason_'+rmaItemId+'_status').val();
                let remark = $('.rma_item_'+rmaItemId+'_remark').val();
                
                $.ajax({
                 url: rmaUrl,
                 type: 'POST',
                 dataType: 'json',
                 showLoader:true,
                 data: {rma_id:rmaId,rma_item_id:rmaItemId,status:status,remark:remark},
                success: function(response) {
                   console.log('Response',response.message);
                },
                error: function (xhr, status, errorThrown) {
                     console.log(errorThrown);
                 }
                });
             });
        });
 
 
    };
    return main;
});
