<?php

namespace Swark\DataModel\Content\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Swark\Content\Domain\Model\ContentType;
use Swark\Content\Domain\Model\WithContentType;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;

class Content extends Model implements WithContentType
{
    use HasScompId;

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
