<?php

return [
    'order_created' => <<<'TEXT'
Halo Kak {customer_name}!

Terima kasih sudah mempercayakan cucian Anda ke Istana Laundry

Pesanan Anda:
 No. Order: #{order_number}
 Layanan: {service_list}
 Total: Rp {grand_total}
 Cabang: {branch_name}
 Estimasi: {estimated_time}

Pantau status pesanan Anda di sini:
{tracking_url}

Untuk bertanya, balas pesan ini ya.
TEXT,

    'status_update' => <<<'TEXT'
Halo Kak {customer_name}!

Ada kabar baik! Pesanan Anda sudah diproses ke tahap selanjutnya:

 Order: #{order_number}
 Item: {service_name} ({quantity})
 Status: {old_status} -> {new_status}

Pantau terus pesanan Anda:
{tracking_url}

~ Istana Laundry
TEXT,

    'ready_for_pickup' => <<<'TEXT'
Halo Kak {customer_name}!

Pakaian Anda SUDAH SIAP diambil!

 Order: #{order_number}
 Cabang: {branch_name}
 Siap sejak: {ready_time}

Silakan datang ke toko kami untuk mengambil pesanan.
Jangan lupa bawa nomor order ini ya!

Jika ingin diantar, hubungi kami segera:
{wa_contact}

~ Istana Laundry
TEXT,

    'payment_reminder' => <<<'TEXT'
Halo Kak {customer_name}!

Pesanan Anda di Istana Laundry masih menunggu pembayaran:

 Order: #{order_number}
 Total: Rp {grand_total}

Silakan lakukan pembayaran agar pesanan segera diproses.

Pembayaran dapat dilakukan via:
- Tunai (di kasir)
- Transfer ke {bank_info}
- QRIS (scan di toko)

Link pembayaran online: {payment_link}

~ Istana Laundry
TEXT,

    'refund_request' => <<<'TEXT'
Halo Kak {customer_name}!

Kami menerima permohonan refund Anda untuk pesanan:

 Order: #{order_number}
 Jumlah: Rp {refund_amount}
 Alasan: {reason}

Tim kami akan memproses pengajuan ini.
Kami akan menghubungi Anda dalam 1x24 jam.

Terima kasih atas pengertiannya

~ Istana Laundry
TEXT,

    'refund_approved' => <<<'TEXT'
Halo Kak {customer_name}!

Kabar baik! Permohonan refund Anda telah DISETUJUI

 Order: #{order_number}
 Jumlah refund: Rp {refund_amount}

Silakan datang ke {branch_name} untuk mengambil refund Anda.
Jangan lupa bawa nomor order ini.

~ Istana Laundry
TEXT,

    'membership_upgrade' => <<<'TEXT'
Selamat Kak {customer_name}!

Anda sekarang menjadi member {new_tier} Istana Laundry!

Benefit baru Anda:
{benefit_list}

Terima kasih sudah setia laundry di Istana!

~ Istana Laundry
TEXT,

    'birthday_voucher' => <<<'TEXT'
Halo Kak {customer_name}!

Selamat Ulang Tahun!

Sebagai member {tier} Istana Laundry, kami memberikan voucher spesial:
 Voucher Rp {voucher_amount}
Berlaku hingga {expiry_date}

Tukarkan saat Anda laundry berikutnya ya!

~ Istana Laundry
TEXT,

    'pickup_reminder' => <<<'TEXT'
Halo Kak {customer_name}!

Pengingat: Pakaian Anda sudah siap diambil sejak 3 hari yang lalu:

 Order: #{order_number}
 Cabang: {branch_name}

Mohon segera diambil ya. Jika ada kendala, hubungi kami.

~ Istana Laundry
TEXT,
];
