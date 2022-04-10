$('.btn-change').click(function(){
    if($(this).attr('id') == 'btn-change-notclicked'){
        $(this).attr('id','btn-change-clicked');
        $(this).text('適用');
        $('.check-box').attr('disabled',false);
    }
    else{
        $(this).attr('id','btn-change-notclicked');
        $(this).text('変更する');
        $('.check-box').attr('disabled',true);
        var arr = [];
        var index = 0;
        $('.teacher-list').each(function(){
            var data = {};

            data['id'] = $(this).children('#teacher-id').text();
            var sum = 2;
            $(this).find('input').each(function(){
                if($(this).prop('checked')){
                    sum = sum | $(this).val();
                }
            })
            data['value'] = sum;
            arr[index] = data;
            index += 1;
        })
        $.ajax({
            url: "../../administrator/teacherlist/ajax.php",
            type: "POST",
            cache: false,
            dataType: "json",
            data: {
            param: arr
            },
            success: function(){
            },
            error: function(){     
            }
        });
    }
});
