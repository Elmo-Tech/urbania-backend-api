<?php

namespace App\Http\Requests\ClientOuterTicket;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class SollecitoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ticketId' => 'required|integer|min:1',
            'token' => 'required|string|max:255',
            'sollecito' => 'sometimes|boolean',
            'message' => 'nullable|string',
            'files' => 'sometimes|array|max:20',
            'files.*' => 'array',
            'files.*.actionStatus' => 'required|in:create',
            'files.*.path' => [
                'bail', 'required', 'file', 'max:20480',
                function ($attribute, $file, $fail) {
                    // These uploads are publicly served; do not store executable or active web content.
                    $allowed = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods',
                        'txt', 'csv', 'rtf', 'jpg', 'jpeg', 'png', 'gif', 'tif', 'tiff', 'bmp',
                        'webp', 'zip', '7z', 'rar', 'p7m', 'eml', 'msg'];
                    if (! $file instanceof UploadedFile) {
                        $fail('Invalid attachment.');

                        return;
                    }
                    $extension = strtolower($file->getClientOriginalExtension());
                    $storedExtension = in_array($extension, ['p7m', 'eml', 'msg'], true)
                        ? $extension : strtolower($file->guessExtension() ?? '');
                    if (! in_array($extension, $allowed, true) || ! in_array($storedExtension, $allowed, true)) {
                        $fail('Unsupported attachment format.');
                    }
                },
            ],
        ];
    }
}
