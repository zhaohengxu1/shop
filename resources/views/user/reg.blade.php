@extends('layouts.bootstrap')

<title>用户注册</title>

@section('content')
    <body>
    <form class="form-signin" action="/userreg" method="post">
        {{csrf_field()}}
        <h2 class="form-signin-heading">User Register</h2>

        <label for="inputUserName">Username</label>
        <input type="text" name="u_name" id="inputUserName" class="form-control" required autofocus>

        <label for="inputPwd">Password</label>
        <input type="password" name="u_pwd" id="inputPwd" class="form-control" required autofocus>

        <label for="inputConfirmPwd">Confirm Password</label>
        <input type="password" name="u_qpwd" id="inputConfirmPwd" class="form-control"  required autofocus>

        <label for="inputEmail">Email</label>
        <input type="text" name="u_email" id="inputEmail" class="form-control" required autofocus>

        <label for="inputAge">Age</label>
        <input type="text" name="u_age" id="inputAge" class="form-control" required autofocus>

        <button style="width: 200px;" class="btn btn-lg btn-primary btn-block" type="submit">Register</button>
    </form>
    </body>
@endsection



