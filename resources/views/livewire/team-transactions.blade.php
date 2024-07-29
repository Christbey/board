<ul class="list-none mb-6">
    @if($transactions)
        @foreach($transactions as $transaction)
            <x-list-item>
                {{ json_encode($transaction, JSON_PRETTY_PRINT) }}
            </x-list-item>
        @endforeach
    @else
        <x-list-item>No transactions data available.</x-list-item>
    @endif
</ul>
