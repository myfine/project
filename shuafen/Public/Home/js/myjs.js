

$(function(){
        $('#denglu').click(function(){
            var username = $('#username').val();
            var password = $('#password').val();
            if(!username){
                $('#userinfo').css('display','block');
                return false;
            }

            if(!password){
                $('#passwordinfo').css('display','block');
                return false;
            }

            var url = "/index.php/Login/doLogin";
            $.post(url,{'username':username,'password':password},function(data){
                 console.log(data);
                  if(data == 1){
                        alert('成功');
                  }
            })

        })

})