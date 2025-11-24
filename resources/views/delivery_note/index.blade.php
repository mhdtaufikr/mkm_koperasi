<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Surat Pengantar - Kopkar MKM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-file-alt"></i> Surat Pengantar - Kopkar MKM</h4>
                <button class="btn btn-light" onclick="openAddModal()">
                    <i class="fas fa-plus"></i> Tambah Surat Pengantar
                </button>
            </div>
            <div class="card-body">
                <table id="deliveryNoteTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No. Surat</th>
                            <th>No. Kendaraan</th>
                            <th>Tanggal</th>
                            <th>Penerima</th>
                            <th>Lokasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deliveryNotes as $index => $dn)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $dn->delivery_note_no }}</strong></td>
                            <td>{{ $dn->vehicle_no ?? '-' }}</td>
                            <td>{{ date('d/m/Y', strtotime($dn->delivery_date)) }}</td>
                            <td>{{ $dn->receiver_name ?? '-' }}</td>
                            <td>{{ $dn->location }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-info" onclick="viewDetail({{ $dn->id }})" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-warning" onclick="editDeliveryNote({{ $dn->id }})" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger" onclick="deleteDeliveryNote({{ $dn->id }})" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <a href="{{ route('delivery-note.pdf', $dn->id) }}" class="btn btn-success" target="_blank" title="PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Add/Edit -->
    <div class="modal fade" id="deliveryNoteModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">Tambah Surat Pengantar</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="deliveryNoteForm">
                    <div class="modal-body">
                        <input type="hidden" id="delivery_note_id">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">No. Surat Pengantar <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="delivery_note_no" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Kendaraan No.</label>
                                    <input type="text" class="form-control" id="vehicle_no" placeholder="Contoh: B 1234 XYZ">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="delivery_date" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Lokasi</label>
                                    <input type="text" class="form-control" id="location" value="Jakarta">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Pengirim</label>
                                    <input type="text" class="form-control" id="sender_name" value="KOPKAR MKM" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nama Penerima</label>
                                    <input type="text" class="form-control" id="receiver_name" placeholder="Nama yang menerima">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Footer Code</label>
                                    <input type="text" class="form-control" id="footer_code" readonly>
                                    <small class="text-muted">Format: MKM/DX/FR/MEC/MAC/YY/MM/NNN</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" id="notes" rows="2" placeholder="Catatan tambahan (opsional)"></textarea>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Daftar Barang</h5>
                            <button type="button" class="btn btn-sm btn-success" onclick="addItemRow()">
                                <i class="fas fa-plus"></i> Tambah Barang
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="itemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">NO.</th>
                                        <th width="35%">NAMA BARANG</th>
                                        <th width="15%">BANYAKNYA</th>
                                        <th width="15%">SATUAN</th>
                                        <th width="20%">KETERANGAN</th>
                                        <th width="10%">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody">
                                    <!-- Rows akan ditambahkan via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Detail Surat Pengantar</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            $('#deliveryNoteTable').DataTable({
                order: [[3, 'desc']]
            });
        });

        let itemCounter = 0;

        function openAddModal() {
            $('#modalTitle').text('Tambah Surat Pengantar');
            $('#deliveryNoteForm')[0].reset();
            $('#delivery_note_id').val('');
            $('#itemsTableBody').empty();
            itemCounter = 0;

            // Get new delivery note number
            $.get('{{ route("delivery-note.create") }}', function(response) {
                $('#delivery_note_no').val(response.delivery_note_no);
                $('#footer_code').val(response.footer_code);
                $('#delivery_date').val(new Date().toISOString().split('T')[0]);
                $('#location').val('Jakarta');
                $('#sender_name').val('KOPKAR MKM');
                addItemRow(); // Add first row
                $('#deliveryNoteModal').modal('show');
            });
        }

        function addItemRow() {
            itemCounter++;
            const row = `
                <tr>
                    <td class="text-center">${itemCounter}</td>
                    <td><input type="text" class="form-control form-control-sm" name="items[][item_name]" required></td>
                    <td><input type="number" step="0.01" class="form-control form-control-sm" name="items[][quantity]" required></td>
                    <td><input type="text" class="form-control form-control-sm" name="items[][unit]" placeholder="UNIT" required></td>
                    <td><input type="text" class="form-control form-control-sm" name="items[][description]"></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeItemRow(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#itemsTableBody').append(row);
            updateRowNumbers();
        }

        function removeItemRow(btn) {
            $(btn).closest('tr').remove();
            updateRowNumbers();
        }

        function updateRowNumbers() {
            $('#itemsTableBody tr').each(function(index) {
                $(this).find('td:first').text(index + 1);
            });
            itemCounter = $('#itemsTableBody tr').length;
        }

        $('#deliveryNoteForm').submit(function(e) {
            e.preventDefault();

            const id = $('#delivery_note_id').val();
            const url = id ? '{{ url("delivery-note") }}/' + id : '{{ route("delivery-note.store") }}';
            const method = id ? 'PUT' : 'POST';

            const items = [];
            $('#itemsTableBody tr').each(function() {
                items.push({
                    item_name: $(this).find('input[name="items[][item_name]"]').val(),
                    quantity: $(this).find('input[name="items[][quantity]"]').val(),
                    unit: $(this).find('input[name="items[][unit]"]').val(),
                    description: $(this).find('input[name="items[][description]"]').val()
                });
            });

            if (items.length === 0) {
                Swal.fire('Peringatan!', 'Minimal harus ada 1 barang', 'warning');
                return;
            }

            const formData = {
                delivery_note_no: $('#delivery_note_no').val(),
                vehicle_no: $('#vehicle_no').val(),
                delivery_date: $('#delivery_date').val(),
                location: $('#location').val(),
                sender_name: $('#sender_name').val(),
                receiver_name: $('#receiver_name').val(),
                footer_code: $('#footer_code').val(),
                notes: $('#notes').val(),
                items: items
            };

            // Show loading
            Swal.fire({
                title: 'Menyimpan...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: url,
                method: method,
                data: formData,
                success: function(response) {
                    if (response.success) {
                        const downloadId = id ? id : response.id;

                        // Close modal
                        $('#deliveryNoteModal').modal('hide');

                        // Auto download PDF menggunakan iframe (lebih halus)
                        const iframe = document.createElement('iframe');
                        iframe.style.display = 'none';
                        iframe.src = '{{ url("delivery-note") }}/' + downloadId + '/pdf';
                        document.body.appendChild(iframe);

                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            html: 'Data tersimpan<br>PDF sedang diunduh...',
                            showConfirmButton: true,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });

                        // Remove iframe after download
                        setTimeout(() => {
                            document.body.removeChild(iframe);
                        }, 5000);
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Terjadi kesalahan', 'error');
                }
            });
        });

        function editDeliveryNote(id) {
            $.get('{{ url("delivery-note") }}/' + id + '/edit', function(response) {
                $('#modalTitle').text('Edit Surat Pengantar');
                $('#delivery_note_id').val(id);
                $('#delivery_note_no').val(response.deliveryNote.delivery_note_no);
                $('#vehicle_no').val(response.deliveryNote.vehicle_no);
                $('#delivery_date').val(response.deliveryNote.delivery_date);
                $('#location').val(response.deliveryNote.location);
                $('#sender_name').val(response.deliveryNote.sender_name);
                $('#receiver_name').val(response.deliveryNote.receiver_name);
                $('#footer_code').val(response.deliveryNote.footer_code);
                $('#notes').val(response.deliveryNote.notes);

                $('#itemsTableBody').empty();
                itemCounter = 0;
                response.items.forEach(function(item) {
                    itemCounter++;
                    const row = `
                        <tr>
                            <td class="text-center">${itemCounter}</td>
                            <td><input type="text" class="form-control form-control-sm" name="items[][item_name]" value="${item.item_name}" required></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="items[][quantity]" value="${item.quantity}" required></td>
                            <td><input type="text" class="form-control form-control-sm" name="items[][unit]" value="${item.unit}" required></td>
                            <td><input type="text" class="form-control form-control-sm" name="items[][description]" value="${item.description || ''}"></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-danger" onclick="removeItemRow(this)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    $('#itemsTableBody').append(row);
                });

                $('#deliveryNoteModal').modal('show');
            });
        }

        function deleteDeliveryNote(id) {
            Swal.fire({
                title: 'Anda yakin?',
                text: "Data akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url("delivery-note") }}/' + id,
                        method: 'DELETE',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: response.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                setTimeout(() => location.reload(), 1500);
                            }
                        }
                    });
                }
            });
        }

        function viewDetail(id) {
            $.get('{{ url("delivery-note") }}/' + id + '/detail', function(response) {
                let html = `
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">No. Surat</th>
                                    <td>${response.deliveryNote.delivery_note_no}</td>
                                </tr>
                                <tr>
                                    <th>No. Kendaraan</th>
                                    <td>${response.deliveryNote.vehicle_no || '-'}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal</th>
                                    <td>${new Date(response.deliveryNote.delivery_date).toLocaleDateString('id-ID')}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Lokasi</th>
                                    <td>${response.deliveryNote.location}</td>
                                </tr>
                                <tr>
                                    <th>Pengirim</th>
                                    <td>${response.deliveryNote.sender_name}</td>
                                </tr>
                                <tr>
                                    <th>Penerima</th>
                                    <td>${response.deliveryNote.receiver_name || '-'}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <hr>
                    <h6 class="mb-3">Daftar Barang:</h6>
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="40%">Nama Barang</th>
                                <th width="15%">Banyaknya</th>
                                <th width="15%">Satuan</th>
                                <th width="25%">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                response.items.forEach((item, index) => {
                    html += `
                        <tr>
                            <td class="text-center">${index + 1}</td>
                            <td>${item.item_name}</td>
                            <td class="text-center">${item.quantity}</td>
                            <td>${item.unit}</td>
                            <td>${item.description || '-'}</td>
                        </tr>
                    `;
                });

                html += `
                        </tbody>
                    </table>
                `;

                if (response.deliveryNote.notes) {
                    html += `
                        <div class="alert alert-info mt-3">
                            <strong>Catatan:</strong> ${response.deliveryNote.notes}
                        </div>
                    `;
                }

                html += `<p class="text-muted mt-3 mb-0"><small>Footer Code: ${response.deliveryNote.footer_code}</small></p>`;

                $('#detailContent').html(html);
                $('#detailModal').modal('show');
            });
        }
    </script>
</body>
</html>
