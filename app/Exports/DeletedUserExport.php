<?php

namespace App\Exports;

use App\Model\DeletedUser;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DeletedUserExport implements FromQuery, WithHeadings
{
    use Exportable;


    public function forUser()
    {
        
        return $this;
        //->paginate($limit);

    }

    public function query()
    {
        $users = DeletedUser::query()->select('id','username','name','provider','platform','is_active','is_subscribed','created_at','deleted_at')
        ->orderBy('deleted_at','desc');
        
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
            'Deleted At',
        ];
    }
}
