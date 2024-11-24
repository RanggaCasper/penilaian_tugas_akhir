<div class="card">
    @isset($title)
        <div class="card-header">
            <h4 class="card-title mb-0">{{ $title }}</h4>
        </div>
    @endisset
    
    @isset($img)
    <img class="card-img-top img-fluid" src="{{ Storage::url($img) }}" alt="Card image">
    @endisset

    <div class="card-body">
        {{ $slot }}
    </div>
</div>
