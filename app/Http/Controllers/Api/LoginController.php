<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * Handle the login request and return a token with user details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'email'     => 'required',
            'password'  => 'required'
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Mengambil kredensial dari permintaan
        $credentials = $request->only('email', 'password');

        // Jika autentikasi gagal
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password Anda salah'
            ], 401);
        }

        // Ambil data pengguna yang sedang login
        $user = auth()->user();

        // Menambahkan klaim khusus untuk name dan email
        $customClaims = [
            'name'  => $user->name,
            'email' => $user->email,
        ];

        // Buat token JWT dengan klaim khusus
        $token = JWTAuth::claims($customClaims)->fromUser($user);

        // Mengembalikan respons dengan token dan data pengguna
        return response()->json([
            'success' => true,
            'user'    => $user,
            'token'   => $token
        ], 200);
    }

    /**
     * Get user details from the token.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUserFromToken()
    {
        try {
            // Mendapatkan payload dari token
            $payload = JWTAuth::parseToken()->getPayload();

            // Mengambil data dari klaim khusus di token
            $name = $payload->get('name');
            $email = $payload->get('email');

            // Mengembalikan respons dengan data name dan email
            return response()->json([
                'success' => true,
                'name'    => $name,
                'email'   => $email
            ]);
        } catch (\Exception $e) {
            // Menangani kesalahan jika token tidak valid atau telah kedaluwarsa
            return response()->json(['error' => 'Token is invalid or expired'], 401);
        }
    }
}
