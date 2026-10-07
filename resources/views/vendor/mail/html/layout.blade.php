<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{{ config('app.name') }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
<style>
    :root {
        --ente-primary: #003399;
        --ente-accent: #0d47d5;
        --ente-bg: #f6f8fb;
        --ente-text: #1a1a1a;
    }
    body, table, td {
        font-family: 'Helvetica Neue', Arial, sans-serif !important;
        color: var(--ente-text);
        background-color: var(--ente-bg);
    }
    .panel-content table, 
    .panel-content td {
        background-color: transparent !important;
    }
    .wrapper {
        background-color: var(--ente-bg) !important;
    }
    .content-cell {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0, 51, 153, 0.08);
        padding: 32px !important;
    }
    .header {
        padding: 30px 0 !important;
    }
    .logo {
        max-width: 160px !important;
        height: auto;
    }
    .button-primary {
        background: linear-gradient(90deg, var(--ente-primary) 0%, var(--ente-accent) 100%) !important;
        border-color: var(--ente-primary) !important;
    }
    .panel {
        border-left: 4px solid var(--ente-primary) !important;
        background: #f0f4ff !important;
    }
    @media only screen and (max-width: 600px) {
        .inner-body { width: 100% !important; }
        .footer { width: 100% !important; }
    }
    @media only screen and (max-width: 500px) {
        .button { width: 100% !important; }
    }
</style>
{!! $head ?? '' !!}
</head>
<body>

<table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="center">
<table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
{!! $header ?? '' !!}

<!-- Email Body -->
<tr>
<td class="body" width="100%" cellpadding="0" cellspacing="0" style="border: hidden !important;">
<table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<!-- Body content -->
<tr>
<td class="content-cell">
{!! Illuminate\Mail\Markdown::parse($slot) !!}

{!! $subcopy ?? '' !!}
</td>
</tr>
</table>
</td>
</tr>

{!! $footer ?? '' !!}
</table>
</td>
</tr>
</table>
</body>
</html>
