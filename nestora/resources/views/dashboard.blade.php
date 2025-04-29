@extends('layout.apps')

@section('konten')
<h1>Welcome, {{ Auth::user()->name }}</h1>
<p>This is the content of the dashboard page.</p>
@endsection
