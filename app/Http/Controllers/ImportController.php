<?php

namespace App\Http\Controllers;

use App\Models\ImportLog;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImportController extends Controller
{
    public function index()
    {
        $logs = ImportLog::with('user')->latest()->paginate(15);
        return view('import.index', compact('logs'));
    }

    public function create()
    {
        return view('import.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            'tipe' => 'required|in:karyawan,pinjaman,kasbon',
        ]);

        $file     = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $path     = $file->storeAs('import', $fileName, 'public');

        $log = ImportLog::create([
            'user_id'    => auth()->id(),
            'file_name'  => $file->getClientOriginalName(),
            'file_path'  => $path,
            'tipe'       => $request->tipe,
            'total_baris'=> 0,
            'berhasil'   => 0,
            'gagal'      => 0,
            'status'     => 'proses',
        ]);

        // Proses CSV sederhana
        if ($request->tipe === 'karyawan') {
            $this->importKaryawan($log, storage_path('app/public/' . $path));
        }

        return redirect()->route('import.index')
            ->with('success', "File berhasil diimport. Berhasil: {$log->berhasil}, Gagal: {$log->gagal}.");
    }

    protected function importKaryawan(ImportLog $log, string $filePath): void
    {
        if (!file_exists($filePath)) {
            $log->update(['status' => 'gagal', 'pesan_error' => 'File tidak ditemukan.']);
            return;
        }

        $handle = fopen($filePath, 'r');
        $header = fgetcsv($handle); // skip header row
        $berhasil = 0;
        $gagal    = 0;
        $errors   = [];
        $baris    = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $baris++;
            try {
                if (count($row) < 3) {
                    $gagal++;
                    $errors[] = "Baris {$baris}: Data tidak lengkap.";
                    continue;
                }

                Karyawan::updateOrCreate(
                    ['nik' => trim($row[0])],
                    [
                        'nama'       => trim($row[1]),
                        'jabatan'    => trim($row[2] ?? ''),
                        'no_hp'      => trim($row[3] ?? ''),
                        'no_wa'      => trim($row[4] ?? ''),
                        'is_active'  => true,
                    ]
                );
                $berhasil++;
            } catch (\Exception $e) {
                $gagal++;
                $errors[] = "Baris {$baris}: " . $e->getMessage();
            }
        }

        fclose($handle);

        $log->update([
            'total_baris' => $baris,
            'berhasil'    => $berhasil,
            'gagal'       => $gagal,
            'pesan_error' => $errors ? implode("\n", $errors) : null,
            'status'      => 'selesai',
        ]);
    }

    public function mapping()
    {
        return view('import.mapping');
    }

    public function destroy(ImportLog $importLog)
    {
        Storage::disk('public')->delete($importLog->file_path);
        $importLog->delete();
        return back()->with('success', 'Log import berhasil dihapus.');
    }
}
