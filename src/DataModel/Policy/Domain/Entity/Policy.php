<?php

namespace Swark\DataModel\Policy\Domain\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swark\DataModel\Compliance\Domain\Entity\Chapter;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasDescription;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasName;
use Swark\DataModel\Kernel\Infrastructure\Aspects\HasScompId;

class Policy extends Model
{
    use HasScompId, HasName, HasDescription;

    protected $table = 'policy';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'scomp_id',
        'description',
    ];

    public function regulationChapters(): BelongsToMany
    {
        return $this->belongsToMany(Chapter::class, 'policy_for_regulation_chapter');
    }

    public function rules(): HasMany
    {
        return $this->hasMany(Rule::class)->orderBy('order_column');
    }
}
