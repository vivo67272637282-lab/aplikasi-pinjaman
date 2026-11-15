<?php

namespace App\Mail;

use App\Models\Potongan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SlipPotonganMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Potongan $potongan)
    {
    }

    public function build(): self
    {
        $pdf = Pdf::loadView('slip.pdf', ['potongan' => $this->potongan])
            ->setPaper('a5', 'portrait');

        return $this->subject('Rincian Potongan ' . $this->potongan->periode->nama)
            ->view('emails.slip-potongan')
            ->with(['potongan' => $this->potongan])
            ->attachData(
                $pdf->output(),
                'rincian-potongan-' . $this->potongan->karyawan->nik . '.pdf',
                ['mime' => 'application/pdf']
            );
    }
}
