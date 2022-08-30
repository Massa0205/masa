$(document).ready(function(){
    $('.type-select li').click(function(){
        var reportType = $(this).attr("id");
        $('.type-select li').css({"background-color":"rgba(0,0,0,0)","color":"silver"});
        $(this).css({"background-color":"rgba(0,0,0,0.2)","color":"white"});
        $('.newreports').fadeOut("slow").queue(function(){
            $(this).remove();
        })
        $.ajax({
            url: "../../functions/search/ajax.php",
                type: "POST",
                cache: false,
                dataType:"json",
                data:{
                    type:reportType,company:companyCode
                },
            success:function(data){
                for(var i=0;i<data.length;i+=1){
                    $('.type-select').after('<div class="newreports"><ul class="a1"><li class="w2"><p class="reptype">'+data[i].REPORTTYPE+'</p></li></ul><ul class="a2"><li class="w2" id="str">投稿者:</li><li class="w2"><p class="department">'+'　'+data[i].DEPARTMENT+'　</p></li><li class="w2"><p class="studentname">  '+data[i].STUDENTNAME+'</p></li><div class = "reportdate"><li class="w2"><p class="reportdate">'+data[i].POSTDATE+'</p></li></div></ul><p class="report_impression">'+data[i].IMPRESSION+'</p></div>');
                }
                $('.newreports').hide().fadeIn("slow");
            },
            error:function(){
            }
        })
    })
});
