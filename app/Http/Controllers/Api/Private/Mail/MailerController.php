<?php

namespace App\Http\Controllers\Api\Private\Mail;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class MailerController extends Controller
{
    public function composeemail(Request $request)
    {
        $data = $request->validate([
            'emailRecipient' => 'required|email',
            'emailSubject' => 'required|string|max:255',
            'emailBody' => 'required|string',
            'emailAttachments' => 'sometimes|array',
            'emailAttachments.*' => 'file',
        ]);

        try {
            Mail::html($data['emailBody'], function (Message $message) use ($data, $request) {
                $message->to($data['emailRecipient'])->subject($data['emailSubject']);

                foreach ($request->file('emailAttachments', []) as $attachment) {
                    $message->attach($attachment->getRealPath(), [
                        'as' => $attachment->getClientOriginalName(),
                        'mime' => $attachment->getMimeType(),
                    ]);
                }
            });

            return response()->json(['message' => 'message has been sent !'], 200);
        } catch (TransportExceptionInterface $e) {
            return response()->json(['message' => 'Failed to send email', 'error' => $e->getMessage()], 502);
        }
    }
}
