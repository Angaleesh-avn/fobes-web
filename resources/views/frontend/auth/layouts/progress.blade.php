<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <title>{{ __('setup_progress') }}</title>
    <link rel="stylesheet" href="{{ asset('frontend') }}/assets/css/bootstrap-datepicker.min.css">
    @include('frontend.partials.links')
    @yield('css')
</head>

<body dir="{{ langDirection() }}">

    @yield('content')
    <div class="rt-mobile-menu-overlay"></div><!-- /.rt-mobile-menu-overlay -->

    <!-- PWA Button Start -->
    <button class="pwa-install-btn bg-white position-fixed d-none" id="installApp">
        <img src="{{ asset('pwa-btn.png') }}" alt="Install App">
    </button>
    <!-- PWA Button End -->

    <!-- scripts -->
    @include('frontend.partials.scripts')
    <script src="{{ asset('frontend/assets/js/bootstrap-datepicker.min.js') }}"></script>
    @yield('script')

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
