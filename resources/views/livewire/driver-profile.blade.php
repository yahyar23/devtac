<div class="p-6 bg-gray-50 min-h-screen font-sans" dir="rtl">
    <div class="relative overflow-hidden bg-gradient-to-br from-purple-800 to-indigo-900 rounded-3xl p-8 text-white shadow-2xl mb-8">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm opacity-80 mb-1">حساب السائق</p>
                <h2 class="text-3xl font-bold italic">{{ $user->name }}</h2>
            </div>
            <div class="bg-white/20 p-3 rounded-2xl backdrop-blur-md">
                <i class="fas fa-taxi text-2xl"></i>
            </div>
        </div>

        <div class="mt-12 flex justify-between items-end">
            <div>
                <p class="text-xs opacity-60">رقم الهاتف</p>
                <p class="text-xl font-mono tracking-widest">{{ $user->phone }}</p>
            </div>
            <div class="text-left">
                <p class="text-xs opacity-60">حالة الحساب</p>
                <span class="px-3 py-1 bg-yellow-400 text-yellow-900 rounded-full text-xs font-bold">
                    {{ $user->status == 'pending' ? 'قيد الانتظار' : 'نشط' }}
                </span>
            </div>
        </div>
        
        <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4 space-x-reverse">
            <div class="bg-green-100 p-4 rounded-xl text-green-600 text-2xl">
                <i class="fas fa-wallet"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">رصيد المحفظة</p>
                <p class="text-2xl font-bold text-gray-800">{{ $user->driverDetail->wallet_balance }} د.ع</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4 space-x-reverse">
            <div class="bg-blue-100 p-4 rounded-xl text-blue-600 text-2xl">
                <i class="fas fa-car"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">المركبة المسجلة</p>
                <p class="text-lg font-bold text-gray-800">{{ $user->driverDetail->car_model }}</p>
            </div>
        </div>
    </div>
</div>