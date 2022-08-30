
    /*****モーダル表示clickイベント*****/
    $('.modal-wrapper').click(function(){
        $('.block-1').hide();
        $('.block-2').hide();
        $('.modal-wrapper').fadeOut();                    
    });
    $(document).ready(function(){
        $('.block-1').hide();
        $('.block-2').hide();
        $('.stu').click(function(){
            var name = $('#'+$(this).parent().attr("id")+' #student-name').text();
            var elem = $(this);
            $('.modal-student-name').text(name);
            $('.bodyy').css({'-ms-filter':'blur(7px)','filter':'blur(7px)'});
            var qu = $.when(
                $('.modal-wrapper').fadeIn("slow")
            );
            //.queue(function()
            qu.done(function(){
                $('.block-1').fadeIn("slow");
                $('.block-2').fadeIn("slow");
                modalAjax(elem.parent().attr("id"));
            })
        });
        $('#closebtn').click(function(){
            $('.modal-wrapper').fadeOut();
            $('.bodyy').css({'-ms-filter':'blur(0px)','filter':'blur(0px)'});

        })
        $('.pass').click(function(){

        })
        $('.rows').hover(function(){
            $(this).find('.stu').css("background-color","rgba(0, 162, 255,0.1)");
            $(this).find('.pass').css("background-color","rgba(0, 162, 255,0.1)");
        },
        function(){
            $(this).find('.stu').css("background-color","rgba(0, 0, 0, 0)");
            $(this).find('.pass').css("background-color","rgba(0, 0, 0, 0)");
        });
        function modalAjax(user){
            $('#myPieChart').remove();
            $('.modal-reports').remove();
            $('.chart-area').append('<canvas id="myPieChart"></canvas>');

            var User = user;
            $.ajax({
                    url : "../../functions/teacher/ajax.php",
                    type: "POST",
                    data:{student:User},
                    dataType:"json",
                success:function(data){
                    $('.label-first-date').text('最初の活動'+data["firstdate"]);
                    var reps = data["reports"];
                    for(var i=0;i<reps.length;i++){
                        $('.student-reports').append('<ul class="modal-reports"><div class="modal-reports-top"><div class="mdoal-reports-top-info"><li class="kigyomei">'+reps[i]["company_name"]+'</li><li class="syurui">'+reps[i]["report_type"]+'</li></div><li class="hizuke">'+reps[i]["reprt_date"]+'</li></div><li class="naiyo">'+reps[i]["impression"]+'</li></ul>');
                        
                    }
                    drawGraph(data);    
                },
                error:function(){
                    $('.label-first-date').text('記録なし');
                }
            })
        }
        function drawGraph(sender){
            var ctx = document.getElementById("myPieChart");
            var myPieChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ["説明会", "面談", "面接", "試験","インターン"],
                    datasets: [{
                        backgroundColor: [
                            "#F57A62",
                            "#FCDB6A",
                            "#7EE66C",
                            "#7DE6FF",
                            "#B782F5"
                        ],
                        data: [sender["cis"], sender["interview"], sender["interview2"], sender["exam"],sender["intern"]]
                    }]
                },

                options: {
                    title: {
                        display: true,
                        text: '報告書 割合',
                        
                    },
                    cutoutPercentage: 50

                }

            });
        }
    });

            //=========== 円グラフ ============//