<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AHC User Guide</title>
    <style>
        html, body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif; color: #111827; }
        .container { max-width: 900px; margin: 0 auto; }
        h1, h2, h3, h4 { page-break-after: avoid; }
        pre, code { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
        pre { background: #0b1020; color: #e5e7eb; padding: 12px; border-radius: 8px; overflow: hidden; }
        code { background: rgba(31,41,55,.08); padding: 0.1em 0.3em; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; }
        blockquote { border-left: 4px solid #e5e7eb; padding-left: 12px; color: #374151; }
        a { color: #2563eb; text-decoration: none; }
        .header { margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid #e5e7eb; }
        .header-title { font-size: 20px; font-weight: 700; }
        .header-sub { font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="header-title">AHC User Guide</div>
        <div class="header-sub">Generated on {{ now()->format('Y-m-d H:i') }}</div>
    </div>

    {!! $html !!}
</div>
</body>
</html>
