<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            'next_of_kin_surname' => $this->nk_surname,
            'next_of_kin_firstname' => $this->nk_firstname,
            'next_of_kin_phone_no' => $this->nk_phone,
            'next_of_kin_email' => $this->nk_email,
            'reg_status' => ($this->reg_status == 1) ? 'Verified' : 'Pending',
            'created_at' => $this->created_at

        ];
    }
}
