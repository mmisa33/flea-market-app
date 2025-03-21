<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>coachtechフリマ</title>
    <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css" />
    <link rel="stylesheet" href="{{ asset('css/common.css')}}">
    @yield('css')
</head>

<body>
    <div class="app">
        <header class="header">
            {{--  サイトタイトル  --}}
            <div class="header__logo">
                <img src="{{ asset('images/logo.svg') }}" alt="coachtechフリマ">
            </div>

            {{--  ナビ   --}}
            <div class="header__nav">
                @yield('link')
            </div>
        </header>

            {{--  メインコンテンツ  --}}
        <main class="content">
            @yield('content')
        </main>
    </div>
</body>

</html>