@extends('beautymail::templates.ark')

@section('content')

	@include('beautymail::templates.ark.contentStart')
		<span style="padding-top: 15px">
			<h4 class="secondary"><strong>A new unknown device has signed in to your Escape account</strong></h4>
			<br>
			<p>Device: {{$agent['platform']}} {{$agent['device']}} using {{$agent['browser']}}</p>
			<p>IP Address: {{$agent['ip']}}</p>
		</span>
	@include('beautymail::templates.ark.contentEnd')

@stop