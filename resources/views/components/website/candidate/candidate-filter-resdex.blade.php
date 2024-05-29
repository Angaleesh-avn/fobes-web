@props(['professions', 'experiences', 'educations', 'skills'])

<form id="candidate_search_form" action="{{ route('company.resdex_plans.candidates') }}" method="GET">
    <div class="breadcrumbs style-two">
        <div class="container">
            <div class="row align-items-center ">
                <div class="col-12 position-relative">
                    <div class="breadcrumb-menu">
                        <h6 class="f-size-18 m-0">{{ __('find_candidates') }}</h6>
                        <ul>
                            <li><a href="{{ route('website.home') }}">{{ __('home') }}</a></li>
                            <li>/</li>
                            <li>{{ __('candidates') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="container">
            <div class="jobsearchBox tw-my-6 bg-gray-10 input-transparent height-auto-lg">
                <div class="top-content d-flex flex-column flex-lg-row align-items-center leaflet-map-results">
                    <div class="flex-grow-1 flex-grow-1 fromGroup has-icon banner-select">

                        <select class="rt-selectactive candidate-profession" name="skills[]" multiple>{{ __('skills') }}
                            <option value="" class="d-none" selected disabled>{{ __('skills') }}</option>
                            @foreach ($skills as $skill)
                            <option {{ $skill->id == request('skill') ? 'selected' : '' }}
                                    value="{{ $skill->name }}"> {{ $skill->name }}
                            </option>
                            @endforeach
                        </select>
                        <div class="icon-badge category-icon">
                            <x-svg.layer-icon stroke="var(--primary-500)" width="24" height="24" />
                        </div>
                    </div>

                    <input type="hidden" name="lat" id="lat" value="">
                    <input type="hidden" name="long" id="long" value="">
                    @php
                        $oldLocation = request('location');
                        $map = $setting->default_map;
                    @endphp

                    @if ($map == 'google-map')
                        <div class="inputbox_2 flex-grow-1 fromGroup has-icon">
                            <input type="text" id="searchInput" placeholder="Enter a location..." name="location"
                                value="{{ $oldLocation }}" />
                            <div id="google-map" class="d-none"></div>
                            <div class="icon-badge">
                                <x-svg.location-icon stroke="{{ $setting->frontend_primary_color }}" width="24"
                                    height="24" />
                            </div>
                        </div>
                    @else
                        <div class="inputbox_2 flex-grow-1 fromGroup has-icon">
                            <input name="long" class="leaf_lon" type="hidden">
                            <input name="lat" class="leaf_lat" type="hidden">
                            <input type="text" id="leaflet_search" placeholder="{{ __('enter_location') }}"
                                name="location" value="{{ request('location') }}" class="tw-border-0"
                                autocomplete="off" />

                            <div class="icon-badge">
                                <x-svg.location-icon stroke="{{ $setting->frontend_primary_color }}" width="24"
                                    height="24" />
                            </div>
                        </div>
                    @endif
                    <div class="tw-flex tw-gap-3 tw-items-center">
                        <div class="flex-grow-0 rt-pt-md-20">
                            <button
                                class="btn btn-primary d-block d-md-inline-block ">{{ __('search_candidates') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        
            <div class="row">
                <div class="col-12">
                    <div class="tw-flex tw-justify-between tw-items-center tw-py-3 tw-mb-6"
                        style="border-top: 1px solid #E4E5E8; border-bottom: 1px solid #E4E5E8;">
                        <div class="tw-flex tw-justify-end tw-items-center">
                            <div class="joblist-fliter-gorup !tw-min-w-max">
                                <div class="right-content !tw-mt-0">
                                    <nav>
                                        <div class="nav" id="nav-tab" role="tablist">
                                            <button onclick="styleSwitch('box')" class="nav-link active "
                                                id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home"
                                                type="button" role="tab" aria-controls="nav-home"
                                                aria-selected="true">
                                                <x-svg.box-icon />
                                            </button>
                                            <button onclick="styleSwitch('list')" class="nav-link"
                                                id="nav-profile-tab" data-bs-toggle="tab"
                                                data-bs-target="#nav-profile" type="button" role="tab"
                                                aria-controls="nav-profile" aria-selected="false">
                                                <x-svg.list-icon />
                                            </button>
                                        </div>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    <!--    </div>-->
    <!--</div>-->
</form>

@section('frontend_links')
    @include('map::links')
    <x-map.leaflet.autocomplete_links />
    <style>
        .candidate-profession+.select2-container--default .select2-selection--single {
            border: none !important;
        }
    </style>
@endsection

@section('frontend_scripts')
    <x-map.leaflet.autocomplete_scripts />

    <script>
        function professionFilter(profession) {
            console.log(profession);
            $('input[name=profession]').val(profession)
            $('#candidate_search_form').submit();
        }
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // $('input[type=radio]').on('change', function() {
        //     $('#form').submit();
        // });
    </script>
    <!-- ============== gooogle map ========== -->
    @if ($map == 'google-map')
        <script>
            function initMap() {
                var token = "{{ $setting->google_map_key }}";
                var oldlat = {{ Session::has('location') ? Session::get('location')['lat'] : $setting->default_lat }};
                var oldlng = {{ Session::has('location') ? Session::get('location')['lng'] : $setting->default_long }};
                const map = new google.maps.Map(document.getElementById("google-map"), {
                    zoom: 7,
                    center: {
                        lat: oldlat,
                        lng: oldlng
                    },
                });
                // Search
                var input = document.getElementById('searchInput');
                map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

                let country_code = '{{ current_country_code() }}';
                if (country_code) {
                    var options = {
                        componentRestrictions: {
                            country: country_code
                        }
                    };
                    var autocomplete = new google.maps.places.Autocomplete(input, options);
                } else {
                    var autocomplete = new google.maps.places.Autocomplete(input);
                }

                autocomplete.bindTo('bounds', map);
                var infowindow = new google.maps.InfoWindow();
                var marker = new google.maps.Marker({
                    map: map,
                    anchorPoint: new google.maps.Point(0, -29)
                });
                autocomplete.addListener('place_changed', function() {
                    infowindow.close();
                    marker.setVisible(false);
                    var place = autocomplete.getPlace();
                    const total = place.address_components.length;
                    let amount = '';
                    if (total > 1) {
                        amount = total - 2;
                    }
                    const result = place.address_components.slice(amount);
                    let country = '';
                    let region = '';
                    for (let index = 0; index < result.length; index++) {
                        const element = result[index];
                        if (element.types[0] == 'country') {
                            country = element.long_name;
                        }
                        if (element.types[0] == 'administrative_area_level_1') {
                            const str = element.long_name;
                            const first = str.split(',').shift()
                            region = first;
                        }
                    }
                    const text = region + ',' + country;
                    $('#insertlocation').val(text);
                    $('#lat').val(place.geometry.location.lat());
                    $('#long').val(place.geometry.location.lng());
                    if (place.geometry.viewport) {
                        map.fitBounds(place.geometry.viewport);
                    } else {
                        map.setCenter(place.geometry.location);
                        map.setZoom(17);
                    }
                });
            }
            window.initMap = initMap;
        </script>
        <script>
            @php
                $link1 = 'https://maps.googleapis.com/maps/api/js?key=';
                $link2 = $setting->google_map_key;
                $Link3 = '&callback=initMap&libraries=places,geometry';
                $scr = $link1 . $link2 . $Link3;
            @endphp;
        </script>
        <script src="{{ $scr }}" async defer></script>
    @endif
@endsection
