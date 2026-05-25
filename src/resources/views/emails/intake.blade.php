<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family: sans-serif; font-size: 15px; color: #111; max-width: 600px; margin: 0 auto; padding: 24px;">

<h2 style="margin-top: 0;">New intake: {{ $service }}</h2>

<table style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
    <tr>
        <td style="padding: 8px 0; border-bottom: 1px solid #eee; font-weight: bold; width: 160px;">Name</td>
        <td style="padding: 8px 0; border-bottom: 1px solid #eee;">{{ $name }}</td>
    </tr>
    <tr>
        <td style="padding: 8px 0; border-bottom: 1px solid #eee; font-weight: bold;">Email</td>
        <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><a href="mailto:{{ $email }}">{{ $email }}</a></td>
    </tr>
    <tr>
        <td style="padding: 8px 0; border-bottom: 1px solid #eee; font-weight: bold;">Service</td>
        <td style="padding: 8px 0; border-bottom: 1px solid #eee;">{{ $service }}</td>
    </tr>
    @foreach($details as $label => $value)
    <tr>
        <td style="padding: 8px 0; border-bottom: 1px solid #eee; font-weight: bold;">{{ $label }}</td>
        <td style="padding: 8px 0; border-bottom: 1px solid #eee;">{{ $value }}</td>
    </tr>
    @endforeach
</table>

<h3 style="margin-bottom: 8px;">Project details</h3>
<p style="background: #f8f9fa; padding: 16px; border-radius: 4px; white-space: pre-wrap; margin: 0;">{{ $message }}</p>

<hr style="margin: 24px 0; border: none; border-top: 1px solid #eee;">
<p style="color: #888; font-size: 13px; margin: 0;">Sent from joshespi.com intake form. Reply-To is set to the sender's email.</p>

</body>
</html>
