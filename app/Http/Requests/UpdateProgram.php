<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProgram extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'exists:programs,id',
            'name' => [
                    'sometimes',
                    'required',

                    // Check whether the program name already exists within the team (excludes the program being edited).
                    Rule::unique('programs')->where(function ($query) {
                        return $query->where('team_id', $this->team_id)->where('id', '!=', $this->id);
                    })
                ],
            'team_id' => 'sometimes|required|exists:teams,id',
            'default_client_status_id' => 'sometimes|required|exists:client_statuses,id',
            'case_client_status_id' => 'sometimes|nullable|exists:client_statuses,id',
            'group_client_status_id' => 'sometimes|nullable|exists:client_statuses,id',
            'active' => 'sometimes|boolean'
        ];
    }
}
