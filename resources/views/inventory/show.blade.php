@extends('layouts.app')

@section('title', 'Equipment Details')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <a href="{{ route('inventory.index') }}" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
            ← Back to Dashboard
        </a>
        <h1 class="text-3xl font-bold text-gray-900">{{ $equipment->name }}</h1>
        <p class="mt-2 text-sm text-gray-600">Equipment Details</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Basic Information</h2>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ ucfirst($equipment->type) }}
                            </span>
                        </dd>
                    </div>
                    @if($equipment->brand)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Brand</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <a href="{{ route('brands.show', $equipment->brand->id) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                    {{ $equipment->brand->name }}
                                </a>
                            </dd>
                        </div>
                    @endif
                    @if($equipment->model)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Model</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $equipment->model }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @php
                                $statusColors = [
                                    'available' => 'bg-green-100 text-green-800',
                                    'maintenance' => 'bg-yellow-100 text-yellow-800',
                                    'damaged' => 'bg-red-100 text-red-800',
                                    'retired' => 'bg-gray-100 text-gray-800',
                                ];
                                $color = $statusColors[$equipment->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                {{ ucfirst($equipment->status) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total Quantity</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $equipment->quantity }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Available Quantity</dt>
                        <dd class="mt-1 text-sm {{ $equipment->isLowStock() ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                            {{ $equipment->available_quantity }}
                            @if($equipment->isLowStock())
                                <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    ⚠️ Low Stock
                                </span>
                            @endif
                        </dd>
                    </div>
                    @if($equipment->minimum_stock_amount > 0)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Minimum Stock Amount</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $equipment->minimum_stock_amount }}
                                @if($equipment->isLowStock())
                                    <span class="text-red-600">(Shortage: {{ $equipment->minimum_stock_amount - $equipment->available_quantity }})</span>
                                @endif
                            </dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Location</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $equipment->location ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Price</dt>
                        <dd class="mt-1 text-sm text-gray-900">${{ number_format($equipment->price ?? 0, 2) }}</dd>
                    </div>
                    @if($equipment->purchase_date)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Purchase Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $equipment->purchase_date->format('M d, Y') }}</dd>
                        </div>
                    @endif
                    @if($equipment->next_maintenance_date)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Next Maintenance</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $equipment->next_maintenance_date->format('M d, Y') }}</dd>
                        </div>
                    @endif
                </dl>
                @if($equipment->description)
                    <div class="mt-6">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $equipment->description }}</dd>
                    </div>
                @endif
            </div>

            <!-- Features (Decorator Pattern) -->
            @if($equipment->features->count() > 0)
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Features (Decorator Pattern)</h2>
                    <div class="space-y-3">
                        @foreach($equipment->features as $feature)
                            <div class="border-l-4 border-blue-500 pl-4 py-2">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-medium text-gray-900">{{ $feature->feature_name }}</h3>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ ucfirst($feature->feature_type) }}
                                    </span>
                                </div>
                                @if($feature->feature_value)
                                    <p class="text-sm text-gray-600 mt-1">{{ $feature->feature_value }}</p>
                                @endif
                                @if($feature->expiry_date)
                                    <p class="text-xs text-gray-500 mt-1">
                                        Expires: {{ $feature->expiry_date->format('M d, Y') }}
                                        @if($feature->isExpired())
                                            <span class="text-red-600">(Expired)</span>
                                        @endif
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Transactions -->
            @if($equipment->transactions->count() > 0)
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Transactions</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($equipment->transactions->take(5) as $transaction)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $transaction->transaction_date->format('M d, Y') }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ ucfirst($transaction->transaction_type) }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $transaction->quantity }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $transaction->user->name ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- Actions Sidebar -->
        <div class="space-y-6">
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions</h2>
                <div class="space-y-3">
                    <a href="{{ route('inventory.edit', $equipment->id) }}" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 block text-center">
                        Edit Equipment
                    </a>
                    
                    @if($equipment->isAvailable())
                        <button onclick="showCheckoutModal()" class="w-full bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-green-700">
                            Checkout Equipment
                        </button>
                    @endif
                    
                    <button onclick="showReturnModal()" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700">
                        Return Equipment
                    </button>
                </div>
            </div>

            <!-- Utilization -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Utilization</h2>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Utilization Rate</span>
                        <span class="font-medium">{{ number_format($equipment->getUtilizationPercentage(), 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $equipment->getUtilizationPercentage() }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Stock Level -->
            @if($equipment->minimum_stock_amount > 0)
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Stock Level</h2>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Stock Level</span>
                            <span class="font-medium {{ $equipment->isLowStock() ? 'text-red-600' : 'text-green-600' }}">
                                {{ $equipment->isLowStock() ? 'Low Stock' : 'Adequate' }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            @php
                                $stockPercentage = $equipment->getStockLevelPercentage();
                                $barColor = $equipment->isLowStock() ? 'bg-red-600' : ($stockPercentage < 150 ? 'bg-yellow-500' : 'bg-green-600');
                            @endphp
                            <div class="{{ $barColor }} h-2 rounded-full" style="width: {{ min(100, $stockPercentage) }}%"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            Available: {{ $equipment->available_quantity }} / Minimum: {{ $equipment->minimum_stock_amount }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Checkout Modal -->
<div id="checkoutModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Checkout Equipment</h3>
        <form action="{{ route('inventory.checkout', $equipment->id) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Quantity</label>
                    <input type="number" name="quantity" min="1" max="{{ $equipment->available_quantity }}" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Expected Return Date</label>
                    <input type="date" name="expected_return_date" min="{{ date('Y-m-d') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="hideCheckoutModal()" class="px-4 py-2 bg-gray-300 rounded-md">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">Checkout</button>
            </div>
        </form>
    </div>
</div>

<!-- Return Modal -->
<div id="returnModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Return Equipment</h3>
        <form action="{{ route('inventory.return', $equipment->id) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Quantity</label>
                    <input type="number" name="quantity" min="1" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="hideReturnModal()" class="px-4 py-2 bg-gray-300 rounded-md">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Return</button>
            </div>
        </form>
    </div>
</div>

<script>
    function showCheckoutModal() {
        document.getElementById('checkoutModal').classList.remove('hidden');
    }
    function hideCheckoutModal() {
        document.getElementById('checkoutModal').classList.add('hidden');
    }
    function showReturnModal() {
        document.getElementById('returnModal').classList.remove('hidden');
    }
    function hideReturnModal() {
        document.getElementById('returnModal').classList.add('hidden');
    }
</script>
@endsection

