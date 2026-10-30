<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'site_info' => ['sometimes', 'array'],
            'site_info.organization_name' => ['nullable', 'string', 'max:150'],
            'site_info.tagline' => ['nullable', 'string', 'max:255'],
            'site_info.logo_url' => ['nullable', 'string', 'max:2048'],
            'site_info.favicon_url' => ['nullable', 'string', 'max:2048'],
            'site_info.email' => ['nullable', 'email', 'max:255'],
            'site_info.phone' => ['nullable', 'string', 'max:50'],
            'site_info.whatsapp' => ['nullable', 'string', 'max:50'],
            'site_info.address' => ['nullable', 'string', 'max:500'],
            'site_info.operational_hours' => ['nullable', 'string', 'max:255'],
            'site_info.map_url' => ['nullable', 'string', 'max:2048'],
            'site_info.main_domain' => ['nullable', 'string', 'max:255'],
            'site_info.admin_domain' => ['nullable', 'string', 'max:255'],
            'social_media' => ['sometimes', 'array'],
            'social_media.instagram_url' => ['nullable', 'string', 'max:2048'],
            'social_media.tiktok_url' => ['nullable', 'string', 'max:2048'],
            'social_media.youtube_url' => ['nullable', 'string', 'max:2048'],
            'social_media.linkedin_url' => ['nullable', 'string', 'max:2048'],
            'seo' => ['sometimes', 'array'],
            'seo.default_title' => ['nullable', 'string', 'max:120'],
            'seo.default_description' => ['nullable', 'string', 'max:255'],
            'seo.og_image_url' => ['nullable', 'string', 'max:2048'],
            'seo.google_verification' => ['nullable', 'string', 'max:255'],
            'seo.ga4_id' => ['nullable', 'string', 'max:50'],
            'seo.gtm_id' => ['nullable', 'string', 'max:50'],
            'forms' => ['sometimes', 'array'],
            'forms.contact_notification_email' => ['nullable', 'email', 'max:255'],
            'forms.success_message' => ['nullable', 'string', 'max:255'],
            'forms.partnership_success_message' => ['nullable', 'string', 'max:255'],
            'forms.enable_honeypot' => ['nullable', 'boolean'],
            'announcement_bar' => ['sometimes', 'array'],
            'announcement_bar.enabled' => ['nullable', 'boolean'],
            'announcement_bar.text' => ['nullable', 'string', 'max:255'],
            'announcement_bar.link_url' => ['nullable', 'string', 'max:2048'],
            'announcement_bar.link_label' => ['nullable', 'string', 'max:80'],
            'announcement_bar.color' => ['nullable', 'string', 'max:40'],
            'announcement_bar.starts_at' => ['nullable', 'date'],
            'announcement_bar.ends_at' => ['nullable', 'date', 'after_or_equal:announcement_bar.starts_at'],
            'announcement_bar.dismissible' => ['nullable', 'boolean'],
            'popup_banner' => ['sometimes', 'array'],
            'popup_banner.enabled' => ['nullable', 'boolean'],
            'popup_banner.title' => ['nullable', 'string', 'max:150'],
            'popup_banner.body' => ['nullable', 'string', 'max:500'],
            'popup_banner.image_url' => ['nullable', 'string', 'max:2048'],
            'popup_banner.cta_label' => ['nullable', 'string', 'max:80'],
            'popup_banner.cta_url' => ['nullable', 'string', 'max:2048'],
            'popup_banner.trigger' => ['nullable', Rule::in(['on_load', 'delay', 'exit_intent'])],
            'popup_banner.delay_seconds' => ['nullable', 'integer', 'min:0', 'max:3600'],
            'popup_banner.frequency' => ['nullable', Rule::in(['session', 'daily', 'always'])],
            'popup_banner.target_pages' => ['nullable', 'array'],
            'popup_banner.target_pages.*' => ['string', 'max:255'],
        ];
    }
}
