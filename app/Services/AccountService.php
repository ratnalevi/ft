<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AccountService
{
    public static function AccountsDisplay($id): Collection
    {
        return DB::table('UserAccount as b')
            ->join('Accounts as a', function ($join) {
                $join->on('a.AccountID', '=', 'b.AccountID')
                    ->orWhere('b.ConfigurationID', '=', 9999);
            })
            ->select('a.AccountID')
            ->where('b.UserID', '=', $id)
            ->orderBy('a.AccountID')
            ->select('a.AccountID', 'a.AccountName')
            ->get();
    }

}
