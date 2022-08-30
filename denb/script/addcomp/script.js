$(function(){
    /**
     * 
     * 
     */
    $(document).on('input', '#ajaxSearch', function(e) {
        $('.submit-btn').prop('disabled',false);
        var compname = $('#ajaxSearch').val();
        $.ajax({
            url: "../functions/addcompany/ajax.php",
                type: "POST",
                cache: false,
                dataType:"json",
                data:{
                    param:compname
                },
            success:function(data){
                $('.company-name-label').remove();
                for(var i=0;i<data.length;i+=1){
                    $('.company-list').append('<p class="company-name-label">' + data[i].COMPANYNAME + '</p>')
                    console.log(data[i].COMPANYNAME);
                }
            },
            error:function(){
                $('.company-name-label').remove();
            }
        })
    })
});


