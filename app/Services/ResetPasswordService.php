<?php

namespace App\Services;

use App\Mail\SendLinkChangePasswordMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class ResetPasswordService
{
    private const ERROR_MESSAGES = [
        'sendLinkError' => 'При отправке письма произошла ошибка. Повторите попытку позже.',
        'userNotFound' => 'Пользователь не найден.',
    ];

    /**
     * @param string $email
     * @return string
     * @throws \Exception
     */
    public function sendLink(string $email): string
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw new \Exception(self::ERROR_MESSAGES['userNotFound']);
        }

        $key = 'password-reset:' . $user->id;
        $maxAttempts = 1; // Максимальное количество попыток
        $decaySeconds = config('auth.passwords.users.throttle', 60); // Время ожидания в секундах

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $secondsLeft = RateLimiter::availableIn($key);
            throw new \Exception("Слишком много попыток. Пожалуйста, попробуйте через {$secondsLeft} секунд.");
        }

        try {
            $token = Password::createToken($user);

            $url = config('app.frontend_url') . '/change-password/reset?token=' . $token . '&email=' . urlencode($email);

            Mail::to($email)->send(new SendLinkChangePasswordMail($url));

            // Увеличиваем счетчик попыток
            RateLimiter::hit($key, $decaySeconds);

            return Password::RESET_LINK_SENT;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }


    /**
     * @param array $data
     * @return string
     */
    public function resetPassword(array $data): string
    {
        return Password::reset(
            $data,
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );
    }
}
