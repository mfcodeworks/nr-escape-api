@extends('beautymail::templates.ark')

@section('content')

	@include('beautymail::templates.ark.contentStart')
		<span style="padding-top: 15px">
			<h4 class="secondary"><strong>A device has signed in to your NR Escape account</strong></h4>
			<br>
			<p>Device: {{$agent['device']}} {{$agent['platform']}} using {{$agent['browser']}}</p>
			<p>IP Address: {{$agent['ip']}}</p>
		</span>
	@include('beautymail::templates.ark.contentEnd')

@stop