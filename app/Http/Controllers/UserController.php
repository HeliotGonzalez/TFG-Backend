<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Video;
use App\Mail\OtpMail;
use App\Mail\ForgotPasswordMail;
use App\Models\Amigo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {

    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->input('data');
    
        $request->merge([
            'name'         => $data[0],
            'username'     => $data[1],
            'email'        => $data[2],
            'password'     => $data[3],
            'proveniencia' => $data[4],
        ]);
        
        Validator::make($request->all(), [
            'name'         => 'required|string',
            'username'     => 'required|string|unique:users,username',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|string|min:8',
            'proveniencia' => 'required|string',
        ])->validate();
    
        try {
            $user = new User();
            $user->name = $data[0];
            $user->username = $data[1];
            $user->email = $data[2];
            $user->role_id = 1;
            $user->password = Hash::make($data[3]);
            $user->proveniencia = $data[4];
            
            // Generar el OTP y establecer su expiración (15 minutos)
            $user->otp_code = rand(100000, 999999); // o Str::random(6) si prefieres alfanumérico
            $user->otp_expires_at = Carbon::now()->addMinutes(15);
            
            $user->save();
    
            // Enviar el correo con el OTP
            Mail::to($user->email)->send(new OtpMail($user));
            
            return response()->json(['message' => 'Usuario creado, se ha enviado el código de verificación al correo.'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Validar datos
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'username'   => 'required|string|max:255|unique:users,username,' . $user->id,
            'email'      => 'required|email|max:255|unique:users,email,' . $user->id,
            'descricion' => 'nullable|string',
        ]);

        // Actualizar campos básicos
        $user->name = $data['name'];
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->descricion = $data['descricion'] ?? $user->descricion;
        $user->save();

        /*
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatarUrl = asset('storage/' . $path);
        }
        */


        return response()->json([
            'message' => 'Perfil actualizado correctamente',
            'user'    => $user,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }

    public function getUser($email, $password)
    {
        $user = User::where('email', $email)->first();
        
        //Verificar si la cuenta está verificada
        if (!$user->email_verified_at) {
            return response()->json(['error' => 'La cuenta no ha sido verificada'], 403);
        }
        
        if ($user && Hash::check($password, $user->password)) {
            // Las credenciales son correctas
            // Puedes proceder a generar un token o iniciar sesión, etc.
            return $user;
        } else {
            // Credenciales incorrectas
            return 1;
        }
        
    }

    public function verificarOtp(Request $request)
    {
        $data = $request->input('data');
        
        $request->merge([
            'email'    => $data[0],
            'otp_code' => $data[1],
        ]);
        
        Validator::make($request->all(), [
            'email'    => 'required|email',
            'otp_code' => 'required|numeric',
        ])->validate();
        
        $user = User::where('email', $data[0])->first();
        
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        
        if ($user->otp_code != $data[1]) {
            return response()->json(['error' => 'Código OTP incorrecto'], 403);
        }
        
        if ($user->otp_expires_at < Carbon::now()) {
            return response()->json(['error' => 'El código OTP ha expirado'], 403);
        }
        
        // Marcar la cuenta como verificada
        $user->email_verified_at = Carbon::now();
        $user->save();
        
        return response()->json(['message' => 'Cuenta verificada correctamente'], 200);
    }

    public function forgotPassword($email){
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        $user->re_get_password_token = rand(100000, 999999);
        $user->re_get_password_expires_at = Carbon::now()->addMinutes(15);
        $user->save();

        // Enviar el correo con el token
        Mail::to($user->email)->send(new ForgotPasswordMail($user));

        return 0; 
    }

    public function verificarOtpPassword($email, $code){
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        if ($user->re_get_password_token != $code) {
            return response()->json(['error' => 'Código OTP incorrecto'], 403);
        }

        if ($user->re_get_password_expires_at < Carbon::now()) {
            return response()->json(['error' => 'El código OTP ha expirado'], 403);
        }

        return response()->json(['message' => 'Código OTP correcto'], 200);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->input('data');
        
        // Extraer email y password desde el arreglo $data
        $email = $data[0] ?? null;
        $password = $data[1] ?? null;
    
        // Validar que se hayan enviado ambos datos
        if (!$email || !$password) {
            return response()->json(['error' => 'Email o contraseña faltantes'], 400);
        }
    
        // Buscar el usuario
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
    
        // Actualizar la contraseña
        $user->password = Hash::make($password);
        $user->save();
    
        return response()->json(['message' => 'Contraseña actualizada correctamente'], 200);
    } 
    
    public function getUserData($ownerID, $userID){
        $user = User::where('id', $ownerID)->first();

        $videos = Video::with('significado', 'user')
            ->with(['diccionario' => function ($query) use ($userID) {
                $query->where('user_id', $userID);
            }])
            ->withCount(['userVideos as likes' => function ($query) {
                    $query->where('action', 'like');
                }, 'userVideos as dislikes' => function ($query) {
                    $query->where('action', 'dislike');
                }
            ])->with(['userVideos' => function ($query) use ($userID) {
                $query->where('user_id', $userID);
            }])->with('significado.etiquetas')
            ->orderBy('likes', 'desc')
            ->where('user_id', $ownerID)
            ->whereNotIn('corregido', [1, 3, 5])
            ->get();

    
        $videos->map(function ($video) {
            $video->inDictionary = $video->diccionario->isNotEmpty();

            $reaction = $video->userVideos->first();
            $video->myReaction = $reaction ? $reaction->action : null;

            unset($video->diccionario);
            unset($video->userVideos);
        });

        $NAmigos = Amigo::where('user_id', $userID)->orWhere('amigo_id', $userID)->count();
    

        return response()->json([
            'user' => $user,
            'videos' => $videos,
            'NAmigos' => $NAmigos
        ], 200);
    }

    public function getUserDataByName($username, $userID){
        $user = User::where('username', $username)->first();

        $videos = Video::with('significado', 'user')
            ->with(['diccionario' => function ($query) use ($userID) {
                $query->where('user_id', $userID);
            }])
            ->withCount(['userVideos as likes' => function ($query) {
                    $query->where('action', 'like');
                }, 'userVideos as dislikes' => function ($query) {
                    $query->where('action', 'dislike');
                }
            ])->with(['userVideos' => function ($query) use ($userID) {
                $query->where('user_id', $userID);
            }])->with('significado.etiquetas')
            ->orderBy('likes', 'desc')
            ->where('user_id', $user->id)
            ->whereNotIn('corregido', [1, 3, 5])
            ->get();

    
        $videos->map(function ($video) {
            $video->inDictionary = $video->diccionario->isNotEmpty();

            $reaction = $video->userVideos->first();
            $video->myReaction = $reaction ? $reaction->action : null;

            unset($video->diccionario);
            unset($video->userVideos);
        });
    

        return response()->json([
            'user' => $user,
            'videos' => $videos,
        ], 200);
    }
}
