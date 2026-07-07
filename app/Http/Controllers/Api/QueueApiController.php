<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JanjiTemu;
use App\Http\Requests\StoreQueueRequest;
use App\Http\Resources\QueueResource;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class QueueApiController extends Controller
{
    public function index(Request $request)
    {
        $query = JanjiTemu::query();

        // Saring berdasarkan nama dokter
        if ($request->has('doctor')) {
            $query->where('dokter_name', 'like', '%' . $request->doctor . '%');
        }

        // Saring berdasarkan rentang tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->where('tanggal', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->where('tanggal', '<=', $request->end_date);
        }

        // Batasi 10 data per halaman
        $queues = $query->paginate(10);

        return QueueResource::collection($queues);
    }

    public function store(StoreQueueRequest $request)
    {
        $user = $request->user();

        // Hitung nomor antrean berikutnya
        $count = JanjiTemu::where('dokter_name', $request->dokter_name)
            ->where('tanggal', $request->tanggal)
            ->count();
        $nomor_antrean = 'A-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        $queue = JanjiTemu::create([
            'pasien_id' => $user->id,
            'pasien_name' => $user->name,
            'dokter_name' => $request->dokter_name,
            'poli' => $request->poli,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'status' => 'menunggu',
            'nomor_antrean' => $nomor_antrean,
            'keluhan' => $request->keluhan,
        ]);

        return (new QueueResource($queue))
            ->additional(['message' => 'Antrean berhasil dibuat'])
            ->response()
            ->setStatusCode(201);
    }

    public function show($id)
    {
        try {
            $queue = JanjiTemu::findOrFail($id);
            return new QueueResource($queue);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Resource tidak ditemukan',
                'message' => 'Data antrean dengan ID ' . $id . ' tidak ada di sistem.'
            ], 404);
        }
    }

    public function update(StoreQueueRequest $request, $id)
    {
        try {
            $queue = JanjiTemu::findOrFail($id);
            $queue->update($request->validated());
            return new QueueResource($queue);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Resource tidak ditemukan',
                'message' => 'Data antrean dengan ID ' . $id . ' tidak ada di sistem.'
            ], 404);
        }
    }

    public function destroy($id)
    {
        try {
            $queue = JanjiTemu::findOrFail($id);
            $queue->delete();
            return response()->json([
                'message' => 'Data antrean berhasil dihapus'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Resource tidak ditemukan',
                'message' => 'Data antrean dengan ID ' . $id . ' tidak ada di sistem.'
            ], 404);
        }
    }
}
