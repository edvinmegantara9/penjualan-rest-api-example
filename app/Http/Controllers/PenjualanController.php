<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $row = $request->input('row');
        $keyword = $request->input('keyword');
        $sortby = $request->input('sortby');
        $sorttype = $request->input('sorttype');

        if ($keyword == 'null') $keyword = '';
        $keyword = urldecode($keyword);

        $penjualans = Penjualan::orderBy('penjualans.' . $sortby, $sorttype)
            ->when($keyword, function ($query) use ($keyword) {
                return $query
                    ->where('penjualans.no_penjualan', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('penjualans.pelanggan', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('penjualans.keterangan', 'LIKE', '%' . $keyword . '%');
            })->select('penjualans.*')->paginate($row);

        foreach ($penjualans as $penjualan) {
            $penjualan->penjualanDetails;

            foreach ($penjualan->penjualanDetails as $detail) {
                $detail->barang;
            }
        }


        $response = [
            'status' => 200,
            'message' => 'successfully retrieved data',
            'data' => $penjualans
        ];
        return response()->json($response, 200);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $this->validate($request, [
                'no_penjualan' => 'required',
                'pelanggan' => 'required',
                'penjualan_details' => 'required|array|min:1',
                'penjualan_details.*.barang_id' => 'required',
                'penjualan_details.*.qty' => 'required|integer',
                'penjualan_details.*.harga_beli' => 'required',
                'penjualan_details.*.harga_jual' => 'required',
            ]);

            $penjualan = new Penjualan;
            $penjualan->no_penjualan = $request->get('no_penjualan');
            $penjualan->pelanggan = $request->get('pelanggan');
            $penjualan->keterangan =  $request->get('keterangan');

            if ($penjualan->save()) {
                foreach ($request->get('penjualan_details') as $d) {
                    $detail = new PenjualanDetail;
                    $detail->penjualan_id = $penjualan->id;
                    $detail->barang_id = $d['barang_id'];
                    $detail->qty = $d['qty'];
                    $detail->harga_beli = $d['harga_beli'];
                    $detail->harga_jual = $d['harga_jual'];
                    $detail->save();
                }

                $penjualan->penjualanDetails;
                foreach ($penjualan->penjualanDetails as $detail) {
                    $detail->barang;
                }

                DB::commit();

                $response = [
                    'status' => 200,
                    'message' => 'successfully created data',
                    'data' => $penjualan
                ];

                return response()->json($response, 200);
            }

            $response = [
                'status' => 404,
                'message' => 'Error during Creating',
                'data' => $penjualan
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
                'no_penjualan' => 'required',
                'pelanggan' => 'required',
                'penjualan_details' => 'required|array|min:1',
                'penjualan_details.*.barang_id' => 'required',
                'penjualan_details.*.qty' => 'required|integer',
                'penjualan_details.*.harga_beli' => 'required',
                'penjualan_details.*.harga_jual' => 'required',
            ]);

            $penjualan = Penjualan::find($id);
            $penjualan->no_penjualan = $request->get('no_penjualan');
            $penjualan->pelanggan = $request->get('pelanggan');
            $penjualan->keterangan =  $request->get('keterangan');

            if ($penjualan->save()) {
                PenjualanDetail::where("penjualan_id", $penjualan->id)->delete();

                foreach ($request->get('penjualan_details') as $d) {
                    $detail = new PenjualanDetail;
                    $detail->penjualan_id = $penjualan->id;
                    $detail->barang_id = $d['barang_id'];
                    $detail->qty = $d['qty'];
                    $detail->harga_beli = $d['harga_beli'];
                    $detail->harga_jual = $d['harga_jual'];
                    $detail->save();
                }

                $penjualan->penjualanDetails;
                foreach ($penjualan->penjualanDetails as $detail) {
                    $detail->barang;
                }

                DB::commit();

                $response = [
                    'status' => 200,
                    'message' => 'successfully updated data',
                    'data' => $penjualan
                ];

                return response()->json($response, 200);
            }

            $response = [
                'status' => 404,
                'message' => 'Error during creating',
                'data' => $penjualan
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
        $penjualan = Penjualan::find($id);
        $penjualan->penjualanDetails;

        foreach ($penjualan->penjualanDetails as $detail) {
            $detail->barang;
        }

        $response = [
            'status' => 200,
            'message' => 'successfully retrieved data',
            'data' => $penjualan
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
            $penjualan = Penjualan::findOrFail($id);
            PenjualanDetail::where('penjualan_id', $penjualan->id)->delete();

            if (!$penjualan->delete()) {
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
