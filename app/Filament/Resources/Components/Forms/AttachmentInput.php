<?php

namespace App\Filament\Components\Forms;

use AngryMoustache\Media\Models\Attachment;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Set;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class AttachmentInput extends FileUpload
{
    public function setUp(): void
    {
        parent::setUp();

        $this->storeFiles(false);

        $this->afterStateHydrated(static function (BaseFileUpload $component, string|array|null $state): void {
            $files = Attachment::find(Arr::wrap($state ?? []))
                ->mapWithKeys(fn (Attachment $file): array => [((string) Str::uuid()) => $file])
                ->all();

            $component->state($files);

        });

        $this->getUploadedFileUsing(static function (Attachment $file): ?array {
            return [
                'name' => $file->name,
                'size' => $file->size,
                'type' => $file->mime_type,
                'url' => $file->path(),
            ];
        });

        $this->beforeStateDehydrated(function (FileUpload $component, Set $set, array $state) {
            $attachment = Arr::first($state);

            if ($attachment instanceof TemporaryUploadedFile) {
                $attachment = Attachment::livewireUpload($attachment);
            }

            if (is_int($attachment)) {
                $attachment = Attachment::find($attachment);
            }

            $set($component->getStatePath(false), [
                $attachment->id,
            ]);
        });
    }
}
