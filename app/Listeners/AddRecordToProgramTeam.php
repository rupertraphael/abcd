<?php

namespace App\Listeners;

use App\Events\ProgramRecordSaved;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AddRecordToProgramTeam
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ProgramRecordCreated  $event
     * @return void
     */
    public function handle(ProgramRecordSaved $event)
    {
        $programTeam = $event->program->team_id;
        $recordTeams = $event->record->teams->pluck('id');

        if($recordTeams->contains($programTeam))
            return;

        $event->record->teams()->save($event->program->team);
    }
}