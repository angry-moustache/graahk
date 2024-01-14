<?php

namespace App\Filament\Components\Columns;

use AngryMoustache\Media\Models\Attachment;
use Filament\Tables\Columns\ImageColumn;

class AttachmentColumn extends ImageColumn
{
    public function getImageUrl(?string $state = null): ?string
    {
        return Attachment::find($state)?->path();
    }
}
