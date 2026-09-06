@props(['heading'])
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family: sans-serif; font-size: 15px; color: #111; max-width: 600px; margin: 0 auto; padding: 24px;">

<h2 style="margin-top: 0;">{{ $heading }}</h2>

{{ $slot }}

<hr style="margin: 24px 0; border: none; border-top: 1px solid #eee;">
{{ $footer }}

</body>
</html>
