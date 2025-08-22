<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\Traits\HasRoles;

class Role extends SpatieRole
{
    use HasFactory, HasUuids, HasRoles;

    protected $primaryKey = 'uuid';
}
