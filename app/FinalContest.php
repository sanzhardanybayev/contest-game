<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FinalContest extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'final';//

    /**
     * Get the teams
     */
    public function teams()
    {
        return $this->hasMany('App\Team');
    }
}
