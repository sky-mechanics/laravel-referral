<?php

/*
 * This file is part of questocat/laravel-referral package.
 *
 * (c) questocat <zhengchaopu@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Questocat\Referral\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cookie;

trait UserReferral
{
    public function getReferralLink()
    {
        return url('/').'/?ref='.$this->affiliate_id;
    }

    public function referred()
    {
        return $this->hasOne(config('referral.user_model', 'App\User'), 'affiliate_id', 'referred_by');
    }

    public function referrals() {
        return $this->hasMany(config('referral.user_model', 'App\User'), 'referred_by', 'affiliate_id');
    }

    public static function scopeReferralExists(Builder $query, $referral)
    {
        return $query->whereAffiliateId($referral)->exists();
    }

    public static function bootUserReferral()
    {
        static::creating(function ($model) {
            if ($referredBy = Cookie::get('referral')) {
                $model->referred_by = $referredBy;
            }

            $model->affiliate_id = self::generateReferral();
        });
    }

    protected static function generateReferral()
    {
        return uniqid();
    }
}
