<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $code ?? 500 }} — {{ $title ?? 'Terjadi Kesalahan' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: "Plus Jakarta Sans", system-ui, -apple-system, "Segoe UI", sans-serif;
            background: #F5F9F3;
            color: #171717;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            background: #ffffff;
            border: 1px solid #E8F0E5;
            border-radius: 20px;
            box-shadow: 0 8px 24px rgba(26, 22, 19, 0.06);
            max-width: 480px;
            width: 100%;
            padding: 48px 40px;
            text-align: center;
        }
        .code {
            font-size: 72px;
            font-weight: 800;
            line-height: 1;
            color: #2D5A27;
            letter-spacing: -0.02em;
        }
        .icon-wrap {
            width: 72px; height: 72px;
            border-radius: 999px;
            background: #E8F0E5;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
        }
        .icon-wrap svg { width: 34px; height: 34px; color: #2D5A27; }
        h1 { font-size: 22px; font-weight: 700; margin-top: 18px; }
        p.message { font-size: 15px; color: #737373; margin-top: 10px; line-height: 1.6; }
        .detail {
            margin-top: 18px;
            background: #FEF2F2;
            border: 1px solid #FECACA;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 13px;
            color: #991B1B;
            text-align: left;
            word-break: break-word;
            display: none;
        }
        .actions { margin-top: 28px; display: flex; flex-direction: column; gap: 10px; }
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 12px 20px; border-radius: 12px; font-size: 14px; font-weight: 600;
            text-decoration: none; transition: all .15s ease; cursor: pointer; border: none;
        }
        .btn-primary { background: #2D5A27; color: #fff; }
        .btn-primary:hover { background: #1E3D1A; }
        .btn-secondary { background: #fff; color: #2D5A27; border: 1.5px solid #2D5A27; }
        .btn-secondary:hover { background: #E8F0E5; }
    </style>
</head>
<body>
    <main class="card">
        @isset($icon)
            <div class="icon-wrap">{{ $icon }}</div>
        @endisset
        <div class="code">{{ $code ?? 500 }}</div>
        <h1>{{ $title ?? 'Terjadi Kesalahan' }}</h1>
        <p class="message">{{ $message ?? 'Maaf, terjadi kesalahan yang tidak terduga. Tim kami sudah mencatat masalah ini.' }}</p>

        @if (!empty($detail))
            <div class="detail" style="display:block;">{{ $detail }}</div>
        @endif

        <div class="actions">
            @php $home = request()->is('admin') || request()->is('admin/*') ? url('/admin') : url('/'); @endphp
            <a href="{{ $home }}" class="btn btn-primary">
                {{ request()->is('admin') || request()->is('admin/*') ? 'Kembali ke Dashboard' : 'Kembali ke Beranda' }}
            </a>
            <a href="javascript:history.back()" class="btn btn-secondary">Kembali ke Halaman Sebelumnya</a>
        </div>
    </main>
</body>
</html>
