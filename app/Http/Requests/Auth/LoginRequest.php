<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * تحديد ما إذا كان المستخدم مخولاً لإجراء هذا الطلب.
     */
    public function authorize()
    {
        return true; // يجب أن يبقى true فقط
    }

    /**
     * قواعد التحقق.
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string'], 
            'password' => ['required', 'string'],
        ];
    }

    /**
     * محاولة المصادقة.
     */
    public function authenticate()
    {
        // --- اختبار الشاشة السوداء (مكانه الصحيح هنا) ---
        // dd($this->all()); 
        // ------------------------------------------

        $this->ensureIsNotRateLimited();

        // قمنا بتغيير email إلى phone هنا أيضاً لكي يطابق قاعدة البيانات
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'), // تغيير رسالة الخطأ لتظهر على حقل الهاتف
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * التأكد من عدم تجاوز حد محاولات الدخول.
     */
    public function ensureIsNotRateLimited()
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));
        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * مفتاح تحديد المحاولات (Throttling).
     */
    public function throttleKey()
    {
        // تغيير email إلى phone هنا لضمان حماية الحساب الصحيح
        return Str::lower($this->input('email')).'|'.$this->ip();
    }
}