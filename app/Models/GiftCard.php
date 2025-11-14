<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * GiftCard model representing an e-gift card.
 *
 * @package App\Models
 * @since 1.0.0
 * @category Models
 */
class GiftCard extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'gift_card_id',
        'status',
        'occasion',
        'recipient',
        'sender',
        'delivery',
        'gift',
        'personalization',
        'redemption',
        'checkout',
        'recipient_email',
        'sender_email',
        'amount',
        'currency',
        'delivery_date',
        'send_immediately',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'occasion' => 'array',
            'recipient' => 'array',
            'sender' => 'array',
            'delivery' => 'array',
            'gift' => 'array',
            'personalization' => 'array',
            'redemption' => 'array',
            'checkout' => 'array',
            'amount' => 'decimal:2',
            'delivery_date' => 'date',
            'send_immediately' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the recipient's full name.
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getRecipientFullNameAttribute(): ?string
    {
        return $this->recipient['full_name'] ?? null;
    }

    /**
     * Get the sender's display name.
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getSenderDisplayNameAttribute(): ?string
    {
        return $this->sender['display_name'] ?? null;
    }

    /**
     * Get the formatted gift amount.
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getFormattedAmountAttribute(): ?string
    {
        return $this->gift['formatted_amount'] ?? null;
    }

    /**
     * Get the redemption code.
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getRedemptionCodeAttribute(): ?string
    {
        return $this->redemption['code'] ?? null;
    }

    /**
     * Get the redemption URL.
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getRedemptionUrlAttribute(): ?string
    {
        return $this->redemption['url'] ?? null;
    }

    /**
     * Boot the model.
     *
     * @return void
     * @since 1.0.0
     */
    protected static function boot(): void
    {
        parent::boot();

        // Sync email fields from JSON when saving
        static::saving(function (GiftCard $giftCard): void {
            if (isset($giftCard->recipient['email'])) {
                $giftCard->recipient_email = $giftCard->recipient['email'];
            }
            if (isset($giftCard->sender['email'])) {
                $giftCard->sender_email = $giftCard->sender['email'];
            }
            if (isset($giftCard->gift['amount'])) {
                $giftCard->amount = $giftCard->gift['amount'];
            }
            if (isset($giftCard->delivery['date'])) {
                $giftCard->delivery_date = $giftCard->delivery['date'];
            }
            if (isset($giftCard->delivery['send_immediately'])) {
                $giftCard->send_immediately = $giftCard->delivery['send_immediately'];
            }
        });
    }
}
