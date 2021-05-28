<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Http\Controllers\Mail\Activar;
use App\Http\Controllers\Mail\Recuperar;
use App\Models\User;
use App\Models\User_Rol;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Authentication extends Controller
{
    /**
     * Acceso a la aplicación
     */
    public function login(Request $request)
    {
        // TODO: Controlar el numero de intentos de acceso desde una ip
        $loginData = $request->validate([
            'email' => ['required', 'max:255'],
            'password' => ['required', 'max:255'],
        ]);
        if (!Auth::attempt($loginData)) {
            //return response(['message' => 'Login incorrecto. Revise las credenciales.'], 400);
            return response()->noContent(403);
        }
        $nuevo = auth()->user();
        if ($nuevo->activated == 0 || $nuevo->blocked == 1) {
            return response()->noContent(402);
        }
        $accessToken = auth()->user()->createToken('authToken')->accessToken;
        return response()->json(['user' => $nuevo, 'token' => $accessToken], 200);
    }
    /**
     * Salir de la aplicación
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->noContent(200);
    }
    public function sendActivate($token, $email, $name, $lastname = '')
    {
        $url = env('APP_URL') .  DIRECTORY_SEPARATOR . env('ACTVATE_URL') . DIRECTORY_SEPARATOR . $token;
        try {
            Mail::to($email)->send(new Activar($name, $lastname, $url));
            return !Mail::failures();
        } catch (\Throwable $th) {
            dd($th);
        }
    }
    /**
     * Registrar un nuevo usuario
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:32'],
            'lastname' => ['max:255'],
        ]);
        $data = app(Users::class)->getRequestData($request);
        $data['activated_token'] = Str::random(128) . time();

        if (!array_key_exists('lastname', $data)) $data['lastname'] = '';
        try {
            DB::beginTransaction();
            $nuevo = app(Users::class)->insertDB($data);
            if ($nuevo) {
                app(Users::class)->addRol($nuevo->id, 'teacher');
                $sendMail = $this->sendActivate($data['activated_token'], $nuevo->email, $nuevo->name, $nuevo->lastname);
                if ($sendMail) {
                    DB::commit();
                    return response()->noContent(201);
                } else {
                    return response()->json($sendMail, 505);
                    DB::rollBack();
                    throw new Exception('Error al mandar mail', 1);
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json($th, 500);
        }
    }
    /**
     * Activa un usuario
     */
    public function activate($code)
    {
        $user = User::where('activated_token', $code)->first();
        if ($user != null) {
            $user->email_verified_at = time();
            $user->activated = 1;
            $user->activated_token = null;
            $user->blocked = 0;
            $user->save();
            return redirect(env('APP_CLIENT'));
        } else {
            return view(404);
        }
    }
    /**
     * Recuperar password
     */
    public function forget(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email', 'max:255'],
        ]);

        $user = User::where('email', $data['email'])->first();
        if ($user != null) {
            if ($user->email_verified_at != null && $user->blocked == 0) {
                $pass = Str::random(12);
                $user->password = bcrypt($pass);
                $user->save();
                Mail::to($data['email'])->send(new Recuperar($pass));
                return response()->noContent(!Mail::failures() ? 200 : 500);
            } elseif ($user->email_verified_at === null) {
                return response()->noContent(
                    $this->sendActivate($user->activated_token, $user->email, $user->name, $user->lastname) ? 460 : 500
                );
            } elseif ($user->blocked == 1) {
                return response()->noContent(461);
            }
        }
        return response()->noContent(200);
    }

    public function getRol(Request $request)
    {
        $roles = User_Rol::where('idUser', auth()->user()->id)->pluck('idRol')->toArray();
        if (in_array(1, $roles)) {
            return response()->json(['rol' => 'admin'], 200);
        } elseif (in_array(2, $roles)) {
            return response()->json(['rol' => 'teacher'], 200);
        } elseif (in_array(3, $roles)) {
            return response()->json(['rol' => 'parent'], 200);
        } else {
            return response(403)->noContent();
        }
    }
}
