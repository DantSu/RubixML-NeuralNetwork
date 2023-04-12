@extends('templates/layout')

@section('content')
    <h1>Success : {{$score * 100}}%</h1>

    {!! $stackTrace !!}
@endsection
