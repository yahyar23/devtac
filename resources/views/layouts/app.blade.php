<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ماي تكسي - لوحة التحكم</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { font-family: 'Cairo', sans-serif; }
    </style>
    @livewireStyles {{-- يجب أن يكون هنا --}}
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        {{-- شريط التنقل العلوي مع زر تسجيل الخروج --}}
        <nav class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex items-center">
                        <span class="text-xl font-bold text-purple-600">🚖 ماي تكسي</span>
                    </div>
                    
                    {{-- زر تسجيل الخروج --}}
                    <div class="flex items-center">
                        <span class="ml-4 text-sm text-gray-600">مرحباً، {{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="bg-red-50 text-red-600 px-4 py-2 rounded-lg text-sm font-bold hover:bg-red-100 transition">
                                تسجيل الخروج
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        {{-- المحتوى الأساسي --}}
        <main class="py-10">
            {{ $slot }}
        </main>
        @livewireScripts {{-- ضروري جداً لعمل الأزرار --}}
    </div>
</body>
</html>