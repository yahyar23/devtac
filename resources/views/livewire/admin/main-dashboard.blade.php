<div class="p-6 bg-gray-50 min-h-screen" dir="rtl">
    
    {{-- رسائل التنبيه --}}
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg shadow-sm border-r-4 border-green-500 flex justify-between items-center animate-pulse">
            <span>{{ session('message') }}</span>
            <i class="fas fa-check-circle"></i>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg shadow-sm border-r-4 border-red-500 flex justify-between items-center">
            <span>{{ session('error') }}</span>
            <i class="fas fa-exclamation-triangle"></i>
        </div>
    @endif

    {{-- الإحصائيات المعدلة --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8 text-right font-sans">
        <div class="bg-white p-6 rounded-xl shadow-sm border-r-4 border-purple-500">
            <h3 class="text-gray-500 text-xs font-bold mb-1">إجمالي المندوبين</h3>
            <p class="text-2xl font-black text-gray-800">{{ $stats['total_drivers'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border-r-4 border-orange-500">
            <h3 class="text-gray-500 text-xs font-bold mb-1">مندوبين بانتظار التفعيل</h3>
            <p class="text-2xl font-black text-orange-600">{{ $stats['pending_drivers'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border-r-4 border-blue-500">
            <h3 class="text-gray-500 text-xs font-bold mb-1">إجمالي الزبائن</h3>
            <p class="text-2xl font-black text-blue-600">{{ $stats['total_customers'] }}</p>
        </div>
        {{-- إجمالي مستحقات المندوبين --}}
        <div class="bg-white p-4 rounded-xl shadow-sm border-r-4 border-indigo-800">
            <h3 class="text-gray-500 text-[10px] font-bold mb-1">مستحقات المندوبين (Drivers)</h3>
            <p class="text-xl font-black text-indigo-800">{{ number_format($stats['drivers_total_balance'] ?? 0) }} <small class="text-[10px]">د.ع</small></p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border-r-4 border-green-500">
            <h3 class="text-gray-500 text-xs font-bold mb-1">إجمالي مستحقات الزبائن</h3>
            <p class="text-2xl font-black text-green-600">{{ number_format($stats['customers_total_balance'] ?? 0) }} <small class="text-[10px]">د.ع</small></p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border-r-4 border-red-500">
            <h3 class="text-gray-500 text-xs font-bold mb-1">طلبات صرف معلقة</h3>
            <p class="text-2xl font-black text-red-600">{{ $stats['pending_payouts'] }}</p>
        </div>
    </div>

    {{-- رادار طلبات التوصيل --}}
    <div class="mb-8 bg-white rounded-xl shadow-md border border-indigo-100 overflow-hidden text-right font-sans">
        <div class="p-4 bg-gradient-to-l from-indigo-600 to-blue-600 flex justify-between items-center">
            <h3 class="text-white font-bold flex items-center gap-2">
                <i class="fas fa-truck-loading animate-bounce"></i> رادار طلبات التوصيل المباشرة
            </h3>
            <button wire:click="createTestTrip" class="bg-white text-indigo-600 px-4 py-1 rounded-lg text-xs font-bold hover:bg-indigo-50 transition shadow-lg">
                <i class="fas fa-plus-circle"></i> محاكاة طلب توصيل
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 border-b">
                        <th class="p-4 text-right">الزبون</th>
                        <th class="p-4 text-right">مسار التوصيل (من -> إلى)</th>
                        <th class="p-4 text-right">سعر البضاعة + التوصيل</th>
                        <th class="p-4 text-center">نوع الطلب</th>
                        <th class="p-4 text-center">حالة الطلب</th>
                        <th class="p-4 text-center">المندوب المسؤول</th>
                        <th class="p-4 text-center text-red-500">إجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($active_trips as $trip)
                    <tr class="border-b hover:bg-blue-50 transition-all duration-300">
                        <td class="p-4 font-bold text-gray-700">
                            <i class="fas fa-user-tag text-blue-400 ml-1"></i> {{ $trip->customer->name ?? 'مجهول' }}
                        </td>
                        <td class="p-4 text-xs">
                            <div class="flex flex-col gap-1">
                                <span class="text-green-600 font-medium"><i class="fas fa-box"></i> {{ $trip->pickup_location }}</span>
                                <span class="text-red-600 font-medium"><i class="fas fa-map-marked-alt"></i> {{ $trip->dropoff_location }}</span>
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="font-bold text-indigo-900">{{ number_format($trip->package_price) }} <small class="text-[8px]">بضاعة</small></div>
                            <div class="text-[11px] text-gray-500 font-bold border-t mt-1">+ {{ number_format($trip->fare) }} <small class="text-[8px]">توصيل</small></div>
                            @if(!empty($trip->order_details))
                                <div class="text-[11px] text-gray-600 font-bold mt-1 border-t pt-1">
                                    عدد القطع: {{ collect($trip->order_details)->sum('qty') }}
                                </div>
                            @endif
                        </td>
                        <td class="p-4 font-bold text-gray-700">
                             {{ $trip->package_type ?? 'مجهول' }}
                        </td>
                        <td class="p-4 text-center">
                            @php
                                $statusMap = [
                                    'pending'   => ['class' => 'bg-orange-100 text-orange-700 animate-pulse', 'label' => 'بانتظار مندوب'],
                                    'accepted'  => ['class' => 'bg-blue-100 text-blue-700', 'label' => 'تم الاستلام'],
                                    'ongoing'   => ['class' => 'bg-yellow-100 text-yellow-800', 'label' => 'في الطريق'],
                                    'completed' => ['class' => 'bg-green-100 text-green-700', 'label' => 'تم التسليم'],
                                ];
                                $currentStatus = $statusMap[$trip->status] ?? ['class' => 'bg-gray-100', 'label' => $trip->status];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold {{ $currentStatus['class'] }}">
                                {{ $currentStatus['label'] }}
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            @if($trip->status == 'pending')
                                <button wire:click="acceptTrip({{ $trip->id }})" class="bg-indigo-600 text-white px-3 py-1 rounded-md text-[10px] font-bold hover:bg-indigo-700 shadow-sm transition">
                                    تعيين مندوب (تجريبي)
                                </button>
                            @else
                                <div class="flex items-center justify-center gap-2 text-gray-600 font-bold text-xs">
                                    <i class="fas fa-truck text-indigo-400"></i> {{ $trip->driver->name ?? 'غير محدد' }}
                                </div>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            <button wire:click="deleteTrip({{ $trip->id }})" 
                                    onclick="confirm('هل أنت متأكد من حذف هذا الطلب نهائياً؟') || event.stopImmediatePropagation()"
                                    class="text-red-400 hover:text-red-700 p-2 transition transform hover:scale-110">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-12 text-center text-gray-400 font-bold italic">
                            <i class="fas fa-shipping-fast text-4xl mb-3 block opacity-20"></i> لا توجد عمليات توصيل جارية
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- إدارة مستحقات الزبائن (صرف المبالغ) --}}
    <div class="mb-8 bg-white rounded-xl shadow-lg border-t-4 border-green-500 overflow-hidden text-right font-sans">
        <div class="p-4 bg-green-50 flex justify-between items-center border-b border-green-100">
            <h3 class="text-green-800 font-black flex items-center gap-2 italic">
                <i class="fas fa-hand-holding-usd text-xl"></i> طلبات تصفية الحسابات (المتاجر)
            </h3>
            <span class="bg-green-600 text-white px-3 py-1 rounded-lg text-[10px] font-bold shadow-sm">
                {{ $payout_requests->count() }} طلب معلق
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 border-b">
                    <tr>
                        <th class="p-4 text-right">صاحب الطلب (الزبون)</th>
                        <th class="p-4 text-right">رصيد المحفظة المستحق</th>
                        <th class="p-4 text-right">وقت الطلب</th>
                        <th class="p-4 text-center">الإجراء المالي</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($payout_requests as $req)
                    <tr class="hover:bg-green-50/50 transition group">
                        <td class="p-4 font-bold text-gray-800">{{ $req->user->name ?? 'مجهول' }}</td>
                        <td class="p-4">
                            <span class="text-lg font-black text-green-700">{{ number_format($req->amount ?? 0) }}</span>
                            <small class="text-[10px] text-gray-400 font-bold">د.ع</small>
                        </td>
                        <td class="p-4 text-xs text-gray-500">{{ $req->created_at->diffForHumans() }}</td>
                        <td class="p-4 text-center">
                            <button wire:click="approvePayout({{ $req->id }})" 
                                    onclick="confirm('هل تم تسليم المبلغ يدوياً؟ سيتم تصفير محفظة الزبون فوراً') || event.stopImmediatePropagation()"
                                    class="bg-green-600 text-white px-5 py-2 rounded-lg text-xs font-black hover:bg-green-700 shadow-md transition transform group-hover:scale-105">
                                <i class="fas fa-check-double ml-1"></i> تم تسليم المبلغ (تصفية)
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-gray-400 font-medium italic">لا توجد طلبات صرف مبالغ حالياً</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- سجل المتاجر والزبائن --}}
    <div class="mb-8 bg-white rounded-xl shadow-lg border-t-4 border-blue-400 overflow-hidden text-right font-sans">
        <div class="p-4 bg-blue-50 flex justify-between items-center border-b border-blue-100">
            <h3 class="text-blue-800 font-black flex items-center gap-2">
                <i class="fas fa-store text-xl"></i> سجل المتاجر والزبائن
            </h3>
            <input wire:model.live="searchCustomer" type="text" placeholder="ابحث باسم المتجر أو الهاتف..." class="text-xs border-gray-200 rounded-lg w-64 focus:ring-blue-500">
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 border-b">
                        <th class="p-4 text-right">المتجر / الزبون</th>
                        <th class="p-4 text-right">رقم الهاتف</th>
                        <th class="p-4 text-center">الرصيد في المحفظة</th>
                        <th class="p-4 text-center">تاريخ الانضمام</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($all_customers as $cust)
                    <tr class="hover:bg-blue-50/30 transition border-b border-gray-50">
                        <td class="p-4 font-bold text-gray-800">
                             <i class="fas fa-user-circle text-blue-300 ml-1"></i> {{ $cust->name }}
                        </td>
                        <td class="p-4 font-sans text-gray-600">{{ $cust->phone }}</td>
                        <td class="p-4 text-center">
                            <span class="text-green-700 font-black">{{ number_format($cust->balance) }} د.ع</span>
                        </td>
                        <td class="p-4 text-center text-gray-400 text-xs">{{ $cust->created_at->format('Y-m-d') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- الأكواد والمندوبين --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 text-right font-sans">
        
        {{-- أكواد الشحن --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 flex flex-col">
            <div class="p-4 bg-gray-800 border-b border-gray-700 flex justify-between items-center text-white">
                <span class="font-bold flex items-center gap-2"><i class="fas fa-ticket-alt"></i> توليد أكواد الشحن</span>
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
                <button wire:click="generateVouchers" class="w-full bg-green-600 text-white py-2 rounded-lg font-bold text-sm hover:bg-green-700 shadow-md transition">
                    توليد الأكواد الآن
                </button>
            </div>
            <div class="flex-1 overflow-y-auto max-h-[300px]">
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-gray-50">
                        @foreach($vouchers as $v)
                        <tr class="hover:bg-green-50 transition group">
                            <td class="p-3 font-mono font-bold text-blue-600">{{ $v->code }}</td>
                            <td class="p-3 font-bold text-gray-700 text-xs text-left">{{ number_format($v->amount) }} د.ع</td>
                            <td class="p-3 text-center">
                                <button wire:click="deleteVoucher({{ $v->id }})" class="text-red-300 hover:text-red-600 transition">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- إدارة المندوبين --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <div class="p-4 bg-purple-50 border-b border-purple-100 flex flex-col md:flex-row justify-between items-center gap-4">
                <span class="font-bold text-purple-800 flex items-center gap-2 self-start"><i class="fas fa-id-card"></i> طلبات المندوبين والمستندات</span>
                <input wire:model.live="searchDriver" type="text" placeholder="ابحث باسم المندوب..." class="text-xs border-gray-200 rounded-lg w-full md:w-64 focus:ring-purple-500">
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-[10px] text-gray-500 border-b">
                            <th class="p-4 text-right">المندوب</th>
                            <th class="p-4 text-right">معلومات المركبة</th>
                            <th class="p-4 text-center">الحالة</th>
                            <th class="p-4 text-center">الإجراء</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($latest_drivers as $driver)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-500">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-gray-800">{{ $driver->name }}</span>
                                        <span class="text-[10px] text-gray-500 font-bold">{{ $driver->phone }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <div class="flex flex-col text-[10px] text-gray-600 font-bold">
                                    <span><i class="fas fa-truck ml-1"></i> {{ $driver->car_brand }} - {{ $driver->car_plate }}</span>
                                </div>
                            </td>
                            <td class="p-4 text-center">
                                <span class="px-2 py-1 rounded-full text-[9px] font-bold {{ $driver->status == 'active' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                    {{ $driver->status == 'active' ? 'نشط' : 'معلق' }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                @if($driver->status == 'pending')
                                    <button wire:click="activateDriver({{ $driver->id }})" class="bg-purple-600 text-white px-3 py-1 rounded-lg text-[10px] font-bold hover:bg-purple-700 transition">
                                        تفعيل
                                    </button>
                                @else
                                    <i class="fas fa-check-circle text-green-500"></i>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>