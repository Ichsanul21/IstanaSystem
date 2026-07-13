# WhatsApp Templates

All WhatsApp notifications use **wa.me links** (manual click by admin). No API integration. Templates use human-friendly Indonesian language.

## Template Categories

| Category | Triggered By | Sent By |
|----------|-------------|---------|
| Order Created | Order saved | CS / Cashier |
| Status Update | Production status changed | Workshop Staff |
| Ready for Pickup | Status = SIAP | Workshop Staff |
| Payment Reminder | Order unpaid > 24h | CS |
| Refund Request | Refund submitted | CS |
| Refund Approved | Refund approved | Branch Admin |
| Membership Upgrade | Points threshold crossed | Auto (trigger) |
| Birthday Voucher | Customer birthday | Auto (cron) |
| Promo Broadcast | Manual | CS / Marketing |
| Tracking Link | Any WA notification | Auto |

## Template Format

```
Kak {customer_name},

{message_body}

~ Istana Laundry Samarinda
```

## 1. Order Created

```
Halo Kak {customer_name}!

Terima kasih sudah mempercayakan cucian Anda ke Istana Laundry 🧺✨

Pesanan Anda:
📋 No. Order: #{order_number}
📦 Layanan: {service_list}
💰 Total: Rp {grand_total}
📍 Cabang: {branch_name}
⏱ Estimasi: {estimated_time}

Pantau status pesanan Anda di sini:
{tracking_url}

Untuk bertanya, balas pesan ini ya.
```

## 2. Status Update (per item)

```
Halo Kak {customer_name}!

Ada kabar baik! Pesanan Anda sudah diproses ke tahap selanjutnya:

📦 Order: #{order_number}
🔖 Item: {service_name} ({quantity})
📍 Status: {old_status} → {new_status}

Pantau terus pesanan Anda:
{tracking_url}

~ Istana Laundry
```

## 3. Ready for Pickup (SIAP)

```
Halo Kak {customer_name}!

🎉 Pakaian Anda SUDAH SIAP diambil!

📋 Order: #{order_number}
📍 Cabang: {branch_name}
📅 Siap sejak: {ready_time}

Silakan datang ke toko kami untuk mengambil pesanan.
Jangan lupa bawa nomor order ini ya!

Jika ingin diantar, hubungi kami segera:
{wa_contact}

~ Istana Laundry
```

## 4. Payment Reminder

```
Halo Kak {customer_name}!

Pesanan Anda di Istana Laundry masih menunggu pembayaran:

📋 Order: #{order_number}
💰 Total: Rp {grand_total}

Silakan lakukan pembayaran agar pesanan segera diproses.

Pembayaran dapat dilakukan via:
• Tunai (di kasir)
• Transfer ke {bank_info}
• QRIS (scan di toko)

Link pembayaran online: {payment_link}

~ Istana Laundry
```

## 5. Refund Request (CS follows up)

```
Halo Kak {customer_name}!

Kami menerima permohonan refund Anda untuk pesanan:

📋 Order: #{order_number}
💰 Jumlah: Rp {refund_amount}
📝 Alasan: {reason}

Tim kami akan memproses pengajuan ini.
Kami akan menghubungi Anda dalam 1x24 jam.

Terima kasih atas pengertiannya 🙏

~ Istana Laundry
```

## 6. Refund Approved

```
Halo Kak {customer_name}!

Kabar baik! Permohonan refund Anda telah DISETUJUI ✅

📋 Order: #{order_number}
💰 Jumlah refund: Rp {refund_amount}

Silakan datang ke {branch_name} untuk mengambil refund Anda.
Jangan lupa bawa nomor order ini.

~ Istana Laundry
```

## 7. Membership Upgrade

```
🎉 Selamat Kak {customer_name}!

Anda sekarang menjadi member {new_tier} Istana Laundry!

Benefit baru Anda:
{benefit_list}

Terima kasih sudah setia laundry di Istana! 😊

~ Istana Laundry
```

## 8. Birthday Voucher

```
Halo Kak {customer_name}!

🎂 Selamat Ulang Tahun!

Sebagai member {tier} Istana Laundry, kami memberikan voucher spesial:
🎁 Voucher Rp {voucher_amount}
Berlaku hingga {expiry_date}

Tukarkan saat Anda laundry berikutnya ya!

~ Istana Laundry
```

## 9. Order Pickup Reminder (if not picked up in 3 days)

```
Halo Kak {customer_name}!

Pengingat: Pakaian Anda sudah siap diambil sejak 3 hari yang lalu:

📋 Order: #{order_number}
📍 Cabang: {branch_name}

Mohon segera diambil ya. Jika ada kendala, hubungi kami.

~ Istana Laundry
```

## Template Variables Reference

| Variable | Description | Source |
|----------|-------------|--------|
| `{customer_name}` | Customer name | `customer.name` |
| `{order_number}` | Order number | `order.order_number` |
| `{service_list}` | List of services | `order.items.pluck('service.name')` |
| `{service_name}` | Single service name | `order_item.service.name` |
| `{quantity}` | Quantity with unit | `order_item.quantity . order_item.service.unit` |
| `{grand_total}` | Formatted total | `Number::currency($order->grand_total)` |
| `{branch_name}` | Branch name | `order.branch.name` |
| `{estimated_time}` | Estimated finish | `order.created_at + branch.avg_processing_time` |
| `{tracking_url}` | Public tracking link | `route('tracking.show', $order->tracking_token)` |
| `{wa_contact}` | Branch WA number | `branch.phone` |
| `{payment_link}` | Midtrans payment link | `gatewayPayment.payment_url` |
| `{refund_amount}` | Refund amount | `refund.amount` formatted |
| `{reason}` | Refund reason | `refund.reason` |
| `{new_tier}` | New membership tier | `membershipTier.name` |
| `{old_tier}` | Previous tier | For upgrade message |
| `{benefit_list}` | Tier benefits | `membershipTier.benefits` |
| `{voucher_amount}` | Birthday voucher | `membershipTier.birthday_voucher` formatted |
| `{expiry_date}` | Expiry date | `now()->addDays(30)->format('d M Y')` |

## How Links Are Generated

```php
// App/Services/Notification/WhatsAppService.php
class WhatsAppService
{
    public function generateLink(string $phone, string $templateKey, array $data): string
    {
        $template = $this->getTemplate($templateKey);
        $message = $this->fillTemplate($template, $data);
        $encoded = urlencode($message);
        
        return "https://wa.me/62{$phone}?text={$encoded}";
    }
    
    private function fillTemplate(string $template, array $data): string
    {
        foreach ($data as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }
        return $template;
    }
}
```

## Storage

- Templates stored in `config/wa-templates.php` or `database/settings` (for dynamic editing by Owner/Super Admin via Settings → Notification)
- Or hardcoded in a service class for simplicity

## Files

```
app/Services/Notification/WhatsAppService.php
config/wa-templates.php
resources/views/settings/notification.blade.php  (template editor UI)
```
