<h3>扫码支付</h3>
<div id="qrcode"></div>
<input type="hidden" id="code_url" value="{{$code_url}}">
<input type="hidden" id="order_num" value="{{$order_num}}">

<script src="{{URL::asset('/js/qrcode.js')}}"></script>
<script src="{{URL::asset('/js/jquery-3.2.1.min.js')}}"></script>


<script>

    setInterval(function(){

        var order_num = $('#order_num').val();

        $.ajax({
            headers:{
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url : '/wechat/pay/find',
            type: 'post',
            data:{order_num:order_num},
            success : function(res){
                if(res==1){
                    alert('支付成功');
                    location.href="/allorders";
                }
            },
            dataType:'json',
        })

    },3000);

    var code_url = $('#code_url').val();

    var qrcode = new QRCode('qrcode',{
        text : code_url,
        width:300,
        height:300,
        colorDark:'#000000',
        colorLight:'#ffffff',
        correctLevel:QRCode.CorrectLevel.H
    });

</script>