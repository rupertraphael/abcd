<?php

namespace App;

use Spatie\Permission\Models\Permission as Model;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as HasAuditable;

class Permission extends Model implements Auditable
{
    use HasAuditable;
}
