<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="language" content="English" />
    <meta name="Expires" content="never" />
    <meta name="revisit-after" content="Daily" />
    <meta name="Author" content="https://www.fobes.in/" />
    <meta name="Distribution" content="Global" />
    <meta name="Rating" content="general" />
    <meta name="geo.region" content="GB" />
    <meta name="geo.placename" content="India" />
    <meta name="search engines" content="ALL" />
    <meta name="email" mailto:content="admin@fobes.in" />
    <meta name="copyright" content="https://www.fobes.in/" />
    <meta property="og:image" content="@yield('og:image')">
    <title>@yield('title') - {{ config('app.name') }}</title>

    @yield("ld-data")

    {{-- Style --}}
    @include('frontend.partials.analytics')
    @include('frontend.partials.links')
    @include('frontend.partials.preloader') <!-- Include the preloader -->
    @yield('css')

    {{-- Custome css and js  --}}
    {!! $setting->header_css !!}
    {!! $setting->header_script !!}

</head>

<body dir="{{ langDirection() }}">
    <input type="hidden" value="{{ current_country_code() }}" id="current_country_code">
    <x-admin.app-mode-alert />
    {{-- Header --}}
    @include('frontend.partials.header')

    {{-- Main --}}
    @yield('main')

    {{-- footer --}}
    @if (!Route::is('candidate.*') && !Route::is('company.*'))
        @include('frontend.partials.footer')
    @endif

    <!-- PWA Button Start -->
    <button class="pwa-install-btn bg-white position-fixed d-none" id="installApp">
        <img src="{{ asset('pwa-btn.png') }}" alt="Install App">
    </button>
    <!-- PWA Button End -->

    <!-- scripts -->
    @include('frontend.partials.scripts')

    <!-- Custom js -->
    {!! $setting->body_script !!}

    <x-frontend.cookies-allowance :cookies="$cookies" />
    <script>
        window.addEventListener('load', function () {
            document.querySelector('.preloader').style.display = 'none';
        });
    </script>

    <!-- PWA Script Start -->
    @if($setting->pwa_enable)
        <script src="{{ asset('/sw.js') }}"></script>
        <script>
            if (!navigator.serviceWorker) {
                navigator.serviceWorker.register("/sw.js").then(function (reg) {
                    console.log("Service worker has been registered for scope: " + reg);
                });
            }

            let deferredPrompt;
            window.addEventListener('beforeinstallprompt', (e) => {
                $('#installApp').removeClass('d-none');
                deferredPrompt = e;
            });

            const installApp = document.getElementById('installApp');
            installApp.addEventListener('click', async () => {
                if (deferredPrompt !== null) {
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    if (outcome === 'accepted') {
                        deferredPrompt = null;
                    }
                }
            });
        </script>
    @endif
    <!-- PWA Script End -->

</body>

</html>
