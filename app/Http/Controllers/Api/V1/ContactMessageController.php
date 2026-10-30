<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Contact\StoreContactMessageRequest;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactMessageController extends Controller
{
    public function store(StoreContactMessageRequest $request): JsonResponse
    {
        $message = ContactMessage::create([
            ...$request->validated(),
            'type' => $request->input('type', 'contact'),
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);

        $recipient = config('mail.contact_notification_address')
            ?: config('mail.from.address');

        if ($recipient) {
            try {
                Mail::raw($this->formatNotificationBody($message), function ($mail) use ($message, $recipient): void {
                    $mail->to($recipient)
                        ->replyTo($message->email, $message->name)
                        ->subject('[Poljar] Pesan kontak baru: '.$message->subject);
                });
            } catch (\Throwable $exception) {
                Log::warning('Contact message notification failed', [
                    'contact_message_id' => $message->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return ResponseHelper::created(
            ['id' => $message->id],
            'Pesan Anda berhasil dikirim. Kami akan segera menghubungi Anda.'
        );
    }

    public function storePartnership(StoreContactMessageRequest $request): JsonResponse
    {
        $request->merge(['type' => 'partnership']);

        return $this->store($request);
    }

    private function formatNotificationBody(ContactMessage $message): string
    {
        return implode("\n", [
            'Pesan kontak baru dari website Poljar',
            '',
            'Nama: '.$message->name,
            'Email: '.$message->email,
            'Subjek: '.$message->subject,
            '',
            'Pesan:',
            $message->message,
            '',
            'IP: '.($message->ip_address ?? '-'),
            'User Agent: '.($message->user_agent ?? '-'),
        ]);
    }
}
