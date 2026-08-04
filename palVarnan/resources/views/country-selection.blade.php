<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose a Country — Pal Varnan</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/logo/logo.png') }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #026966 0%, #026966 40%, #026966 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 24px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        .inner { max-width: 620px; width: 100%; }
        h1 { font-size: 56px; font-weight: 800; color: #fff; letter-spacing: -0.03em; line-height: 1.05; margin-bottom: 44px; }
        .flags { display: flex; gap: 40px; margin-bottom: 36px; flex-wrap: wrap; }
        .flag-btn {
            background: none; border: none; cursor: pointer;
            display: flex; flex-direction: column; align-items: center; gap: 14px;
            padding: 0; transition: transform 0.15s, opacity 0.15s;
        }
        .flag-btn:hover { transform: translateY(-4px); opacity: 0.9; }
        .flag-btn:active { transform: scale(0.97); }
        .flag-box {
            width: 180px; height: 110px; border-radius: 6px;
            background: #fff; display: flex; align-items: center;
            justify-content: center; font-size: 80px; overflow: hidden;
        }
        .flag-box img { width: 100%; height: 100%; object-fit: cover; }
        .flag-label { font-size: 15px; font-weight: 700; color: #fff; }
        .divider { width: 100%; height: 1px; background: rgba(255,255,255,0.25); margin-bottom: 28px; }
        .info-title { font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px; }
        .info-text { font-size: 14px; color: rgba(255,255,255,0.8); line-height: 1.6; }
        .error { color: #ffcccc; font-size: 14px; margin-bottom: 16px; }
    </style>
</head>
<body>
    <div class="inner">
        <h1>Hello!<br>Choose a country.</h1>

        @if(session('error'))
            <p class="error">{{ session('error') }}</p>
        @endif

        <div class="flags">
            @foreach($countries as $country)
            <form method="POST" action="/select-country">
                @csrf
                <button type="submit" name="country" value="{{ strtolower($country->name) }}" class="flag-btn">
                    <div class="flag-box">
                        <img src="{{ asset('assets/images/flags/' . strtolower($country->name) . '.png') }}" 
                            alt="{{ $country->name }}"
                            onerror="this.style.display='none'; this.parentElement.innerHTML='🌐';">
                    </div>
                    <span class="flag-label">{{ $country->name }}</span>
                </button>
            </form>
            @endforeach
        </div>

        <div class="divider"></div>
        <p class="info-title">Shopping from another country?</p>
        <p class="info-text">
            International customers can browse palvarnan.com and have orders shipped to any supported address.
        </p>
    </div>
</body>
</html>