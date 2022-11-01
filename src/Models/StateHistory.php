<?php

namespace TamkeenTech\LaravelEnumStateMachine\Models;

use Illuminate\Database\Eloquent\Model;

class StateHistory extends Model
{
    protected $guarded = [];

    protected $table = 'state_machine_histories';

    public function responsible()
    {
        return $this->morphTo();
    }
}
