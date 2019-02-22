<form action="/wechat/form" method="post" enctype="multipart/form-data">
    {{csrf_field()}}
    <input type="file" name="media">
    <input type="submit" value="添加">
</form>