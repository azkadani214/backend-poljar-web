@extends('emails.newsletter.layout')

@section('content')
    {!! $body !!}
@endsection

@section('footer_links')
    <a href="{{ $preferenceUrl }}">Manage Preferences</a> | <a href="{{ $unsubscribeUrl }}">Unsubscribe</a>
@endsection
