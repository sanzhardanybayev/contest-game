<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FirstTour extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'first_tour';

    /**
     * Get the teams
     */
    public function teams()
    {
        return $this->hasMany('App\Team');
    }

}
