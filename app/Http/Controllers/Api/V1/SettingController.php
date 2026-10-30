<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\UpdateSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

class SettingController extends Controller
{
    public function index(): JsonResponse
    {
        return ResponseHelper::success($this->settings(), 'Settings retrieved successfully');
    }

    public function update(UpdateSettingsRequest $request): JsonResponse
    {
        $payload = Arr::only($request->validated(), array_keys($this->defaults()));

        foreach ($payload as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['group' => $key, 'value' => array_replace_recursive($this->defaults()[$key], $value)]
            );
        }

        return ResponseHelper::updated($this->settings(), 'Settings updated successfully');
    }

    private function settings(): array
    {
        $settings = $this->defaults();
        $stored = Setting::query()
            ->whereIn('key', array_keys($settings))
            ->get()
            ->mapWithKeys(fn (Setting $setting): array => [$setting->key => $setting->value ?? []])
            ->all();

        foreach ($stored as $key => $value) {
            $settings[$key] = array_replace_recursive($settings[$key], $value);
        }

        return $settings;
    }

    private function defaults(): array
    {
        return [
            'site_info' => [
                'organization_name' => 'Polinema Mengajar',
                'tagline' => 'Gerakan sosial pendidikan dari Politeknik Negeri Malang',
                'logo_url' => '',
                'favicon_url' => '',
                'email' => '',
                'phone' => '',
                'whatsapp' => '',
                'address' => 'Politeknik Negeri Malang, Jawa Timur, Indonesia',
                'operational_hours' => '',
                'map_url' => '',
                'main_domain' => 'https://polinemamengajar.or.id',
                'admin_domain' => 'https://manager.polinemamengajar.or.id',
            ],
            'social_media' => [
                'instagram_url' => config('services.social.instagram_url', 'https://instagram.com/polinema.mengajar'),
                'tiktok_url' => config('services.social.tiktok_url', 'https://www.tiktok.com/@polinema_mengajar'),
                'youtube_url' => config('services.social.youtube_url', 'https://www.youtube.com/@polinemamengajar'),
                'linkedin_url' => '',
            ],
            'seo' => [
                'default_title' => 'Polinema Mengajar',
                'default_description' => 'Gerakan sosial pendidikan dari Politeknik Negeri Malang.',
                'og_image_url' => '',
                'google_verification' => '',
                'ga4_id' => '',
                'gtm_id' => '',
            ],
            'forms' => [
                'contact_notification_email' => config('mail.contact_notification_address', ''),
                'success_message' => 'Pesan Anda berhasil dikirim. Kami akan segera menghubungi Anda.',
                'partnership_success_message' => 'Permintaan kemitraan berhasil dikirim. Tim kami akan segera menghubungi Anda.',
                'enable_honeypot' => true,
            ],
            'announcement_bar' => [
                'enabled' => false,
                'text' => '',
                'link_url' => '',
                'link_label' => '',
                'color' => 'primary',
                'starts_at' => null,
                'ends_at' => null,
                'dismissible' => true,
            ],
            'popup_banner' => [
                'enabled' => false,
                'title' => '',
                'body' => '',
                'image_url' => '',
                'cta_label' => '',
                'cta_url' => '',
                'trigger' => 'on_load',
                'delay_seconds' => 3,
                'frequency' => 'daily',
                'target_pages' => [],
            ],
        ];
    }
}
