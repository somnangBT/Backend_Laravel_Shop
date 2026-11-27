<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class VerificationDispatcher
{
    /**
     * Detect whether input is an email address or a phone number, and normalize it.
     *
     * @return array{type: 'email'|'phone'|'invalid', value: string}
     */
    public static function normalizeContact(string $raw): array
    {
        $trimmed = trim($raw);
        if ($trimmed === '') {
            return ['type' => 'invalid', 'value' => ''];
        }

        if (filter_var($trimmed, FILTER_VALIDATE_EMAIL)) {
            return ['type' => 'email', 'value' => strtolower($trimmed)];
        }

        // Strip spaces, dashes, and parentheses for phone detection
        $compact = preg_replace('/[\s\-\(\)]+/', '', $trimmed);
        if (preg_match('/^\+?[0-9]{8,15}$/', $compact)) {
            // Normalize local Cambodian 0xxxxxxxx numbers to +855xxxxxxxx
            if (str_starts_with($compact, '0') && !str_starts_with($compact, '00')) {
                $compact = '+855' . substr($compact, 1);
            } elseif (!str_starts_with($compact, '+')) {
                $compact = '+' . $compact;
            }

            return ['type' => 'phone', 'value' => $compact];
        }

        return ['type' => 'invalid', 'value' => $trimmed];
    }

    public static function isEmailMailerConfigured(): bool
    {
        $mailer = config('mail.default');
        return !in_array($mailer, ['log', 'array', null, ''], true);
    }

    public static function isSmsConfigured(): bool
    {
        $provider = strtolower((string) config('services.sms.provider', 'none'));

        return match ($provider) {
            'plasgate' => !empty(config('services.sms.api_key')) && !empty(config('services.sms.api_secret')),
            'mekongsms' => !empty(config('services.sms.api_key')) && !empty(config('services.sms.api_secret')),
            'twilio' => !empty(config('services.sms.twilio_sid'))
                && !empty(config('services.sms.twilio_token'))
                && !empty(config('services.sms.twilio_from')),
            default => false,
        };
    }

    /**
     * Dispatch a 6-digit verification code via Email SMTP or configured SMS Gateway.
     *
     * @return array{ok: bool, configured: bool, error: ?string}
     */
    public static function sendCode(string $type, string $recipient, string $code, string $purpose = 'register'): array
    {
        if ($type === 'email') {
            if (!self::isEmailMailerConfigured()) {
                return [
                    'ok' => false,
                    'configured' => false,
                    'error' => 'ប្រព័ន្ធផ្ញើ Email មិនទាន់បានកំណត់ Gmail SMTP (MAIL_MAILER=smtp) នៅលើ Server នៅឡើយទេ។',
                ];
            }

            try {
                $subject = $purpose === 'register'
                    ? 'LuckShop — លេខកូដផ្ទៀងផ្ទាត់ការចុះឈ្មោះ'
                    : 'LuckShop — លេខកូដផ្ទៀងផ្ទាត់';

                $body = "សួស្ដី!\n\n"
                    . "លេខកូដផ្ទៀងផ្ទាត់ LuckShop របស់អ្នកគឺ៖ {$code}\n\n"
                    . "លេខកូដនេះមានសុពលភាពរយៈពេល ១៥ នាទី។ សូមកុំចែករំលែកកូដនេះទៅអ្នកដទៃ។";

                Mail::raw($body, function ($msg) use ($recipient, $subject) {
                    $msg->to($recipient)->subject($subject);
                });

                return ['ok' => true, 'configured' => true, 'error' => null];
            } catch (\Exception $e) {
                Log::error('Registration email OTP send failed: ' . $e->getMessage());
                return [
                    'ok' => false,
                    'configured' => true,
                    'error' => 'មិនអាចផ្ញើលេខកូដទៅអ៊ីមែលបានទេ។ សូមពិនិត្យការកំណត់ SMTP ឬព្យាយាមម្ដងទៀត។',
                ];
            }
        }

        if ($type === 'phone') {
            if (!self::isSmsConfigured()) {
                return [
                    'ok' => false,
                    'configured' => false,
                    'error' => 'សេវាកម្មផ្ញើសារ SMS ទៅទូរស័ព្ទមិនទាន់បានភ្ជាប់ API Key (PlasGate/MekongSMS/Twilio) ទេ។ សូមប្រើ Gmail ដើម្បីចុះឈ្មោះសិន!',
                ];
            }

            $provider = strtolower((string) config('services.sms.provider', 'none'));
            $message = "LuckShop: Your verification code is {$code}. Valid for 15 minutes.";

            try {
                if ($provider === 'plasgate') {
                    $endpoint = config('services.sms.endpoint') ?: 'https://cloudapi.plasgate.com/rest/send';
                    $response = Http::timeout(10)
                        ->withHeaders(['X-Secret' => config('services.sms.api_secret')])
                        ->post($endpoint . '?private_key=' . urlencode((string) config('services.sms.api_key')), [
                            'sender' => config('services.sms.sender', 'LuckShop'),
                            'to' => ltrim($recipient, '+'),
                            'content' => $message,
                        ]);

                    if ($response->successful()) {
                        return ['ok' => true, 'configured' => true, 'error' => null];
                    }
                    Log::warning('PlasGate SMS failed: ' . $response->body());
                } elseif ($provider === 'mekongsms') {
                    $endpoint = config('services.sms.endpoint') ?: 'https://api.mekongsms.com/api/sendsms.aspx';
                    $response = Http::timeout(10)->asForm()->post($endpoint, [
                        'username' => config('services.sms.api_key'),
                        'pass' => config('services.sms.api_secret'),
                        'sender' => config('services.sms.sender', 'LuckShop'),
                        'gsm' => ltrim($recipient, '+'),
                        'smstext' => $message,
                        'int' => 1,
                    ]);

                    if ($response->successful()) {
                        return ['ok' => true, 'configured' => true, 'error' => null];
                    }
                    Log::warning('MekongSMS failed: ' . $response->body());
                } elseif ($provider === 'twilio') {
                    $sid = config('services.sms.twilio_sid');
                    $token = config('services.sms.twilio_token');
                    $from = config('services.sms.twilio_from');
                    $response = Http::timeout(10)
                        ->withBasicAuth($sid, $token)
                        ->asForm()
                        ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                            'From' => $from,
                            'To' => $recipient,
                            'Body' => $message,
                        ]);

                    if ($response->successful()) {
                        return ['ok' => true, 'configured' => true, 'error' => null];
                    }
                    Log::warning('Twilio SMS failed: ' . $response->body());
                }
            } catch (\Exception $e) {
                Log::error('SMS OTP dispatch exception: ' . $e->getMessage());
            }

            return [
                'ok' => false,
                'configured' => true,
                'error' => 'មិនអាចផ្ញើសារ SMS ទៅលេខទូរស័ព្ទបានទេ។ សូមព្យាយាមម្ដងទៀត ឬប្រើ Gmail ជំនួស។',
            ];
        }

        return [
            'ok' => false,
            'configured' => false,
            'error' => 'ទម្រង់អ៊ីមែល ឬលេខទូរស័ព្ទមិនត្រឹមត្រូវទេ។',
        ];
    }
}
