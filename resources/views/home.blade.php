@extends('layouts.app')

@section('content')
    Hello {{ Auth::user()->name }}
@endsection
