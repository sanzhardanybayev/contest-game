<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'groups';

    /**
     * Get the team record associated with the group.
     */
    public function team()
    {
        return $this->hasOne('App\Team');
    }
}
