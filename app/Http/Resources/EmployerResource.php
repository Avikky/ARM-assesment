<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        return [
            'id' => $this->id,
            'surname' => $this->surname,
            'firstname' => $this->firstname,
            'email' => $this->email,
            'address' => $this->address,
            'state_of_residence' => $this->state_of_residence,
            'employer_code' => $this->employer_code,
            'mobile_number' => $this->mobile_no,
            'account_number' => $this->acct_no,
            'reg_status' => ($this->reg_status == 1) ? 'Verified' : 'Pending',
            'created_at' => $this->created_at

        ];
    }
}
