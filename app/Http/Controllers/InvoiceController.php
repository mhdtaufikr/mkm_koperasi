<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use PDF;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('items')->latest()->paginate(15);
        return view('invoice.index', compact('invoices'));
    }

    public function create()
    {
        return view('invoice.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client' => 'required|string|max:255',
            'pic' => 'nullable|string|max:255',
            'no_po' => 'nullable|string|max:255',
            'no_invoice' => 'nullable|string|max:255|unique:invoices,no_invoice',
            'tanggal_terbit' => 'nullable|date',
            'tanggal_jatuh_tempo' => 'nullable|date',
            'tujuan_pembayaran' => 'nullable|string|max:255',
            'tertanda' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            // items validation as array
            'items' => 'required|array|min:1',
            'items.*.description' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.quantity_unit' => 'nullable|string',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.tax_included' => 'nullable|boolean',
            'items.*.tax_percent' => 'nullable|numeric|min:0',
            'items.*.gross_up' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // jika no_invoice kosong, generate otomatis (safe loop until unique)
            if (empty($data['no_invoice'])) {
                $data['no_invoice'] = $this->generateInvoiceNumber();
            } else {
                // jika user mengisi no_invoice, pastikan unik (validator sudah cek unique)
            }

            // compute totals
            $subtotal = 0;
            $discount_total = 0;
            $tax_total = 0;
            $total = 0;

            // create invoice header
            $invoice = Invoice::create([
                'client' => $data['client'],
                'pic' => $data['pic'] ?? null,
                'no_po' => $data['no_po'] ?? null,
                'no_invoice' => $data['no_invoice'],
                'tanggal_terbit' => $data['tanggal_terbit'] ?? null,
                'tanggal_jatuh_tempo' => $data['tanggal_jatuh_tempo'] ?? null,
                'tujuan_pembayaran' => $data['tujuan_pembayaran'] ?? null,
                'tertanda' => $data['tertanda'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $it) {
                $lineSubtotal = ($it['quantity'] * $it['price']);

                // interpret discount: if <=100 treat as percent else nominal
                $lineDiscount = 0;
                if (isset($it['discount']) && $it['discount'] !== '') {
                    $d = floatval($it['discount']);
                    if ($d > 0 && $d <= 100) {
                        $lineDiscount = ($lineSubtotal) * ($d / 100);
                    } else {
                        $lineDiscount = $d;
                    }
                }

                $taxPercent = $it['tax_percent'] ?? 0;
                $taxAmount = ($lineSubtotal - $lineDiscount) * ($taxPercent / 100);

                $lineTotal = $lineSubtotal - $lineDiscount + $taxAmount + ($it['gross_up'] ?? 0);

                $item = new InvoiceItem([
                    'description' => $it['description'] ?? null,
                    'quantity' => $it['quantity'],
                    'quantity_unit' => $it['quantity_unit'] ?? null,
                    'price' => $it['price'],
                    'discount' => $lineDiscount,
                    'tax_included' => !empty($it['tax_included']),
                    'tax_percent' => $taxPercent,
                    'gross_up' => $it['gross_up'] ?? 0,
                    'line_subtotal' => $lineSubtotal,
                    'line_total' => $lineTotal,
                ]);

                $invoice->items()->save($item);

                $subtotal += $lineSubtotal;
                $discount_total += $lineDiscount;
                $tax_total += $taxAmount;
                $total += $lineTotal;
            }

            $invoice->update([
                'subtotal' => $subtotal,
                'discount_total' => $discount_total,
                'tax_total' => $tax_total,
                'total' => $total,
            ]);

            DB::commit();

            return redirect()->route('invoice.index')->with('success', 'Invoice berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();

            // Jika exception akibat duplicate key pada unique no_invoice, coba generate kembali sekali
            if ($this->isDuplicateInvoiceException($e)) {
                try {
                    // regenerate unique no_invoice and retry save once
                    $data['no_invoice'] = $this->generateInvoiceNumber();

                    DB::beginTransaction();

                    $invoice = Invoice::create([
                        'client' => $data['client'],
                        'pic' => $data['pic'] ?? null,
                        'no_po' => $data['no_po'] ?? null,
                        'no_invoice' => $data['no_invoice'],
                        'tanggal_terbit' => $data['tanggal_terbit'] ?? null,
                        'tanggal_jatuh_tempo' => $data['tanggal_jatuh_tempo'] ?? null,
                        'tujuan_pembayaran' => $data['tujuan_pembayaran'] ?? null,
                        'tertanda' => $data['tertanda'] ?? null,
                        'notes' => $data['notes'] ?? null,
                    ]);

                    $subtotal = $discount_total = $tax_total = $total = 0;
                    foreach ($data['items'] as $it) {
                        $lineSubtotal = ($it['quantity'] * $it['price']);
                        $lineDiscount = 0;
                        if (isset($it['discount']) && $it['discount'] !== '') {
                            $d = floatval($it['discount']);
                            if ($d > 0 && $d <= 100) {
                                $lineDiscount = ($lineSubtotal) * ($d / 100);
                            } else {
                                $lineDiscount = $d;
                            }
                        }
                        $taxPercent = $it['tax_percent'] ?? 0;
                        $taxAmount = ($lineSubtotal - $lineDiscount) * ($taxPercent / 100);
                        $lineTotal = $lineSubtotal - $lineDiscount + $taxAmount + ($it['gross_up'] ?? 0);

                        $invoice->items()->create([
                            'description' => $it['description'] ?? null,
                            'quantity' => $it['quantity'],
                            'quantity_unit' => $it['quantity_unit'] ?? null,
                            'price' => $it['price'],
                            'discount' => $lineDiscount,
                            'tax_included' => !empty($it['tax_included']),
                            'tax_percent' => $taxPercent,
                            'gross_up' => $it['gross_up'] ?? 0,
                            'line_subtotal' => $lineSubtotal,
                            'line_total' => $lineTotal,
                        ]);

                        $subtotal += $lineSubtotal;
                        $discount_total += $lineDiscount;
                        $tax_total += $taxAmount;
                        $total += $lineTotal;
                    }

                    $invoice->update([
                        'subtotal' => $subtotal,
                        'discount_total' => $discount_total,
                        'tax_total' => $tax_total,
                        'total' => $total,
                    ]);

                    DB::commit();

                    return redirect()->route('invoice.index')->with('success', 'Invoice berhasil disimpan (retry)');
                } catch (\Exception $e2) {
                    DB::rollBack();
                    return back()->withInput()->withErrors(['error' => 'Gagal menyimpan invoice setelah retry: ' . $e2->getMessage()]);
                }
            }

            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('items');
        return view('invoice.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('items');
        return view('invoice.edit', compact('invoice'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'client' => 'required|string|max:255',
            'pic' => 'nullable|string|max:255',
            'no_po' => 'nullable|string|max:255',
            'no_invoice' => ['nullable','string','max:255', Rule::unique('invoices','no_invoice')->ignore($invoice->id)],
            'tanggal_terbit' => 'nullable|date',
            'tanggal_jatuh_tempo' => 'nullable|date',
            'tujuan_pembayaran' => 'nullable|string|max:255',
            'tertanda' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            $invoice->update([
                'client' => $data['client'],
                'pic' => $data['pic'] ?? null,
                'no_po' => $data['no_po'] ?? null,
                'no_invoice' => $data['no_invoice'] ?? $invoice->no_invoice,
                'tanggal_terbit' => $data['tanggal_terbit'] ?? null,
                'tanggal_jatuh_tempo' => $data['tanggal_jatuh_tempo'] ?? null,
                'tujuan_pembayaran' => $data['tujuan_pembayaran'] ?? null,
                'tertanda' => $data['tertanda'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            // remove old items & recalc
            $invoice->items()->delete();

            $subtotal = 0;
            $discount_total = 0;
            $tax_total = 0;
            $total = 0;

            foreach ($data['items'] as $it) {
                $lineSubtotal = ($it['quantity'] * $it['price']);
                $lineDiscount = 0;
                if (isset($it['discount']) && $it['discount'] !== '') {
                    $d = floatval($it['discount']);
                    if ($d > 0 && $d <= 100) {
                        $lineDiscount = ($lineSubtotal) * ($d / 100);
                    } else {
                        $lineDiscount = $d;
                    }
                }

                $taxPercent = $it['tax_percent'] ?? 0;
                $taxAmount = ($lineSubtotal - $lineDiscount) * ($taxPercent / 100);
                $lineTotal = $lineSubtotal - $lineDiscount + $taxAmount + ($it['gross_up'] ?? 0);

                $invoice->items()->create([
                    'description' => $it['description'] ?? null,
                    'quantity' => $it['quantity'],
                    'quantity_unit' => $it['quantity_unit'] ?? null,
                    'price' => $it['price'],
                    'discount' => $lineDiscount,
                    'tax_included' => !empty($it['tax_included']),
                    'tax_percent' => $taxPercent,
                    'gross_up' => $it['gross_up'] ?? 0,
                    'line_subtotal' => $lineSubtotal,
                    'line_total' => $lineTotal,
                ]);

                $subtotal += $lineSubtotal;
                $discount_total += $lineDiscount;
                $tax_total += $taxAmount;
                $total += $lineTotal;
            }

            $invoice->update([
                'subtotal' => $subtotal,
                'discount_total' => $discount_total,
                'tax_total' => $tax_total,
                'total' => $total,
            ]);

            DB::commit();
            return redirect()->route('invoice.index')->with('success', 'Invoice berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoice.index')->with('success', 'Invoice dihapus');
    }

    /**
     * Generate nomor invoice unik.
     * Format contoh: KMS/YYMM/000001/DD/MM/YYYY
     */
    protected function generateInvoiceNumber()
    {
        $prefixRoot = 'KMS/';
        $ym = date('ym'); // YYMM
        $day = date('d');
        $month = date('m');
        $yearFull = date('Y');

        $counter = 1;
        // loop untuk mencari nomor unik
        while (true) {
            $seq = str_pad($counter, 6, '0', STR_PAD_LEFT);
            $candidate = $prefixRoot . $ym . '/' . $seq . '/' . $day . '/' . $month . '/' . $yearFull;

            $exists = Invoice::where('no_invoice', $candidate)->exists();
            if (!$exists) {
                return $candidate;
            }

            $counter++;

            // safety cap: jika sudah nyoba 10000 kali, break dan gunakan microtime
            if ($counter > 10000) {
                $candidate = $prefixRoot . $ym . '/' . uniqid() . '/' . $day . '/' . $month . '/' . $yearFull;
                if (!Invoice::where('no_invoice', $candidate)->exists()) {
                    return $candidate;
                }
            }
        }
    }

    /**
     * Detect apakah exception berkaitan duplicate unique key no_invoice.
     */
    protected function isDuplicateInvoiceException(\Exception $e)
    {
        $msg = $e->getMessage();
        return (stripos($msg, 'Duplicate entry') !== false && stripos($msg, 'no_invoice') !== false)
            || (stripos($msg, 'invoices_no_invoice_unique') !== false);
    }
    public function exportPdf(Invoice $invoice)
    {
        // eager load items
        $invoice->load('items');

        // siapkan data untuk view
        $data = [
            'invoice' => $invoice,
            'items' => $invoice->items,
        ];

        // generate view menjadi PDF
        $pdf = PDF::loadView('invoice.pdf', $data);

        // nama file contoh: invoice-<no_invoice>.pdf
        $filename = 'invoice-' . preg_replace('/[^A-Za-z0-9\-]/','', $invoice->no_invoice) . '.pdf';

        // download langsung
        return $pdf->download($filename);
    }
}
