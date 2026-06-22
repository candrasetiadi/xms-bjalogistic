<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resi extends Model
{
    protected $table = 'bja_resi';

    protected $fillable = ['resi_num', 'kota_asal', 'kota_tujuan', 'layanan', 'estimasi_tiba'];

    protected $casts = ['estimasi_tiba' => 'date'];

    public function statuses()
    {
        return $this->hasMany(ResiStatus::class, 'resi_id')->orderBy('waktu', 'desc');
    }

    public function latestStatus()
    {
        return $this->hasOne(ResiStatus::class, 'resi_id')->latestOfMany('waktu');
    }

    public static function statusList(): array
    {
        return [
            'Barang Diterima di Warehouse BJA' => 'Paket telah tiba di Warehouse. Dilakukan pengecekan awal terkait jumlah koli, volume/berat, dan kondisi fisik luar barang.',
            'Barang dalam Proses Packing'       => 'Barang sedang disiapkan agar aman selama perjalanan laut. Meliputi pembungkusan ulang, pemberian bubble wrap, karung, atau palet kayu sesuai kebutuhan.',
            'Diproses di Warehouse Surabaya'    => 'Barang telah tiba di hub transit Warehouse Surabaya untuk persiapan masuk ke area pelabuhan.',
            'Menunggu Muat di Kapal'            => 'Barang sudah berada di pelabuhan dan masuk dalam antrean untuk dinaikkan ke atas kapal.',
            'Perjalanan di Kapal'               => 'Kapal telah berangkat dan kargo sedang dalam pelayaran menuju pelabuhan kota tujuan.',
            'Menunggu Kapal Sandar'             => 'Kapal telah tiba di perairan pelabuhan tujuan dan sedang menunggu izin untuk merapat di dermaga.',
            'Menunggu Bongkar Muat'             => 'Proses penurunan kontainer atau pembongkaran kargo dari atas kapal sedang berjalan.',
            'Dooring'                           => 'Barang telah keluar dari pelabuhan dan sedang dalam perjalanan kurir menuju alamat pengiriman akhir.',
            'Barang Diterima'                   => 'Pengiriman selesai. Barang telah diserahkan kepada penerima di alamat tujuan.',
        ];
    }

    public static function layananList(): array
    {
        return ['Cargo Laut', 'Cargo Udara', 'Cargo Darat', 'Kirim Motor', 'Kirim Mobil'];
    }

    public static function kotaAsalList(): array
    {
        return ['Jabodetabek', 'Surabaya'];
    }

    public function isDone(): bool
    {
        return $this->latestStatus?->status === 'Barang Diterima';
    }
}
