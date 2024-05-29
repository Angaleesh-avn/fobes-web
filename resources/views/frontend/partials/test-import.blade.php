{{-- For testing environment --}}
@if (config('templatecookie.testing_mode'))
    @php
        $setting = App\Models\Setting::first();
        $cms_setting = App\Models\Cms::first()
    @endphp
@endif
{{-- For testing environment --}}
