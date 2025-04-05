<x-mail::layout>
# Test Email

This is a test email from Tech Store. If you're receiving this, it means your email configuration is working correctly!

<x-mail::button :url="$url">
Visit Our Store
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::layout> 