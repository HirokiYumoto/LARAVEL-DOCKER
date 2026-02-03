<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // 既に存在しない場合のみ作成
        if (!User::where('email', 'admin@example.com')->exists()) {
            User::create([
                'name' => 'システム管理者',
                'email' => 'admin@example.com',
                'password' => Hash::make('password123'), // 任意のパスワード
                'role_id' => 3, // ★重要：管理者は 3 とする
            ]);
        }
    }
}