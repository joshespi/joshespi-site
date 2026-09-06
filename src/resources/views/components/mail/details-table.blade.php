@props(['rows'])
<table style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
    @foreach($rows as $label => $value)
    <tr>
        <td style="padding: 8px 0; border-bottom: 1px solid #eee; font-weight: bold; width: 160px;">{{ $label }}</td>
        <td style="padding: 8px 0; border-bottom: 1px solid #eee;">{{ $value }}</td>
    </tr>
    @endforeach
</table>
