@props(['id', 'title', 'action'])

<button type="button" class="btn btn-dark font-weight-bold" data-toggle="modal" data-target="#{{ $id }}">
    Download PDF
</button>

<div class="modal fade" id="{{ $id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius:16px; border:none;">
            <form method="GET" action="{{ $action }}" target="_blank">
                <div class="modal-header" style="border:none;">
                    <h5 class="modal-title font-weight-bolder">Download PDF — {{ $title }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted font-size-sm">Pilih rentang periode data yang mau diunduh.</p>
                    <div class="form-group">
                        <label class="font-weight-bold font-size-sm">Dari Tanggal</label>
                        <input type="date" name="awal" class="form-control form-control-solid" value="{{ now()->subDays(30)->format('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold font-size-sm">Sampai Tanggal</label>
                        <input type="date" name="akhir" class="form-control form-control-solid" value="{{ now()->format('Y-m-d') }}" required>
                    </div>

                    {{ $slot ?? '' }}
                </div>
                <div class="modal-footer" style="border:none;">
                    <button type="button" class="btn btn-light font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold">Unduh PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>