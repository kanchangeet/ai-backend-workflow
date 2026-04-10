<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Master\Enums\MasterStatus;
use Illuminate\Database\Eloquent\Model;

class CategoryModel extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'title',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => MasterStatus::class,
    ];
}
