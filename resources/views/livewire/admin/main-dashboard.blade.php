<div class="p-6 bg-gray-50 min-h-screen" dir="rtl">
    
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg shadow-sm border-r-4 border-green-500 flex justify-between items-center animate-bounce">
            <span>{{ session('message') }}</span>
            <i class="fas fa-check-circle"></i>
        </div>
    @endif

    {{-- الإحصائيات --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
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
        <div class="bg-white p-6 rounded-xl shadow-sm border-r-4 border-gray-800">
            <h3 class="text-gray-500 text-xs font-bold mb-1">أكواد غير مستخدمة</h3>
            <p class="text-2xl font-black text-gray-800">{{ $stats['unused_vouchers'] }}</p>
        </div>
    </div>

    {{-- رادار الرحلات --}}
    <div class="mb-8 bg-white rounded-xl shadow-md border border-purple-100 overflow-hidden">
        <div class="p-4 bg-gradient-to-l from-purple-600 to-indigo-600 flex justify-between items-center text-right">
            <h3 class="text-white font-bold flex items-center gap-2">
                <i class="fas fa-satellite-dish animate-pulse"></i> رادار الرحلات المباشرة
            </h3>
            <button wire:click="createTestTrip" class="bg-white text-purple-600 px-4 py-1 rounded-lg text-xs font-bold hover:bg-purple-50 transition shadow-lg">
                <i class="fas fa-plus-circle"></i> محاكاة طلب رحلة
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
                    <tr class="border-b hover:bg-purple-50 transition-all duration-300">
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
                            <p class="italic font-medium">لا توجد طلبات رحلات نشطة حالياً</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 text-right">
        
        {{-- أكواد الشحن --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 flex flex-col">
            <div class="p-4 bg-gray-800 border-b border-gray-700 flex justify-between items-center">
                <span class="font-bold text-white flex items-center gap-2"><i class="fas fa-ticket-alt"></i> توليد أكواد الشحن</span>
                <i class="fas fa-money-bill-wave text-green-400"></i>
            </div>
            <div class="p-4 bg-gray-50 border-b">
                <div class="grid grid-cols-2 gap-2 mb-4">
                    <div>
                        <label class="text-[10px] font-bold text-gray-500 uppercase">المبلغ</label>
                        <input wire:model="voucherAmount" type="number" class="w-full text-sm border-gray-200 rounded-lg focus:ring-green-500">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-500 uppercase">العدد</label>
                        <input wire:model="voucherCount" type="number" class="w-full text-sm border-gray-200 rounded-lg focus:ring-green-500">
                    </div>
                </div>
                <button wire:click="generateVouchers" class="w-full bg-green-600 text-white py-2 rounded-lg font-bold text-sm hover:bg-green-700 shadow-md transition transform active:scale-95">
                    توليد الأكواد الآن
                </button>
            </div>
            <div class="flex-1 overflow-y-auto max-h-[400px]">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 sticky top-0">
                        <tr class="text-right text-[10px] text-gray-500 uppercase">
                            <th class="p-2">الكود</th>
                            <th class="p-2">القيمة</th>
                            <th class="p-2">الإجراء</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($vouchers as $v)
                        <tr class="hover:bg-green-50 transition group">
                            <td class="p-3 font-mono font-bold text-blue-600 selection:bg-yellow-200">
                                {{ $v->code }}
                            </td>
                            <td class="p-3 font-bold text-gray-700 text-xs">
                                {{ number_format($v->amount) }} <span class="text-[8px]">د.ع</span>
                            </td>
                            <td class="p-3">
                                <button wire:click="deleteVoucher({{ $v->id }})" class="text-red-400 hover:text-red-600 transition">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- إدارة السائقين (المحدثة مع المستندات) --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <div class="p-4 bg-purple-50 border-b border-purple-100 flex flex-col md:flex-row justify-between items-center gap-4">
                <span class="font-bold text-purple-800 flex items-center gap-2 self-start"><i class="fas fa-id-card"></i> طلبات السائقين والمستندات</span>
                <input wire:model.live="searchDriver" type="text" placeholder="ابحث باسم السائق أو رقم الهاتف..." class="text-xs border-gray-200 rounded-lg w-full md:w-64 focus:ring-purple-500">
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-[10px] text-gray-500 border-b">
                            <th class="p-4 text-right">السائق</th>
                            <th class="p-4 text-right">معلومات السيارة</th>
                            <th class="p-4 text-right">الهاتف</th>
                            <th class="p-4 text-center">المستندات</th>
                            <th class="p-4 text-center">الحالة</th>
                            <th class="p-4 text-center">الإجراء</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($latest_drivers as $driver)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    @if($driver->img_personal)
                                        <a href="{{ asset('storage/' . $driver->img_personal) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $driver->img_personal) }}" class="w-10 h-10 rounded-full object-cover border-2 border-purple-200 shadow-sm">
                                        </a>
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center"><i class="fas fa-user text-gray-400"></i></div>
                                    @endif
                                    <div class="flex flex-col">
                                        <span class="font-bold text-gray-800">{{ $driver->name }}</span>
                                        <span class="text-[10px] text-gray-500"><i class="fas fa-phone-alt ml-1"></i>{{ $driver->phone }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <div class="flex flex-col text-[10px] text-gray-600">
                                    <span class="font-medium"><i class="fas fa-car ml-1"></i> {{ $driver->car_color }}</span>
                                    <span><i class="fas fa-hashtag ml-1"></i> {{ $driver->car_plate }}</span>
                                </div>
                            </td>
                            <td class="p-4">
                                <div class="flex flex-col text-[10px] text-gray-600">
                                    <span><i class="fas fa-hashtag ml-1"></i> {{ $driver->phone }}</span>
                                </div>
                            </td>
                            <td class="p-4">
                                <div class="flex justify-center gap-2">
                                    @if($driver->img_id_front)
                                        <a href="{{ asset('storage/' . $driver->img_id_front) }}" target="_blank" title="الهوية - وجه" class="relative group">
                                            <img src="{{ asset('storage/' . $driver->img_id_front) }}" class="w-12 h-8 rounded border border-gray-200 group-hover:opacity-75 transition">
                                            <i class="fas fa-search-plus absolute inset-0 flex items-center justify-center text-white opacity-0 group-hover:opacity-100 text-xs"></i>
                                        </a>
                                    @endif
                                    @if($driver->img_id_back)
                                        <a href="{{ asset('storage/' . $driver->img_id_back) }}" target="_blank" title="الهوية - خلف" class="relative group">
                                            <img src="{{ asset('storage/' . $driver->img_id_back) }}" class="w-12 h-8 rounded border border-gray-200 group-hover:opacity-75 transition">
                                            <i class="fas fa-search-plus absolute inset-0 flex items-center justify-center text-white opacity-0 group-hover:opacity-100 text-xs"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <td class="p-4 text-center">
                                <span class="px-2 py-1 rounded-full text-[10px] font-bold {{ $driver->status == 'active' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                    {{ $driver->status == 'active' ? 'نشط' : 'قيد الانتظار' }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                @if($driver->status == 'pending')
                                    <button wire:click="activateDriver({{ $driver->id }})" class="bg-purple-600 text-white px-4 py-1 rounded-lg text-[10px] font-bold hover:bg-purple-700 transition shadow-sm">
                                        تفعيل الآن
                                    </button>
                                @else
                                    <i class="fas fa-check-double text-green-500"></i>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- إدارة الزبائن --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <div class="p-4 bg-blue-50 border-b border-blue-100 flex flex-col justify-between items-center gap-2">
                <span class="font-bold text-blue-800 flex items-center gap-2 self-start"><i class="fas fa-users"></i> إدارة الزبائن</span>
                <input wire:model.live="searchCustomer" type="text" placeholder="ابحث عن زبون..." class="text-xs border-gray-200 rounded-lg w-full focus:ring-blue-500">
            </div>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-gray-100">
                    @foreach($latest_customers as $customer)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 font-bold">{{ $customer->name }}</td>
                        <td class="p-4 text-gray-500 text-xs">{{ $customer->phone }}</td>
                        <td class="p-4 text-[10px] text-gray-400 italic">{{ $customer->created_at->format('Y/m/d') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>