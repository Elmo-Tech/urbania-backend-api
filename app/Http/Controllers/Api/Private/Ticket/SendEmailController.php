<?php

namespace App\Http\Controllers\Api\Private\Ticket;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\CreateTicketRequest;
use App\Http\Requests\Ticket\UpdateTicketRequest;
use App\Http\Requests\Ticket\TicketClient\CreateTicketClientRequest;
use App\Http\Requests\Ticket\TicketClient\UpdateTicketClientRequest;
use App\Http\Requests\Ticket\TicketClient\TicketClientContacts\CreateSingleTicketClientContactRequest;
use App\Http\Requests\Ticket\TicketClient\TicketClientContacts\UpdateSingleTicketClientContactRequest;
use App\Http\Requests\Ticket\TicketClient\TicketClientAddresses\CreateSingleTicketClientAddressRequest;
use App\Http\Requests\Ticket\TicketClient\TicketClientAddresses\UpdateSingleTicketClientAddressRequest;
use App\Services\Ticket\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ClientTicketEmail;
use Illuminate\Support\Facades\Storage;




class SendEmailController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function send(Request $request)
    {
        // Validate request data
        $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);

        DB::beginTransaction();
        try {
            $attachments = $request->file('attachments') ?? [];
            $storedAttachments = [];
            $storedAttachmentsToDelete = [];

            // Store files in the 'uploads' disk and prepare the paths for email attachments
            foreach ($attachments as $file) {
                // Store the file in the 'uploads' disk with the original file name
                $filePath = $file->storeAs('attachments', $file->getClientOriginalName(), 'uploads');

                // Get the full storage path for the file (to attach in email)
                $absolutePath = Storage::disk('uploads')->path($filePath);

                // Store the absolute path for email attachments
                $storedAttachments[] = $absolutePath;

                // Keep track of files to delete
                $storedAttachmentsToDelete[] = $filePath;
            }

            // Send email using Mailable with the actual file paths for attachments
            Mail::to($request->email)->send(new ClientTicketEmail(
                $request->subject,
                $request->content,
                $storedAttachments // Pass the array of actual file paths
            ));

            DB::commit();

            // Delete the files after sending the email
            Storage::disk('uploads')->delete($storedAttachmentsToDelete);

            return response()->json(['message' => 'Email Sent!'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to send email', 'message' => $e->getMessage()], 500);
        }
    }
}
