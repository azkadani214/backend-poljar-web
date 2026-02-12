@extends('emails.newsletter.layout')

@section('content')
    {!! $body !!}
@endsection

@section('footer_links')
    <p>
        <a href="{{ $preferenceUrl }}">Manage Preferences</a> | 
        <a href="{{ $unsubscribeUrl }}">Unsubscribe</a>
    </p>
@endsection
