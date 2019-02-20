@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <form action="/pdfadd" method="post" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <input type="file" name="pdf">
                    <input type="submit" value="UPLOAD">
                </form>
            </div>
        </div>
    </div>
@endsection
