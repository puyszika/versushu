@extends('layouts.app')



@section('content')
    <div class="max-w-6xl mx-auto px-4 py-6 ">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-white animate-slide-up">Kezdőlap</h1>
        </div>

        <div class="relative w-full h-64 overflow-hidden rounded-xl shadow mb-8">

            <!-- Slide container -->
            <div id="slider-track" class="flex transition-transform duration-700 ease-in-out w-full h-full" style="width: 100%;">
                @foreach (\App\Models\Slider::all() as $index => $slider)
                    <div class="w-full flex-shrink-0">
                        <img src="{{ asset($slider->image_path) }}" class="w-full h-full object-cover" alt="Slider kép {{ $index + 1 }}">
                    </div>
                @endforeach
            </div>

            <!-- Előző gomb -->
            <button id="prev" class="absolute top-1/2 left-4 transform -translate-y-1/2 bg-black/50 text-white p-2 rounded-full hover:bg-black/70 z-10">
                &lt;
            </button>

            <!-- Következő gomb -->
            <button id="next" class="absolute top-1/2 right-4 transform -translate-y-1/2 bg-black/50 text-white p-2 rounded-full hover:bg-black/70 z-10">
                &gt;
            </button>

            <!-- Lapozó pöttyök -->
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                @foreach (\App\Models\Slider::all() as $index => $slider)
                    <span class="dot w-3 h-3 {{ $index === 0 ? 'bg-white/70' : 'bg-white/30' }} rounded-full cursor-pointer" data-slide="{{ $index }}"></span>
                @endforeach
            </div>
        </div>


        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($posts as $post)
                <div class="bg-white rounded-xl overflow-hidden shadow transition-transform duration-300 hover:-translate-y-1 hover:shadow-lg">
                    {{-- Kép, ha van --}}
                    @if ($post->image)
                        <img src="{{ asset('storage/' . $post->image) }}" alt="Borítókép"
                             class="w-full h-48 object-cover">
                    @endif

                    <div class="p-4">
                        <h2 class="text-xl font-bold mb-2">{{ $post->title }}</h2>
                        <p class="text-gray-600">{{ Str::limit(strip_tags($post->content), 100) }}</p>
                        <a href="{{ route('blog.show', $post) }}"
                           class="text-blue-600 hover:text-blue-800 font-semibold inline-block mt-3 transition">
                            Tovább olvasom →
                        </a>
                    </div>
                </div>
                {{ $posts->links() }}
            @endforeach
        </div>
    </div>


    <script>
        setTimeout(() => {
            const toast = document.getElementById('login-toast');
            if (toast) {
                toast.classList.add('opacity-0');
                setTimeout(() => toast.remove(), 500);
            }
        }, 2000);

        // Opció: kattintásra eltűnik
        document.getElementById('login-toast').addEventListener('click', function () {
            this.remove();
        });
    </script>
    <script>
        const sliderTrack = document.getElementById("slider-track");
        const slides = sliderTrack.children;
        const dots = document.querySelectorAll(".dot");
        let currentSlide = 0;
        const totalSlides = slides.length;

        function updateSlider(index) {
            sliderTrack.style.transform = `translateX(-${index * 100}%)`;
            dots.forEach((dot, i) => {
                dot.classList.toggle("bg-white/70", i === index);
                dot.classList.toggle("bg-white/30", i !== index);
            });
        }

        document.getElementById("next").addEventListener("click", () => {
            currentSlide = (currentSlide + 1) % totalSlides;
            updateSlider(currentSlide);
        });

        document.getElementById("prev").addEventListener("click", () => {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            updateSlider(currentSlide);
        });

        dots.forEach(dot => {
            dot.addEventListener("click", () => {
                currentSlide = parseInt(dot.dataset.slide);
                updateSlider(currentSlide);
            });
        });

        // Automata csúszás
        setInterval(() => {
            currentSlide = (currentSlide + 1) % totalSlides;
            updateSlider(currentSlide);
        }, 5000);
    </script>
    <x-footer></x-footer>
@endsection
