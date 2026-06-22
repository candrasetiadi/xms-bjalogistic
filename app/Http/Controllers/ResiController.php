<?php

namespace App\Http\Controllers;

use App\Models\Resi;
use App\Models\ResiStatus;
use Illuminate\Http\Request;

class ResiController extends Controller
{
    public function index(Request $request)
    {
        $q      = $request->get('q', '');
        $status = $request->get('status', '');

        $query = Resi::with('latestStatus')->orderByDesc('created_at');
        if ($q)      $query->where('resi_num', 'like', "%$q%")->orWhere('kota_tujuan', 'like', "%$q%");
        if ($status) $query->whereHas('latestStatus', fn($s) => $s->where('status', $status));

        $resis    = $query->paginate(30)->withQueryString();
        $statuses = array_keys(Resi::statusList());

        return view('resi.index', compact('resis', 'q', 'status', 'statuses'));
    }

    public function create()
    {
        return view('resi.create', [
            'statusList'  => Resi::statusList(),
            'layananList' => Resi::layananList(),
            'kotaList'    => Resi::kotaAsalList(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'resi_num'   => 'required|string|unique:bja_resi,resi_num',
            'kota_asal'  => 'required|string',
            'kota_tujuan'=> 'required|string',
            'layanan'    => 'required|string',
            'status'     => 'required|string',
            'waktu'      => 'required|date',
        ]);

        $resi = Resi::create([
            'resi_num'     => $request->resi_num,
            'kota_asal'    => $request->kota_asal,
            'kota_tujuan'  => $request->kota_tujuan,
            'layanan'      => $request->layanan,
            'estimasi_tiba'=> $request->estimasi_tiba ?: null,
        ]);

        $statusList = Resi::statusList();
        ResiStatus::create([
            'resi_id'    => $resi->id,
            'status'     => $request->status,
            'keterangan' => $statusList[$request->status] ?? '',
            'catatan'    => $request->catatan,
            'waktu'      => $request->waktu,
            'created_by' => session('auth_user.id'),
            'created_at' => now(),
        ]);

        return redirect()->route('resi.show', $resi)->with('success', 'Resi berhasil didaftarkan.');
    }

    public function show(Resi $resi)
    {
        $resi->load(['statuses.user']);
        return view('resi.show', [
            'resi'       => $resi,
            'statusList' => Resi::statusList(),
        ]);
    }

    public function addStatus(Request $request, Resi $resi)
    {
        $request->validate([
            'status' => 'required|string',
            'waktu'  => 'required|date',
        ]);

        $statusList = Resi::statusList();
        ResiStatus::create([
            'resi_id'    => $resi->id,
            'status'     => $request->status,
            'keterangan' => $statusList[$request->status] ?? '',
            'catatan'    => $request->catatan,
            'waktu'      => $request->waktu,
            'created_by' => session('auth_user.id'),
            'created_at' => now(),
        ]);

        return redirect()->route('resi.show', $resi)->with('success', 'Status berhasil diupdate.');
    }

    public function edit(Resi $resi)
    {
        return view('resi.edit', [
            'resi'        => $resi,
            'layananList' => Resi::layananList(),
            'kotaList'    => Resi::kotaAsalList(),
        ]);
    }

    public function update(Request $request, Resi $resi)
    {
        $request->validate([
            'kota_asal'   => 'required|string',
            'kota_tujuan' => 'required|string',
            'layanan'     => 'required|string',
        ]);

        $resi->update([
            'kota_asal'    => $request->kota_asal,
            'kota_tujuan'  => $request->kota_tujuan,
            'layanan'      => $request->layanan,
            'estimasi_tiba'=> $request->estimasi_tiba ?: null,
        ]);

        return redirect()->route('resi.show', $resi)->with('success', 'Info resi diperbarui.');
    }

    // Public API — for external website
    public function track(string $resiNum)
    {
        $resi = Resi::with(['statuses' => fn($q) => $q->orderBy('waktu', 'asc')])->where('resi_num', $resiNum)->first();

        if (!$resi) {
            return response()->json(['found' => false, 'message' => 'Nomor resi tidak ditemukan.'], 404);
        }

        return response()->json([
            'found'        => true,
            'resi_num'     => $resi->resi_num,
            'kota_asal'    => $resi->kota_asal,
            'kota_tujuan'  => $resi->kota_tujuan,
            'layanan'      => $resi->layanan,
            'estimasi_tiba'=> $resi->estimasi_tiba?->translatedFormat('d F Y'),
            'is_done'      => $resi->isDone(),
            'current_status' => $resi->latestStatus?->status,
            'timeline'     => $resi->statuses->map(fn($s) => [
                'status'     => $s->status,
                'keterangan' => $s->keterangan,
                'catatan'    => $s->catatan,
                'waktu'      => $s->waktu?->translatedFormat('d F Y, H:i'),
                'waktu_raw'  => $s->waktu?->toISOString(),
            ]),
        ]);
    }

    // Auto-create resi from invoice (called by InvoiceController)
    public static function autoCreate(string $resiNum, string $kotaTujuan): void
    {
        if (Resi::where('resi_num', $resiNum)->exists()) return;

        $resi = Resi::create([
            'resi_num'    => $resiNum,
            'kota_asal'   => 'Jabodetabek',
            'kota_tujuan' => $kotaTujuan,
            'layanan'     => null,
            'estimasi_tiba' => null,
        ]);

        $statusList = Resi::statusList();
        $firstStatus = array_key_first($statusList);

        ResiStatus::create([
            'resi_id'    => $resi->id,
            'status'     => $firstStatus,
            'keterangan' => $statusList[$firstStatus],
            'catatan'    => null,
            'waktu'      => now(),
            'created_by' => session('auth_user.id'),
            'created_at' => now(),
        ]);
    }
}
