
$('.report-box').click(function(){
    window.alert();
})
$('.change-pass').click(function(){
    $('.change-pass-frm').fadeIn();
})
$('.btn-changepass').click(function(){
    if($(this).attr('id')=='notclicked'){
        $(this).attr('id','clicked');
        $('#set-pass').slideDown();

    }
    else{
        $(this).attr('id','notclicked');
        $('#set-pass').slideUp();
    }
})
$('.logout-button').click(function(){
    console.log('clicked');
    location.href = "../logout/";
})
$('.change-pass-submit').click(function(){
    $('.change-pass-frm').fadeOut();
    $('.change-pass-frm').css('display','none');
})
