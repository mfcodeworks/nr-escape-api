@extends('beautymail::templates.ark')

@section('content')

	@include('beautymail::templates.ark.contentStart')
        <span>
            <h4 class="secondary"><strong>Reports Summary</strong></h4>
            <br>
            <p>Below is the reports summary for {{ now() }}</p>
        </span>
		<span>
            <br>
			<h4 class="secondary"><strong>Profile Reports</strong></h4>
			<br>
			<table style="width: 100%; text-align: left;">
                <thead>
                    <tr>
                        <th>Profile ID</th>
                        <th>Reports</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($profiles as $profile)
                        <tr>
                            <td>{{$profile->profile}}</td>
                            <td>{{$profile->reports}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
		</span>
		<span>
            <br>
			<h4 class="secondary"><strong>Post Reports</strong></h4>
			<br>
			<table style="width: 100%; text-align: left;">
                <thead>
                    <tr>
                        <th>Post ID</th>
                        <th>Reports</th>
                        <th>Last Updated Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($posts as $post)
                        <tr>
                            <td>{{$post->post}}</td>
                            <td>{{$post->reports}}</td>
                            <td>{{$post->post_date}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
		</span>
	@include('beautymail::templates.ark.contentEnd')

@stop