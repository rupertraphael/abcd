<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    public function files()
    {
        return $this->belongsToMany('App\File');
    }

    public function team()
    {
        return $this->belongsTo('App\Team');
    }

    // Query Scopes
    public function scopeInTeams($query, $teams) {
        return $query->whereIn('team_id', $teams);
    }

    public function assignToTeam($team)
    {
        return $this->team()->associate($team);
    }
}