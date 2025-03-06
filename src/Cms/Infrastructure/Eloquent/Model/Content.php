<?php

namespace Swark\Cms\Infrastructure\Eloquent\Model;

use Illuminate\Database\Eloquent\Model;
use Swark\Cms\Domain\Model\ContentType;
use Swark\Cms\Domain\Model\WithContentType;

class Content extends Model implements WithContentType
{
    protected $table = 'content';

    protected $fillable = [
        'scomp_id',
        'content',
        'type',
    ];

    public function contentType(): ContentType {
        return ContentType::from($this->type);
    }
}
