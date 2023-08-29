<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Helpers\Role\RoleHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Role\CreateRequest;
use App\Http\Requests\Role\UpdateRequest;
use App\Http\Resources\Role\RoleCollection;
use App\Http\Resources\Role\RoleResource;

class RoleController extends Controller
{
    private $role;

    public function __construct()
    {
        $this->role = new RoleHelper();
    }

    /**
     * Mengambil list user
     *
     * @author Brian Marco Agustian <brianmarco1996@email.com>
     */
    public function index(Request $request)
    {
        $filter = [
            'nama' => $request->nama ?? '',
            'access' => $request->access ?? '',
        ];

        $roles = $this->role->getAll($filter, 5, $request->sort ?? '');

        return response()->success(new RoleCollection($roles['data']));
    }

    /**
     * Membuat data role baru & disimpan ke tabel user_roles
     *
     * @author Brian Marco Agustian <brianmarco1996@email.com>
     */
    public function store(CreateRequest $request)
    {
        /**
         * Menampilkan pesan error ketika validasi gagal
         * pengaturan validasi bisa dilihat pada class app/Http/request/Role/CreateRequest
         */
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['name', 'access']);
        $roles = $this->role->create($payload);

        if (!$roles['status']) {
            return response()->failed($roles['error']);
        }

        return response()->success(new RoleResource($roles['data']), "Role berhasil ditambahkan");
    }


    /**
     * Menampilkan role secara spesifik dari tabel user_roles
     *
     * @author Brian Marco Agustian <brianmarco1996@email.com>
     */
    public function show($id)
    {
        $roles = $this->role->getById($id);

        if (!($roles['status'])) {
            return response()->failed(['Data user tidak ditemukan'], 404);
        }

        return response()->success(new RoleResource($roles['data']));
    }

    /**
     * Mengubah data role di tabel user_role
     *
     * @author Brian Marco Agustian <brianmarco1996@email.com>
     */
    public function update(UpdateRequest $request)
    {
        /**
         * Menampilkan pesan error ketika validasi gagal
         * pengaturan validasi bisa dilihat pada class app/Http/request/Role/UpdateRequest
         */
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['id','name', 'access']);
        $roles = $this->role->update($payload, $payload['id'] ?? 0);

        if (!$roles['status']) {
            return response()->failed($roles['error']);
        }

        return response()->success(new RoleResource($roles['data']), "Role berhasil diubah");
    }


    /**
     * Soft delete data role
     *
     * @author Brian Marco Agustian <brianmarco1996@email.com>
     * @param mixed $id
     */
    public function destroy($id)
    {
        $roles = $this->role->delete($id);

        if (!$roles) {
            return response()->failed(['Mohon maaf data pengguna tidak ditemukan']);
        }

        return response()->success($roles);
    }
}
