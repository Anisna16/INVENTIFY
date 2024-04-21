<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarangModel extends Model
{
    use HasFactory;

    protected $table = 'detail_barang';
    protected $primaryKey = 'NUP';
    public $timestamps = false;

    protected $fillable = ['id_kode_barang','merk_barang','satuan', 'harga_perolehan', 'tanggal_pencatatan'];

    public function kodeBarang(): BelongsTo{
        return $this->belongsTo(KodeBarangModel::class, 'id_kode_barang', 'id_kode_barang');
    }
}