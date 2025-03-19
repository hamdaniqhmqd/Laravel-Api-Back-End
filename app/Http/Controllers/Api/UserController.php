<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseApiResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get all users
        $users = User::get();

        //return collection of users as a resource
        return new ResponseApiResource(true, 'List Data Users', $users);
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        // Jika validasi gagal, kembalikan response error
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Membuat user baru
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password), // Hash password sebelum disimpan
        ]);

        // Kembalikan response sukses
        return new ResponseApiResource(true, 'User berhasil ditambahkan!', $user);
    }

    /**
     * show
     *
     * @param  mixed $id
     * @return void
     */
    public function show($id)
    {
        //find user by ID
        $user = User::find($id);

        //return single user as a resource
        return new ResponseApiResource(true, 'Detail Data User!', $user);
    }

    public function update(Request $request, $id)
    {
        // Cari user berdasarkan ID
        $user = User::find($id);

        // Jika user tidak ditemukan
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan!'
            ], 404);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
        ]);

        // Jika validasi gagal, kembalikan response error
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Perbarui data user
        $user->update([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        // Kembalikan response sukses
        return new ResponseApiResource(true, 'User berhasil diperbarui!', $user);
    }

    /**
     * destroy
     *
     * @param  mixed $id
     * @return void
     */
    public function destroy($id)
    {

        //find user by ID
        $user = User::find($id);

        //delete user
        $user->delete();

        //return response
        return new ResponseApiResource(true, 'Data User Berhasil Dihapus!', null);
    }
}