<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\PembelianDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembelianController extends Controller
{
    public function index(Request $request)
    {
        $row = $request->input('row');
        $keyword = $request->input('keyword');
        $sortby = $request->input('sortby');
        $sorttype = $request->input('sorttype');

        if ($keyword == 'null') $keyword = '';
        $keyword = urldecode($keyword);

        $pembelians = Pembelian::orderBy('pembelians.' . $sortby, $sorttype)
            ->when($keyword, function ($query) use ($keyword) {
                return $query
                    ->where('pembelians.no_pembelian', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('pembelians.supplier', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('pembelians.keterangan', 'LIKE', '%' . $keyword . '%');
            })->select('pembelians.*')->paginate($row);

        foreach ($pembelians as $pembelian) {
            $pembelian->pembelianDetails;

            foreach ($pembelian->pembelianDetails as $detail) {
                $detail->barang;
            }
        }


        $response = [
            'status' => 200,
            'message' => 'successfully retrieved data',
            'data' => $pembelians
        ];
        return response()->json($response, 200);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $this->validate($request, [
                'no_pembelian' => 'required',
                'supplier' => 'required',
                'pembelian_details' => 'required|array|min:1',
                'pembelian_details.*.barang_id' => 'required',
                'pembelian_details.*.qty' => 'required|integer',
                'pembelian_details.*.harga_beli' => 'required',
            ]);

            $pembelian = new Pembelian;
            $pembelian->no_pembelian = $request->get('no_pembelian');
            $pembelian->supplier = $request->get('supplier');
            $pembelian->keterangan =  $request->get('keterangan');

            if ($pembelian->save()) {
                foreach ($request->get('pembelian_details') as $d) {
                    $detail = new PembelianDetail;
                    $detail->pembelian_id = $pembelian->id;
                    $detail->barang_id = $d['barang_id'];
                    $detail->qty = $d['qty'];
                    $detail->harga_beli = $d['harga_beli'];
                    $detail->save();
                }

                $pembelian->pembelianDetails;
                foreach ($pembelian->pembelianDetails as $detail) {
                    $detail->barang;
                }

                DB::commit();

                $response = [
                    'status' => 200,
                    'message' => 'successfully created data',
                    'data' => $pembelian
                ];

                return response()->json($response, 200);
            }

            $response = [
                'status' => 404,
                'message' => 'Error during Creating',
                'data' => $pembelian
            ];

            return response()->json($response, 404);
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($e->status) && $e->status == 422) {
                return response()->json($e->response->original, 422);
            }

            return response()->json($e, 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $this->validate($request, [
                'no_pembelian' => 'required',
                'supplier' => 'required',
                'pembelian_details' => 'required|array|min:1',
                'pembelian_details.*.barang_id' => 'required',
                'pembelian_details.*.qty' => 'required|integer',
                'pembelian_details.*.harga_beli' => 'required',
            ]);

            $pembelian = Pembelian::find($id);
            $pembelian->no_pembelian = $request->get('no_pembelian');
            $pembelian->supplier = $request->get('supplier');
            $pembelian->keterangan =  $request->get('keterangan');

            if ($pembelian->save()) {
                PembelianDetail::where("pembelian_id", $pembelian->id)->delete();

                foreach ($request->get('pembelian_details') as $d) {
                    $detail = new PembelianDetail;
                    $detail->pembelian_id = $pembelian->id;
                    $detail->barang_id = $d['barang_id'];
                    $detail->qty = $d['qty'];
                    $detail->harga_beli = $d['harga_beli'];
                    $detail->save();
                }

                $pembelian->pembelianDetails;
                foreach ($pembelian->pembelianDetails as $detail) {
                    $detail->barang;
                }

                DB::commit();

                $response = [
                    'status' => 200,
                    'message' => 'successfully updated data',
                    'data' => $pembelian
                ];

                return response()->json($response, 200);
            }

            $response = [
                'status' => 404,
                'message' => 'Error during creating',
                'data' => $pembelian
            ];

            return response()->json($response, 404);
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($e->status) && $e->status == 422) {
                return response()->json($e->response->original, 422);
            }

            return response()->json($e, 404);
        }
    }

    public function show($id)
    {
        $pembelian = Pembelian::find($id);
        $pembelian->pembelianDetails;

        foreach ($pembelian->pembelianDetails as $detail) {
            $detail->barang;
        }

        $response = [
            'status' => 200,
            'message' => 'successfully retrieved data',
            'data' => $pembelian
        ];
        return response($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $pembelian = Pembelian::findOrFail($id);
            PembelianDetail::where('pembelian_id', $pembelian->id)->delete();

            if (!$pembelian->delete()) {
                DB::rollBack();
                $response = [
                    'message' => 'Error during delete',
                ];
                return response()->json($response, 404);
            }

            DB::commit();

            $response = [
                'message' => 'successfully deleted data'
            ];
            return response()->json($response,  200);
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($e->status) && $e->status == 422) {
                return response()->json($e->response->original, 422);
            }

            return response()->json($e, 404);
        }
    }
}
