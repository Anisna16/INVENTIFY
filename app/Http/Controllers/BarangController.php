<?php

namespace App\Http\Controllers;

use App\Models\KodeBarangModel;
use App\Models\BarangModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BarangController extends Controller
{
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Daftar Barang',
            'list' => ['Home', 'Barang']
        ];

        $page = (object)[
            'title' => 'Daftar Barang dalam sistem',
        ];

        $activeMenu = 'barang';

        $kode = KodeBarangModel::all();
        $user = UserModel::all();

        return view('barang.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kode' => $kode, 'user' => $user, 'activeMenu' => $activeMenu]);
    }

    public function list(Request $request)
    {
        $products = BarangModel::select('id_barang', 'NUP', 'nama_barang', 'satuan', 'harga_perolehan', 'tanggal_pencatatan', 'id_kode_barang', 'id_user')
                    ->with('user')
                    ->with('kode');

        if ($request->id_kode_barang) {
            $products->where('id_kode_barang', $request->id_kode_barang);
        }

        if ($request->id_user) {
            $products->where('id_user', $request->id_user);
        }
        
        return DataTables::of($products)
            ->addIndexColumn() // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
            ->addColumn('aksi', function ($barang) { // menambahkan kolom aksi
                $btn = '<a href="'.url('/barang/' . $barang->id_barang).'" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="'.url('/barang/' . $barang->id_barang . '/edit').'" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<form class="d-inline-block" method="POST" action="'.url('/barang/'.$barang->id_barang).'">'. csrf_field() . method_field('DELETE') .'<button type="submit" class="btn btn-danger btn-sm"onclick="return confirm(\'Apakah Anda yakit menghapus data ini?\');">Hapus</button></form>';
            return $btn;
        })
        ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html
        ->make(true);
    }

    public function create(){
        $breadcrumb = (object)[
            'title' => 'Tambah Barang',
            'list' => ['Home', 'Barang', 'Tambah']
        ];

        $page = (object)[
            'title' => 'Tambah Barang Baru',
        ];

        $kode = KodeBarangModel::all();
        $user = UserModel::all();
        $activeMenu = 'barang';

        return view('barang.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kode' => $kode, 'user' => $user, 'activeMenu' => $activeMenu]);
    }

    public function store(Request $request){
        // Validate each field as an array
        $request->validate([
            'id_kode_barang.*'     => 'required|integer',
            'id_user.*'            => 'required|integer',
            'nama_barang.*'        => 'required|string',
            'NUP.*'                => 'required|string',
            'satuan.*'             => 'required|string',
            'harga_perolehan.*'    => 'required|string',
            'tanggal_pencatatan.*' => 'required|date_format:Y-m-d\TH:i',
        ]);

        // Iterate through each set of fields and create a new record for each
        foreach ($request->id_kode_barang as $key => $value) {
            BarangModel::create([
                'id_kode_barang'        => $request->id_kode_barang[$key],
                'id_user'               => $request->id_user[$key],
                'nama_barang'           => $request->nama_barang[$key],
                'NUP'                   => $request->NUP[$key],
                'satuan'                => $request->satuan[$key],
                'harga_perolehan'       => $request->harga_perolehan[$key],
                'tanggal_pencatatan'    => $request->tanggal_pencatatan[$key],
            ]);
        }
    
        return redirect('/barang')->with('success', 'Data berhasil ditambahkan');
    }

    public function show(string $id){
        $barang = BarangModel::with('kode')->with('user')->find($id);

        $breadcrumb = (object)[
            'title' => 'Detail Barang',
            'list' => ['Home', 'Barang', 'Detail']
        ];

        $page = (object)[
            'title' => 'Detail Barang',
        ];

        $activeMenu = 'barang';

        return view('barang.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'barang' => $barang, 'activeMenu' => $activeMenu]);
    }

    public function edit(string $id){
        $barang = BarangModel::find($id);
        $kode = KodeBarangModel::all();
        $user = UserModel::all();

        $breadcrumb = (object)[
            'title' => 'Edit Barang',
            'list' => ['Home', 'Barang', 'Edit']
        ];

        $page = (object)[
            'title' => 'Edit Barang',
        ];

        $activeMenu = 'barang';

        return view('barang.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'barang' => $barang, 'kode' => $kode, 'user' => $user, 'activeMenu' => $activeMenu]);
    }

    public function update(Request $request, $id){
        $request->validate([
            'nama_barang'           =>'required | string ',
            'NUP'                   => 'required | string | :detail_barang,'.$id.',id_barang',
            'satuan'                => 'required | string',
            'harga_perolehan'       =>'required | string',
            'tanggal_pencatatan'    =>'required | date_format:Y-m-d\TH:i',
            'id_kode_barang'        =>'required | integer',
            'id_user'               =>'required | integer',
        ]);

        BarangModel::find($id)->update([
            'id_kode_barang'        => $request->id_kode_barang,
            'id_user'               => $request->id_user,
            'nama_barang'           => $request->nama_barang,
            'NUP'                   => $request->NUP,
            'satuan'                => $request->satuan,
            'harga_perolehan'       => $request->harga_perolehan,
            'tanggal_pencatatan'    => $request->tanggal_pencatatan,
        ]);

        return redirect('/barang')->with('success', 'Data berhasil diubah!');;
    }

    public function destroy($id)
    {
        $check = BarangModel::find($id);
        if(!$check){
            return redirect('/barang')->with('error', 'Data tidak ditemukan!');
        }

        try{
            BarangModel::destroy($id);
            return redirect('/barang')->with('success', 'Data berhasil dihapus!');
        }catch(\Illuminate\Database\QueryException $e){
            return redirect('/barang')->with('error', 'Data gagal dihapus! masih terdapat tabel lain yang terikat dengan data ini!');
        }
    }
}
