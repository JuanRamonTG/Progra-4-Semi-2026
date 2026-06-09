<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Notifications\WelcomeNotification;

class AuthController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión principal.
     */
    public function showLogin()
    {
        return view('login');
    }

    /**
     * Maneja el registro de un nuevo usuario.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:usuarios,correo',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'nombre_completo' => $request->name,
            'correo' => $request->email,
            'contrasena' => Hash::make($request->password),
            'id_rol' => 1,
        ]);

        Auth::login($user);

        // Enviar correo de bienvenida
        $user->notify(new WelcomeNotification());

        return response()->json([
            'message' => 'Usuario registrado con éxito',
            'redirect' => '/dashboard',
        ]);
    }

    /**
     * Maneja el inicio de sesión.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['correo' => $credentials['email'], 'password' => $credentials['password']], $request->remember)) {
            $request->session()->regenerate();

            return response()->json([
                'message' => 'Sesión iniciada correctamente',
                'redirect' => '/dashboard',
            ]);
        }

        return response()->json([
            'message' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ], 422);
    }

    /**
     * Muestra el formulario para solicitar el enlace de recuperación.
     */
    public function showForgotPasswordForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Envía el enlace de restablecimiento de contraseña.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('correo', $request->email)->first();

        if (! $user) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => __(Password::INVALID_USER)
                ], 422);
            }
            return back()->withErrors(['email' => __(
                Password::INVALID_USER
            )]);
        }

        $token = Password::broker()->createToken($user);
        $user->sendPasswordResetNotification($token);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => __(Password::RESET_LINK_SENT)
            ]);
        }

        return back()->with(['status' => __(Password::RESET_LINK_SENT)]);
    }

    /**
     * Muestra el formulario de restablecimiento con token.
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Procesa el restablecimiento de contraseña.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            [
                'correo' => $request->email,
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation,
                'token' => $request->token,
            ],
            function (User $user, string $password) {
                $user->contrasena = Hash::make($password);
                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($request->wantsJson()) {
            if ($status === Password::PASSWORD_RESET) {
                return response()->json([
                    'message' => __($status),
                    'redirect' => route('login')
                ]);
            }
            return response()->json([
                'message' => __($status)
            ], 422);
        }

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    /**
     * Maneja el cierre de sesión.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
