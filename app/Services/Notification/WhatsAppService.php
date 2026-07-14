<?php

namespace App\Services\Notification;

class WhatsAppService
{
    public function generateLink(string $phone, string $templateKey, array $data): string
    {
        $template = $this->getTemplate($templateKey);
        $message = $this->fillTemplate($template, $data);
        $encoded = urlencode($message);

        $phone = ltrim($phone, '+0');
        if (str_starts_with($phone, '62')) {
            $phone = substr($phone, 2);
        }

        return "https://wa.me/62{$phone}?text={$encoded}";
    }

    public function getTemplate(string $key): string
    {
        $templates = config('wa-templates', []);

        if (!isset($templates[$key])) {
            throw new \InvalidArgumentException("WA template '{$key}' not found.");
        }

        return $templates[$key];
    }

    public function fillTemplate(string $template, array $data): string
    {
        $placeholders = [];
        foreach ($data as $key => $value) {
            $placeholders['{' . $key . '}'] = $value;
        }

        return strtr($template, $placeholders);
    }

    public function send(string $phone, string $templateKey, array $data): string
    {
        return $this->generateLink($phone, $templateKey, $data);
    }
}
