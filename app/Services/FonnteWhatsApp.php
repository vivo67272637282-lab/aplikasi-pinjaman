<?php

namespace App\Services;

use App\Models\WaLog;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use RuntimeException;

class FonnteWhatsApp
{
    public function sendAndLog(
        string $phoneNumber,
        string $message,
        array $logDetails,
        ?string $attachmentContents = null,
        ?string $attachmentName = null
    ): WaLog {
        $log = WaLog::create(array_merge($logDetails, [
            'no_tujuan' => $phoneNumber,
            'pesan' => $message,
            'status' => 'pending',
        ]));

        try {
            $response = $this->sendText($phoneNumber, $message, $attachmentContents, $attachmentName);
            $sent = $response->successful() && $response->json('status') === true;

            $log->update([
                'status' => $sent ? 'terkirim' : 'gagal',
                'response' => $response->body(),
            ]);
        } catch (ConnectionException|InvalidArgumentException|RuntimeException $exception) {
            $log->update([
                'status' => 'gagal',
                'response' => $exception->getMessage(),
            ]);

            Log::warning('Fonnte gagal mengirim pesan WhatsApp.', [
                'wa_log_id' => $log->id,
                'exception' => $exception,
            ]);
        }

        return $log->refresh();
    }

    private function sendText(
        string $phoneNumber,
        string $message,
        ?string $attachmentContents,
        ?string $attachmentName
    ): Response
    {
        $token = config('services.fonnte.token');
        if (!$token) {
            throw new RuntimeException(
                'Fonnte belum dikonfigurasi. Isi FONNTE_TOKEN di file .env.'
            );
        }

        $recipient = preg_replace('/\D+/', '', $phoneNumber);
        if (!$recipient || strlen($recipient) < 8 || strlen($recipient) > 15) {
            throw new InvalidArgumentException('Nomor WhatsApp tidak valid.');
        }

        if (str_starts_with($recipient, '0')) {
            $recipient = '62' . substr($recipient, 1);
        } elseif (str_starts_with($recipient, '8')) {
            $recipient = '62' . $recipient;
        }

        if (($attachmentContents === null) !== ($attachmentName === null)) {
            throw new InvalidArgumentException('Nama dan isi lampiran harus disediakan bersamaan.');
        }

        $request = Http::withHeaders(['Authorization' => $token])
            ->acceptJson()
            ->timeout(30);

        $data = [
            'target' => $recipient,
            'message' => $message,
            'countryCode' => '0',
        ];

        if ($attachmentContents !== null && $attachmentName !== null) {
            $request->attach('file', $attachmentContents, $attachmentName);
        } else {
            $request->asForm();
        }

        return $request->post('https://api.fonnte.com/send', $data);
    }
}
