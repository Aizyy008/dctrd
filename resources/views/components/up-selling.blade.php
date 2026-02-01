@props(['products', 'title', 'slider', 'display', 'type'])

@push('styles_top')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.css"
        integrity="sha512-UTNP5BXLIptsaj5WdKFrkFov94lDx+eBvbKyoe1YAfjeRPC+gT5kyZ10kOHCfNZqEui1sxmqvodNUx3KbuYI/A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush

@if ($products->isNotEmpty())
    <div class="mt-3 mb-3">
        <h2 class="section-title after-line mb-3">{{ trans('update.Recommend Items') }}</h2>
        <div class="owl-carousel owl-upselling owl-theme">
            @foreach ($products as $product)
                <div class="item">
                    @if ($type == 'course')
                        @include('web.default.includes.webinar.grid-card', ['webinar' => $product])
                    @elseif($type == 'product')
                        @include('web.default.products.includes.card', ['product' => $product])
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endif

@php
    $sliderCount = $type == 'course' ? 2 : 3;
@endphp

@push('scripts_bottom')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"
        integrity="sha512-bPs7Ae6pVvhOSiIcyUClR7/q2OAsRiovw4vAkX+zJbw3ShAeeqezq50RIIcIURq7Oa20rW2n2q+fyXBNcU9lrw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        var slider_count = '{{ $sliderCount }}';
        $('.owl-upselling').owlCarousel({
            loop: true,
            margin: 10,
            nav: false,
            dots: true,
            autoplay: true,
            rtl: $('html').attr('lang') === 'ar',
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: slider_count
                },
                1000: {
                    items: slider_count
                }
            }
        })
    </script>
@endpush
