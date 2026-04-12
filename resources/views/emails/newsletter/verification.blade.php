@extends('emails.newsletter.layout')

@section('content')
    @if($subscriber->locale === 'id')
        <h1>Verifikasi Email Anda</h1>
        <p>Halo,</p>
        <p>Terima kasih telah bergabung dengan komunitas <strong>{{ config('app.name') }}</strong>! Kami sangat senang Anda ingin tetap terhubung dengan perkembangan terbaru kami.</p>
        <p>Untuk menyelesaikan pendaftaran newsletter Anda, mohon konfirmasi alamat email Anda dengan menekan tombol di bawah ini:</p>
        
        <div style="text-align: center; margin: 32px 0;">
            <a href="{{ $verificationUrl }}" class="button">Konfirmasi Langganan</a>
        </div>
        
        <p>Setelah konfirmasi, Anda akan mulai menerima pembaruan berkala mengenai artikel, berita, dan program inspiratif dari kami.</p>
        
        <div class="divider"></div>
        
        <p class="text-muted">
            Jika tombol tidak berfungsi, silakan salin dan tempel tautan di bawah ini ke browser Anda:<br>
            <a href="{{ $verificationUrl }}" class="text-orange" style="word-break: break-all;">{{ $verificationUrl }}</a>
        </p>
        <p class="text-muted">Jika Anda tidak merasa melakukan pendaftaran ini, abaikan saja email ini. Anda tidak akan terdaftar kecuali Anda menekan tombol di atas.</p>
    @else
        <h1>Verify Your Email</h1>
        <p>Hello,</p>
        <p>Thank you for joining the <strong>{{ config('app.name') }}</strong> community! We are excited to have you with us.</p>
        <p>To complete your newsletter subscription, please confirm your email address by clicking the button below:</p>
        
        <div style="text-align: center; margin: 32px 0;">
            <a href="{{ $verificationUrl }}" class="button">Confirm Subscription</a>
        </div>
        
        <p>Once confirmed, you will start receiving periodic updates regarding our latest articles, news, and inspiring programs.</p>
        
        <div class="divider"></div>
        
        <p class="text-muted">
            If the button doesn't work, please copy and paste the link below into your browser:<br>
            <a href="{{ $verificationUrl }}" class="text-orange" style="word-break: break-all;">{{ $verificationUrl }}</a>
        </p>
        <p class="text-muted">If you did not sign up for this, you can safely ignore this email. You will not be subscribed unless you click the button above.</p>
    @endif
@endsection

