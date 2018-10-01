@if($balances->count())
<div class="card mb-4">
    <div class="card-header">
        Bitcorn Farms
    </div>
    <div class="table-responsive">
        <table class="table mb-0" style="overflow-y: auto;white-space: nowrap;">
            <thead>
                <th scope="col" style="width: 40px">#</th>
                <th scope="col">Farm</th>
                <th scope="col">Balance</th>
                <th scope="col">Percent</th>
            </thead>
            <tbody>
                @foreach($balances as $balance)
                    <tr>
                        <th scope="row">{{ $loop->iteration }}.</th>
                        <td><a href="{{ route('farms.show', ['farm' => $balance->farm->slug]) }}">{{ $balance->farm->name }}</a></td>
                        <td>{{ $balance->assetModel->divisible ? $balance->quantity_normalized : number_format($balance->quantity_normalized) }}</td>
                        <td>{{ number_format($balance->quantity_normalized / $token->asset->supply_normalized * 100, 2) }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif