@extends('emails.newsletter.layout')

@section('content')
    @if($subscriber->locale === 'id')
        <h2>Selamat Datang!</h2>
        <p>Email Anda telah berhasil dikonfirmasi. Anda sekarang akan menerima update terbaru, inspirasi pendidikan, dan info kegiatan dari Polinema Mengajar.</p>
        <p>Anda bisa mengatur preferensi topik yang ingin Anda terima melalui tautan di bawah ini:</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $preferenceUrl }}" class="button">Atur Preferensi</a>
        </div>
    @else
        <h2>Welcome!</h2>
        <p>Your email has been successfully confirmed. You will now receive the latest updates, educational inspirations, and activity info from Polinema Mengajar.</p>
        <p>You can manage your topic preferences through the link below:</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $preferenceUrl }}" class="button">Manage Preferences</a>
        </div>
    @endif
@endsection

@section('footer_links')
    <p><a href="{{ $preferenceUrl }}">Manage Preferences</a> | <a href="{{ config('app.url') . '/newsletter/unsubscribe?email=' . $subscriber->email }}">Unsubscribe</a></p>
@endsection
