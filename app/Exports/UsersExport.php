<?php

namespace App\Exports;

use App\Model\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromQuery, WithHeadings
{
    use Exportable;


    public function forUser(string $from,string $to,string $is_active,string $email_verified,string $mobile_verified,string $registered_as_writer,string $register_provider,string $platform,string $sortBy,string $sortOrder,int $limit,int $page,string $searchQuery,string $searchBy,string $DBsearchQuery)
    {
        
        $this->from = $from;
        $this->to = $to;
        $this->is_active = $is_active;
        $this->email_verified = $email_verified;
        $this->mobile_verified = $mobile_verified;
        $this->registered_as_writer = $registered_as_writer;
        $this->register_provider = $register_provider;
        $this->platform = $platform;
        $this->sortBy = $sortBy;
        $this->sortOrder = $sortOrder;
        $this->limit = $limit;
        $this->page = $page;
        $this->searchQuery = $searchQuery;
        $this->searchBy = $searchBy;
        $this->DBsearchQuery = $DBsearchQuery;

        return $this;
        //->paginate($limit);

    }

    public function query()
    {
    	$users = User::query()->select('id','username','name','provider','platform','is_active','is_subscribed','created_at')->whereIn('is_active', explode(',',$this->is_active))
        ->whereIn('registered_as_writer',explode(',',$this->registered_as_writer))
        ->whereIn('provider',explode(',',$this->register_provider))
        ->whereIn('platform',explode(',',$this->platform))
        ->where($this->searchBy, 'LIKE', $this->DBsearchQuery)
        ->whereIn('email_verified',explode(',',$this->email_verified))
        ->whereIn('mobile_verified',explode(',',$this->mobile_verified))
        ->whereBetween('created_at',[$this->from,$this->to])
        ->orderBy($this->sortBy,$this->sortOrder);
        
        return $users;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Username',
            'Name',
            'Provider',
            'Plateform',
            'Active',
            'Newsletter',
            'Created At',
        ];
    }
}
