@extends('emails.newsletter.layout')

@section('content')
    @if($subscriber->locale === 'id')
        <h2>Halo!</h2>
        <p>Terima kasih telah mendaftar untuk newsletter Polinema Mengajar. Silakan konfirmasi alamat email Anda dengan menekan tombol di bawah ini:</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $verificationUrl }}" class="button">Konfirmasi Langganan</a>
            <p style="font-size: 12px; color: #999; margin-top: 10px;">
                Jika tombol tidak berfungsi, klik link berikut atau salin ke browser Anda:<br>
                <a href="{{ $verificationUrl }}" style="color: #FF8C42;">{{ $verificationUrl }}</a>
            </p>
        </div>
        <p>Jika Anda tidak merasa mendaftar, Anda bisa mengabaikan email ini.</p>
    @else
        <h2>Hello!</h2>
        <p>Thank you for signing up for the Polinema Mengajar newsletter. Please confirm your email address by clicking the button below:</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $verificationUrl }}" class="button">Confirm Subscription</a>
            <p style="font-size: 12px; color: #999; margin-top: 10px;">
                If the button doesn't work, click the link below or copy it to your browser:<br>
                <a href="{{ $verificationUrl }}" style="color: #FF8C42;">{{ $verificationUrl }}</a>
            </p>
        </div>
        <p>If you did not sign up, you can safely ignore this email.</p>
    @endif
@endsection
