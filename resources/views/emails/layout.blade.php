{{--
    The HTML form of every email, designed as a page of the blog: a masthead with a double rule, serif headlines and
    plenty of air. It follows what mail clients actually support: nested tables, inline styles and system fonts. Nothing
    here depends on an image, because most clients block them by default; icons are typographic marks, which unlike
    emoji look the same everywhere. Light only, because clients that invert colors on their own would undo a dark
    version anyway.
--}}
@php
    $sans = "-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif";
    $serif = "Georgia,'Times New Roman',Times,serif";
    $mono = "'SFMono-Regular',Menlo,Consolas,'Liberation Mono',monospace";
    $ink = '#111827';
    $body = '#44474f';
    $muted = '#6b7280';
    $line = '#e8e6e0';
    $paper = '#f6f5f1';
    $panel = '#faf9f6';
    $mark = '#6875f5';
    $link = '#4f46e5';
    $accent = $draft->tone->accent();
    $soft = $draft->tone->soft();
    $calm = $draft->tone !== \App\Emails\EmailTone::Warning;
    $iconType = $draft->icon === '“' ? 'height:28px;padding-top:12px;font-size:34px;line-height:28px;' : 'height:40px;font-size:19px;line-height:40px;';
@endphp
<!doctype html>
<html lang="es" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="x-apple-disable-message-reformatting">
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
<title>{{ $draft->subject }}</title>
<style>
  @media only screen and (max-width: 600px) {
    .page { padding: 0 !important; }
    .card { border-radius: 0 !important; border-left: 0 !important; border-right: 0 !important; }
    .pad { padding-left: 24px !important; padding-right: 24px !important; }
    .wide-only { display: none !important; }
    .headline { font-size: 27px !important; line-height: 34px !important; }
    .code { font-size: 30px !important; letter-spacing: 6px !important; padding-left: 6px !important; }
    .button { width: 100% !important; }
    .button a { display: block !important; text-align: center !important; }
  }
</style>
</head>
<body style="margin:0;padding:0;background-color:{{ $paper }};-webkit-text-size-adjust:100%;">
<div style="display:none;max-height:0;overflow:hidden;opacity:0;color:{{ $paper }};">{{ $draft->preheader }}</div>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="{{ $paper }}" style="background-color:{{ $paper }};">
<tr><td align="center" class="page" style="padding:40px 16px;">
<!--[if mso]><table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0"><tr><td><![endif]-->
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:600px;">

<tr><td class="card" bgcolor="#ffffff" style="background-color:#ffffff;border:1px solid {{ $line }};border-top:4px solid {{ $mark }};border-radius:6px;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">

{{-- Masthead --}}
<tr><td class="pad" style="padding:26px 44px 0;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td style="padding-bottom:18px;border-bottom:3px double {{ $ink }};">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"><tr>
<td valign="middle">
<table role="presentation" cellspacing="0" cellpadding="0" border="0"><tr>
<td width="30" height="30" align="center" valign="middle" bgcolor="{{ $mark }}" style="width:30px;height:30px;background-color:{{ $mark }};border-radius:15px;font-family:{!! $serif !!};font-size:17px;line-height:30px;font-weight:700;color:#ffffff;">I</td>
<td style="padding-left:11px;font-family:{!! $serif !!};font-size:19px;line-height:24px;font-weight:700;letter-spacing:1.2px;color:{{ $ink }};">{{ $draft->brand }}</td>
</tr></table>
</td>
<td class="wide-only" align="right" valign="middle" style="font-family:{!! $serif !!};font-size:13px;line-height:18px;font-style:italic;color:{{ $muted }};">{{ $draft->tagline }}</td>
</tr></table>
</td></tr></table>
</td></tr>

{{-- Icon, kicker and headline --}}
<tr><td class="pad" style="padding:34px 44px 0;">
<table role="presentation" cellspacing="0" cellpadding="0" border="0"><tr>
<td width="40" height="40" align="center" valign="middle" bgcolor="{{ $soft }}" style="width:40px;{{ $iconType }}background-color:{{ $soft }};border-radius:20px;font-family:{!! $serif !!};font-weight:700;color:{{ $accent }};">{{ $draft->icon }}</td>
<td style="padding-left:12px;font-family:{!! $sans !!};font-size:12px;line-height:16px;font-weight:700;letter-spacing:1.6px;text-transform:uppercase;color:{{ $accent }};">{{ $draft->kicker }}</td>
</tr></table>
</td></tr>
<tr><td class="pad" style="padding:20px 44px 0;"><h1 class="headline" style="margin:0;font-family:{!! $serif !!};font-size:32px;line-height:40px;font-weight:400;letter-spacing:-0.3px;color:{{ $ink }};">{{ $draft->title }}</h1></td></tr>

{{-- Greeting and text --}}
<tr><td class="pad" style="padding:24px 44px 0;"><p style="margin:0;font-family:{!! $sans !!};font-size:16px;line-height:27px;font-weight:600;color:{{ $ink }};">{{ $draft->greeting }}</p></td></tr>
@foreach ($draft->paragraphs as $paragraph)
<tr><td class="pad" style="padding:10px 44px 0;"><p style="margin:0;font-family:{!! $sans !!};font-size:16px;line-height:27px;color:{{ $body }};">{{ $paragraph }}</p></td></tr>
@endforeach

{{-- What someone wrote, set as a pull quote --}}
@if ($draft->quote !== null)
<tr><td class="pad" style="padding:26px 44px 0;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"><tr>
<td style="border-left:3px solid {{ $mark }};padding:2px 0 2px 20px;">
<p style="margin:0;font-family:{!! $serif !!};font-size:19px;line-height:30px;font-style:italic;color:{{ $ink }};">&ldquo;{{ $draft->quote }}&rdquo;</p>
<table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin-top:14px;"><tr>
<td width="32" height="32" align="center" valign="middle" bgcolor="{{ $soft }}" style="width:32px;height:32px;background-color:{{ $soft }};border-radius:16px;font-family:{!! $sans !!};font-size:12px;line-height:32px;font-weight:700;color:{{ $accent }};">{{ $draft->quoteInitials }}</td>
<td style="padding-left:10px;font-family:{!! $sans !!};font-size:14px;line-height:20px;font-weight:600;color:{{ $ink }};">{{ $draft->quoteAuthor }}</td>
</tr></table>
</td>
</tr></table>
</td></tr>
@endif

{{-- Numbered steps --}}
@if ($draft->steps !== [])
<tr><td class="pad" style="padding:22px 44px 0;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
@foreach ($draft->steps as $step)
@php($edge = $loop->first ? '' : "border-top:1px solid {$line};")
<tr>
<td width="46" valign="top" style="width:46px;{{ $edge }}padding:14px 0;font-family:{!! $serif !!};font-size:22px;line-height:26px;font-style:italic;color:{{ $mark }};">{{ $loop->iteration }}</td>
<td valign="top" style="{{ $edge }}padding:14px 0;font-family:{!! $sans !!};font-size:14px;line-height:22px;color:{{ $body }};"><strong style="display:block;font-size:15px;color:{{ $ink }};">{{ $step['title'] }}</strong>{{ $step['text'] }}</td>
</tr>
@endforeach
</table>
</td></tr>
@endif

{{-- A code to copy: one string with spacing, not one cell per digit, so that selecting it copies just the code --}}
@if ($draft->highlight !== null)
<tr><td class="pad" style="padding:26px 44px 0;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="{{ $panel }}" style="background-color:{{ $panel }};border:1px dashed #c9c6bd;border-radius:10px;">
<tr><td align="center" style="padding:24px 12px;">
<div style="font-family:{!! $sans !!};font-size:11px;line-height:16px;font-weight:700;letter-spacing:1.6px;text-transform:uppercase;color:{{ $muted }};">{{ $draft->highlightLabel }}</div>
<div class="code" style="padding:8px 0 0 10px;font-family:{!! $mono !!};font-size:38px;line-height:46px;font-weight:700;letter-spacing:10px;color:{{ $ink }};">{{ $draft->highlight }}</div>
</td></tr>
</table>
</td></tr>
@endif

{{-- Details --}}
@if ($draft->details !== [])
<tr><td class="pad" style="padding:24px 44px 0;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="{{ $panel }}" style="background-color:{{ $panel }};border:1px solid {{ $line }};border-radius:10px;">
<tr><td style="padding:4px 18px;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
@foreach ($draft->details as $detail)
@php($edge = $loop->first ? '' : "border-top:1px solid {$line};")
<tr>
<td valign="top" style="{{ $edge }}padding:11px 14px 11px 0;font-family:{!! $sans !!};font-size:13px;line-height:20px;white-space:nowrap;color:{{ $muted }};">{{ $detail['label'] }}</td>
<td valign="top" align="right" style="{{ $edge }}padding:11px 0;font-family:{!! $sans !!};font-size:14px;line-height:20px;font-weight:600;color:{{ $ink }};word-break:break-word;">{{ $detail['value'] }}</td>
</tr>
@endforeach
</table>
</td></tr>
</table>
</td></tr>
@endif

{{-- Button. Padding on the link, not the cell, so the whole button is clickable; Outlook ignores it and gets the cell's instead --}}
@if ($draft->action !== null)
<tr><td class="pad" style="padding:30px 44px 0;">
<table role="presentation" class="button" cellspacing="0" cellpadding="0" border="0"><tr>
<td align="center" bgcolor="{{ $ink }}" style="background-color:{{ $ink }};border-radius:8px;mso-padding-alt:15px 30px;">
<a href="{{ $draft->action['url'] }}" target="_blank" rel="noopener" style="display:inline-block;padding:15px 30px;font-family:{!! $sans !!};font-size:15px;line-height:20px;font-weight:600;letter-spacing:0.2px;color:#ffffff;text-decoration:none;border-radius:8px;">{{ $draft->action['label'] }}&nbsp;&rarr;</a>
</td></tr></table>
</td></tr>
@endif

@if ($draft->linkHint !== null && $draft->action !== null)
<tr><td class="pad" style="padding:18px 44px 0;">
<p style="margin:0;font-family:{!! $sans !!};font-size:12px;line-height:19px;color:{{ $muted }};">{{ $draft->linkHint }}<br>
<a href="{{ $draft->action['url'] }}" target="_blank" rel="noopener" style="color:{{ $link }};word-break:break-all;">{{ $draft->action['url'] }}</a></p>
</td></tr>
@endif

@if ($draft->secondaryAction !== null && $draft->callout === null)
<tr><td class="pad" style="padding:18px 44px 0;">
<a href="{{ $draft->secondaryAction['url'] }}" target="_blank" rel="noopener" style="font-family:{!! $sans !!};font-size:14px;line-height:20px;font-weight:600;color:{{ $link }};">{{ $draft->secondaryAction['label'] }}&nbsp;&rarr;</a>
</td></tr>
@endif

{{-- Security notice --}}
@if ($draft->callout !== null)
<tr><td class="pad" style="padding:30px 44px 0;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="{{ $calm ? $panel : $soft }}" style="background-color:{{ $calm ? $panel : $soft }};border-left:3px solid {{ $calm ? '#c9c6bd' : $accent }};">
<tr><td style="padding:14px 18px;font-family:{!! $sans !!};font-size:14px;line-height:22px;color:{{ $body }};">
@if ($draft->calloutTitle !== null)
<strong style="display:block;color:{{ $ink }};">{{ $draft->calloutTitle }}</strong>
@endif
{{ $draft->callout }}
@if ($draft->secondaryAction !== null)
<div style="padding-top:8px;"><a href="{{ $draft->secondaryAction['url'] }}" target="_blank" rel="noopener" style="font-weight:600;color:{{ $link }};">{{ $draft->secondaryAction['label'] }}&nbsp;&rarr;</a></div>
@endif
</td></tr>
</table>
</td></tr>
@endif

{{-- Sign-off --}}
<tr><td class="pad" style="padding:30px 44px 36px;"><p style="margin:0;font-family:{!! $serif !!};font-size:15px;line-height:22px;font-style:italic;color:{{ $body }};">{{ $draft->signOff }}</p></td></tr>

</table>
</td></tr>

{{-- Footer --}}
<tr><td class="pad" align="center" style="padding:28px 44px 8px;text-align:center;">
<p style="margin:0 0 4px;font-family:{!! $serif !!};font-size:14px;line-height:20px;font-weight:700;letter-spacing:1px;color:{{ $ink }};">{{ $draft->brand }}</p>
<p style="margin:0 0 6px;font-family:{!! $serif !!};font-size:13px;line-height:18px;font-style:italic;color:{{ $muted }};">{{ $draft->tagline }}</p>
<p style="margin:0 0 16px;font-family:{!! $sans !!};font-size:12px;line-height:18px;font-weight:600;"><a href="{{ $draft->homeUrl }}" target="_blank" rel="noopener" style="color:{{ $link }};">{{ $draft->visitLabel }}</a></p>
@foreach ($draft->footer as $footerLine)
<p style="margin:0 0 8px;font-family:{!! $sans !!};font-size:12px;line-height:18px;color:{{ $muted }};">{{ $footerLine }}</p>
@endforeach
</td></tr>

</table>
<!--[if mso]></td></tr></table><![endif]-->
</td></tr>
</table>
</body>
</html>
