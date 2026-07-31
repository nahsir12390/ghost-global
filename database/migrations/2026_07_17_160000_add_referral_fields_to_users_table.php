<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code')->nullable()->unique()->after('provider_avatar');
            $table->foreignId('referred_by_id')->nullable()->after('referral_code')->constrained('users')->nullOnDelete();
            $table->timestamp('referral_rewarded_at')->nullable()->after('referred_by_id');
            $table->foreignId('referral_reward_order_id')->nullable()->after('referral_rewarded_at')->constrained('orders')->nullOnDelete();
            $table->index(['referred_by_id', 'referral_rewarded_at']);
        });

        DB::table('users')
            ->select('id')
            ->orderBy('id')
            ->chunkById(100, function ($users) {
                foreach ($users as $user) {
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update([
                            'referral_code' => $this->generateUniqueReferralCode(),
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('referral_reward_order_id');
            $table->dropConstrainedForeignId('referred_by_id');
            $table->dropIndex(['referred_by_id', 'referral_rewarded_at']);
            $table->dropUnique(['referral_code']);
            $table->dropColumn([
                'referral_code',
                'referral_rewarded_at',
            ]);
        });
    }

    private function generateUniqueReferralCode(): string
    {
        do {
            $code = 'REF' . strtoupper(Str::random(8));
        } while (DB::table('users')->where('referral_code', $code)->exists());

        return $code;
    }
};
