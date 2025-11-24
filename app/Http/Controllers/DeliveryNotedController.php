<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class DeliveryNotedController extends Controller
{
    public function index()
    {
        $deliveryNotes = DB::table('delivery_notes')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('delivery_note.index', compact('deliveryNotes'));
    }

    public function create()
    {
        // Auto generate nomor surat
        $lastDN = DB::table('delivery_notes')
            ->whereYear('created_at', date('Y'))
            ->orderBy('id', 'desc')
            ->first();

        if ($lastDN) {
            // Extract number from format: 22A030C3/WARKMKMEMR/0206
            preg_match('/(\d+)/', $lastDN->delivery_note_no, $matches);
            $lastNumber = isset($matches[0]) ? intval($matches[0]) : 0;
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Format: 22A030C3/WARKMKMEMR/0206
        $deliveryNoteNo = strtoupper(dechex($newNumber)) . '/WARKMKMEMR/' . date('my');

        // Generate footer code: MKM/DX/FR/MEC/MAC/25/08/513
        $footerCode = 'MKM/DX/FR/MEC/MAC/' . date('y/m') . '/' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        return response()->json([
            'delivery_note_no' => $deliveryNoteNo,
            'footer_code' => $footerCode
        ]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Insert delivery note
            $deliveryNoteId = DB::table('delivery_notes')->insertGetId([
                'delivery_note_no' => $request->delivery_note_no,
                'vehicle_no' => $request->vehicle_no,
                'delivery_date' => $request->delivery_date,
                'location' => $request->location ?? 'Jakarta',
                'sender_name' => $request->sender_name ?? 'KOPKAR MKM',
                'receiver_name' => $request->receiver_name,
                'footer_code' => $request->footer_code,
                'notes' => $request->notes,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Insert items
            if ($request->has('items')) {
                foreach ($request->items as $item) {
                    DB::table('delivery_note_items')->insert([
                        'delivery_note_id' => $deliveryNoteId,
                        'item_name' => $item['item_name'],
                        'quantity' => $item['quantity'],
                        'unit' => $item['unit'],
                        'description' => $item['description'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Surat Pengantar berhasil dibuat',
                'id' => $deliveryNoteId
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat Surat Pengantar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $deliveryNote = DB::table('delivery_notes')
            ->where('id', $id)
            ->first();

        $items = DB::table('delivery_note_items')
            ->where('delivery_note_id', $id)
            ->get();

        return response()->json([
            'deliveryNote' => $deliveryNote,
            'items' => $items
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // Update delivery note
            DB::table('delivery_notes')
                ->where('id', $id)
                ->update([
                    'vehicle_no' => $request->vehicle_no,
                    'delivery_date' => $request->delivery_date,
                    'location' => $request->location ?? 'Jakarta',
                    'sender_name' => $request->sender_name ?? 'KOPKAR MKM',
                    'receiver_name' => $request->receiver_name,
                    'footer_code' => $request->footer_code,
                    'notes' => $request->notes,
                    'updated_at' => now()
                ]);

            // Delete old items
            DB::table('delivery_note_items')
                ->where('delivery_note_id', $id)
                ->delete();

            // Insert new items
            if ($request->has('items')) {
                foreach ($request->items as $item) {
                    DB::table('delivery_note_items')->insert([
                        'delivery_note_id' => $id,
                        'item_name' => $item['item_name'],
                        'quantity' => $item['quantity'],
                        'unit' => $item['unit'],
                        'description' => $item['description'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Surat Pengantar berhasil diupdate'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal update Surat Pengantar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::table('delivery_notes')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Surat Pengantar berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus Surat Pengantar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function detail($id)
    {
        $deliveryNote = DB::table('delivery_notes')
            ->where('id', $id)
            ->first();

        $items = DB::table('delivery_note_items')
            ->where('delivery_note_id', $id)
            ->get();

        return response()->json([
            'deliveryNote' => $deliveryNote,
            'items' => $items
        ]);
    }

    public function generatePDF($id)
    {
        $deliveryNote = DB::table('delivery_notes')
            ->where('id', $id)
            ->first();

        $items = DB::table('delivery_note_items')
            ->where('delivery_note_id', $id)
            ->get();

        $pdf = Pdf::loadView('delivery_note.pdf', compact('deliveryNote', 'items'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('surat-pengantar-' . $deliveryNote->delivery_note_no . '.pdf');
    }
}