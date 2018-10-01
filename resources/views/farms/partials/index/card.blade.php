<div class="card">
    <a href="{{ url(route('farms.show', ['farm' => $farm->slug])) }}">
        <img src="{{ $farm->display_image_url }}" alt="{{ $farm->display_name }}" class="card-img-top" />
    </a>
    <div class="card-body">
        <a href="{{ url(route('farms.show', ['farm' => $farm->slug])) }}" class="btn btn-outline-primary pull-right">
            <i class="fa fa-map-marker"></i>
        </a>
        <h4 class="card-title">
            <a href="{{ url(route('farms.show', ['farm' => $farm->slug])) }}">
                {{ $farm->display_name }}
            </a>
        </h4>
        <p class="card-text">
            {{ config('bitcorn.access_token') }}: {{ $farm->accessBalance()->quantity_normalized }}
        </p>
    </div>
    <div class="card-footer">
        <div class="row text-muted">
            <div class="col">
                {{ $farm->tx ? $farm->tx->display_confirmed_at : $farm->confirmed_at }}
            </div>
            <div class="col text-right">
                Harvests: {{ $farm->harvests_count }}
            </div>
        </div>
    </div>
</div>