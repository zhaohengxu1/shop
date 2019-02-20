@extends('layouts.bootstrap')

<title>用户登录</title>

@section('content')

    <body>
        <form action="/userlogin" method="post">
            {{csrf_field()}}
            <table class="table table-bordered" style="width: 300px;">
                <h2>Login</h2>
                <tr>
                    <td width="100px">Email:</td>
                    <td><input type="text" name="u_email"></td>
                </tr>
                <tr>
                    <td>Password:</td>
                    <td><input type="password" name="u_pwd"></td>
                </tr>
            </table>
            <input class="btn btn-danger" type="submit" value="Login">
            <button class="btn btn-danger" ><a href="/userreg" style="text-decoration: none;color: white;">Go register!</a></button>
        </form>
    </body>
@endsection
