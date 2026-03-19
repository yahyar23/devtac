<div class="p-6 bg-gray-50 min-h-screen" dir="rtl">
    
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg shadow-sm border-r-4 border-green-500 flex justify-between items-center animate-bounce">
            <span>{{ session('message') }}</span>
            <i class="fas fa-check-circle"></i>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border-r-4 border-purple-500">
            <h3 class="text-gray-500 text-xs font-bold mb-1">إجمالي السائقين</h3>
            <p class="text-2xl font-black text-gray-800">{{ $stats['total_drivers'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border-r-4 border-orange-500">
            <h3 class="text-gray-500 text-xs font-bold mb-1">بانتظار التفعيل</h3>
            <p class="text-2xl font-black text-orange-600">{{ $stats['pending_drivers'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border-r-4 border-blue-500">
            <h3 class="text-gray-500 text-xs font-bold mb-1">إجمالي الزبائن</h3>
            <p class="text-2xl font-black text-blue-600">{{ $stats['total_customers'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border-r-4 border-green-500">
            <h3 class="text-gray-500 text-xs font-bold mb-1">إجمالي المحافظ</h3>
            <p class="text-2xl font-black text-green-600">{{ number_format($stats['total_wallet']) }} <small class="text-[10px]">د.ع</small></p>
        </div>
    </div>

    <div class="mb-8 bg-white rounded-xl shadow-md border border-purple-100 overflow-hidden">
        <div class="p-4 bg-gradient-to-l from-purple-600 to-indigo-600 flex justify-between items-center text-right">
            <h3 class="text-white font-bold flex items-center gap-2">
                <i class="fas fa-satellite-dish animate-pulse"></i> رادار الرحلات المباشرة
            </h3>
            <button wire:click="createTestTrip" class="bg-white text-purple-600 px-4 py-1 rounded-lg text-xs font-bold hover:bg-purple-50 transition shadow-lg">
                <i class="fas fa-plus-circle"></i> محاكاة طلب من ياسر
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 border-b">
                        <th class="p-4">الزبون</th>
                        <th class="p-4">مسار الرحلة (من -> إلى)</th>
                        <th class="p-4">التكلفة والمسافة</th>
                        <th class="p-4">الحالة الحالية</th>
                        <th class="p-4">الإجراء / السائق</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($active_trips as $trip)
                    <tr class="border-b hover:bg-purple-50 transition transition-all duration-300">
                        <td class="p-4 font-bold text-gray-700">
                            <i class="fas fa-user-circle text-gray-400 ml-1"></i> {{ $trip->customer->name }}
                        </td>
                        <td class="p-4 text-xs">
                            <div class="flex flex-col gap-1">
                                <span class="text-green-600 font-medium"><i class="fas fa-map-marker-alt"></i> {{ $trip->pickup_location }}</span>
                                <span class="text-red-600 font-medium"><i class="fas fa-flag-checkered"></i> {{ $trip->dropoff_location }}</span>
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="font-bold text-gray-900">{{ number_format($trip->fare) }} <small class="text-[10px]">د.ع</small></div>
                            <div class="text-[10px] text-gray-400">{{ $trip->distance }} كم | {{ $trip->duration }} دقيقة</div>
                        </td>
                        <td class="p-4">
                            @php
                                $statusMap = [
                                    'pending'   => ['class' => 'bg-orange-100 text-orange-700 animate-pulse', 'label' => 'جاري البحث...'],
                                    'accepted'  => ['class' => 'bg-blue-100 text-blue-700', 'label' => 'تم القبول'],
                                    'ongoing'   => ['class' => 'bg-yellow-100 text-yellow-800', 'label' => 'في الطريق'],
                                    'completed' => ['class' => 'bg-green-100 text-green-700', 'label' => 'مكتملة'],
                                ];
                                $currentStatus = $statusMap[$trip->status] ?? ['class' => 'bg-gray-100', 'label' => $trip->status];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold {{ $currentStatus['class'] }}">
                                {{ $currentStatus['label'] }}
                            </span>
                        </td>
                        <td class="p-4">
                            @if($trip->status == 'pending')
                                <button wire:click="acceptTrip({{ $trip->id }})" 
                                        class="bg-indigo-600 text-white px-4 py-1 rounded-md text-[10px] font-bold hover:bg-indigo-700 shadow-sm transition">
                                    قبول كـ (أحمد)
                                </button>
                            @else
                                <div class="flex items-center gap-2 text-gray-600 font-bold">
                                    <i class="fas fa-steering-wheel"></i> {{ $trip->driver->name ?? '---' }}
                                </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center text-gray-400">
                            <i class="fas fa-box-open text-4xl mb-3 block"></i>
                            <p class="italic font-medium">لا توجد طلبات رحلات نشطة حالياً في النظام</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 text-right">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <div class="p-4 bg-purple-50 border-b border-purple-100 flex flex-col md:flex-row justify-between items-center gap-4">
                <span class="font-bold text-purple-800 flex items-center gap-2"><i class="fas fa-id-card"></i> إدارة السائقين</span>
                <input wire:model.live="searchDriver" type="text" placeholder="ابحث عن سائق..." class="text-xs border-gray-200 rounded-lg w-full md:w-48 focus:ring-purple-500">
            </div>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-gray-100 text-right">
                    @foreach($latest_drivers as $driver)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 font-bold">{{ $driver->name }}</td>
                        <td class="p-4 text-gray-500">{{ $driver->phone }}</td>
                        <td class="p-4">
                            <span class="px-2 py-1 rounded-full text-[10px] font-bold {{ $driver->status == 'active' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                {{ $driver->status == 'active' ? 'نشط' : 'قيد الانتظار' }}
                            </span>
                        </td>
                        <td class="p-4">
                            @if($driver->status == 'pending')
                                <button wire:click="activateDriver({{ $driver->id }})" class="bg-purple-600 text-white px-3 py-1 rounded text-xs hover:bg-purple-700 transition">تفعيل</button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <div class="p-4 bg-blue-50 border-b border-blue-100 flex flex-col md:flex-row justify-between items-center gap-4">
                <span class="font-bold text-blue-800 flex items-center gap-2"><i class="fas fa-users"></i> إدارة الزبائن</span>
                <input wire:model.live="searchCustomer" type="text" placeholder="ابحث عن زبون..." class="text-xs border-gray-200 rounded-lg w-full md:w-48 focus:ring-blue-500">
            </div>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-gray-100 text-right">
                    @foreach($latest_customers as $customer)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 font-bold">{{ $customer->name }}</td>
                        <td class="p-4 text-gray-500">{{ $customer->phone }}</td>
                        <td class="p-4 text-xs text-gray-400">{{ $customer->created_at->format('Y/m/d') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>