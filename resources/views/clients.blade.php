@extends('layouts.app')

@section('content')
<div class="col">
    <passport-clients></passport-clients>
    <br />
    <passport-authorized-clients></passport-authorized-clients>
    <br />
    <passport-personal-access-tokens></passport-personal-access-tokens>
</div>
@endsection
