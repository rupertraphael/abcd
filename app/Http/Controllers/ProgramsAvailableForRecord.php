<?php

namespace App\Http\Controllers;

use App\Program;
use App\Record;
use App\RecordType;

use App\Http\Resources\Programs;

use Illuminate\Http\Request;

class ProgramsAvailableForRecord extends Controller
{

    public function __invoke(RecordType $recordType, Record $record)
    {
        $selectedPrograms = $record->programs()->pluck('programs.id');
        $programs = Program::whereNotIn('programs.id', $selectedPrograms);

        // Search
        $search = request('search');
        $programs = $programs->search($search);

        // Sort per request.
        $ascending = request('ascending');
        $sortBy = request('sortBy');
        $programs = $programs->sort($sortBy, $ascending);

        $programs = $programs->availableFor(auth()->user());

        // Paginate per request.
        $perPage = request('perPage');
        $programs = $programs->paginate($perPage);

        return (new Programs($programs));
    }
}