<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quarter extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'quarter';

    /**
     * Get the teams
     */
    public function teams()
    {
        return $this->hasMany('App\Team');
    }
}
