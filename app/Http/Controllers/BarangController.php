<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth:api');
    }

    public function index(Request $request)
    {
        $row = $request->input('row');
        $keyword = $request->input('keyword');
        $sortby = $request->input('sortby');
        $sorttype = $request->input('sorttype');

        if ($keyword == 'null') $keyword = '';
        $keyword = urldecode($keyword);

        $barang = Barang::orderBy('barangs.' . $sortby, $sorttype)
            ->when($keyword, function ($query) use ($keyword) {
                return $query
                    ->where('barangs.nama', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('barangs.merk', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('barangs.kategori', 'LIKE', '%' . $keyword . '%')
                    ->orwhere('barangs.harga_jual', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('barangs.harga_beli', 'LIKE', '%' . $keyword . '%');
            })->select('barangs.*')->paginate($row);

        $response = [
            'status' => 200,
            'message' => 'successfully retrieved data',
            'data' => $barang
        ];
        return response()->json($response, 200);
    }

    public function show($id)
    {
        $barang = Barang::find($id);
        $response = [
            'status' => 200,
            'message' => 'successfully retrieved data',
            'data' => $barang
        ];
        return response($response, 200);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'nama' => 'required',
            'merk' => 'required',
            'kategori' => 'required',
            'harga_beli' => 'required',
            'harga_jual' => 'required',
            'satuan' => 'required'
        ]);

        $barang = Barang::find($id);

        $barang->nama = $request->input('nama');
        $barang->merk = $request->input('merk');
        $barang->kategori = $request->input('kategori');
        $barang->harga_beli = $request->input('harga_beli');
        $barang->harga_jual = $request->input('harga_jual');
        $barang->satuan = $request->input('satuan');

        if (!$barang->update()) {
            return response()->json([
                'status' => 404,
                'message' => 'Error during update'
            ], 404);
        }

        $response = [
            'status' => 201,
            'message' => 'successfully updated data',
            'data' => $barang
        ];

        return response()->json($response, 201);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'nama' => 'required',
            'merk' => 'required',
            'kategori' => 'required',
            'harga_beli' => 'required',
            'harga_jual' => 'required',
            'satuan' => 'required'
        ]);

        $barang = new Barang;

        $barang->nama = $request->input('nama');
        $barang->merk = $request->input('merk');
        $barang->kategori = $request->input('kategori');
        $barang->harga_beli = $request->input('harga_beli');
        $barang->harga_jual = $request->input('harga_jual');
        $barang->satuan = $request->input('satuan');

        if (!$barang->save()) {
            return response()->json([
                'status' => 404,
                'message' => 'Error during update'
            ], 404);
        }

        $response = [
            'status' => 201,
            'message' => 'successfully created data',
            'data' => $barang
        ];

        return response()->json($response, 201);
    }

      /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tasks = Barang::findOrFail($id);

        if (!$tasks->delete()) {
            $response = [
                'message' => 'Error during delete',
            ];
            return response()->json($response, 404);
        }
        $response = [
            'message' => 'successfully deleted data'
        ];
        return response()->json($response,  200);
    }
}
