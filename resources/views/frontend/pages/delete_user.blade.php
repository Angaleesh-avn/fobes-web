@extends('frontend.layouts.app')

@section('description')
@php
    $data = metaData('home');
@endphp
{{ $data->description }}
@endsection
@section('og:image')
{{ asset($data->image) }}
@endsection
@section('title')
{{ $data->title }}
@endsection

@section('main')


<div class="modal-content">
    <div class="modal-header flex-column">
        <div class="icon-box">
            <i class="material-icons">&#xE5CD;</i>
        </div>
        <h4 class="modal-title w-100">Are you sure?</h4>
    </div>
    <div class="modal-body">
        <p>Do you really want to delete these records? This process cannot be undone.</p>
    </div>
    <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" onclick="confirmDelete('{{$id}}')">Delete</button>
    </div>
</div>


@endsection

@section('css')
<style>
    body {
        font-family: 'Varela Round', sans-serif;
    }



    .modal-content {
        padding: 20px;
        border-radius: 5px;
        border: none;
        text-align: center;
        font-size: 14px;
    }

    .modal-header {
        border-bottom: none;
        position: relative;
    }

    h4 {
        text-align: center;
        font-size: 26px;
        margin: 30px 0 -10px;
    }


    .modal-body {
        color: #999;
    }

    .modal-footer {
        border: none;
        text-align: center;
        border-radius: 5px;
        font-size: 13px;
        padding: 10px 15px 25px;
    }

    .modal-footer a {
        color: #999;
    }

    .icon-box {
        width: 80px;
        height: 80px;
        margin: 0 auto;
        border-radius: 50%;
        z-index: 9;
        text-align: center;
        border: 3px solid #0033A0;
    }

    .icon-box i {
        color: #f15e5e;
        font-size: 46px;
        display: inline-block;
        margin-top: 13px;
    }

    .modal-content .btn1,
    .modal-content .btn1:active {
        color: #fff;
        border-radius: 4px;
        background: #60c7c1;
        text-decoration: none;
        transition: all 0.4s;
        line-height: normal;
        min-width: 120px;
        border: none;
        min-height: 40px;
        border-radius: 3px;
        margin: 0 5px;
    }

    .btn-secondary {
        background: #c1c1c1;
    }

    .btn-secondary:hover,
    .btn-secondary:focus {
        background: #a8a8a8;
    }

    .btn-danger {
	background: #0033A0;
border: none;
    }

    .btn-danger:hover,
    .btn-danger:focus {
        background: #ee3535;
    }

    .trigger-btn {
        display: inline-block;
        margin: 100px auto;
    }
</style>
@endsection
@section('script')
<script>
    function confirmDelete(token) {
        console.log(token)
        const myHeaders = new Headers();
        myHeaders.append("Authorization", `Bearer ${token}`);

        const requestOptions = {
            method: "DELETE",
            headers: myHeaders,
            redirect: "follow"
        };
        fetch("https://backend.fobes.in/api/candidates", requestOptions)
            .then((response) => {
                response.text();
                if (response.status == 200) {
                    window.location.href = "{{ url('/') }}";
                } else {
                    alert("User not found")
                }
            })
            .catch((error) => {
                console.error(error)
            });
    }
</script>
@endsection
