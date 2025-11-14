<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GiftCardResource\Pages;
use App\Filament\Resources\GiftCardResource\RelationManagers;
use App\Models\GiftCard;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

/**
 * GiftCard resource for Filament admin panel.
 *
 * @package App\Filament\Resources
 * @since 1.0.0
 */
class GiftCardResource extends Resource
{
    protected static ?string $model = GiftCard::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationLabel = 'Gift Cards';

    protected static ?string $modelLabel = 'Gift Card';

    protected static ?string $pluralModelLabel = 'Gift Cards';

    protected static ?int $navigationSort = 1;

    /**
     * Configure the form schema.
     *
     * @param Form $form The form instance
     * @return Form The configured form
     * @since 1.0.0
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('gift_card_id')
                            ->label('Gift Card ID')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'sent' => 'Sent',
                                'redeemed' => 'Redeemed',
                                'expired' => 'Expired',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('pending'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Recipient Information')
                    ->schema([
                        Forms\Components\TextInput::make('recipient.first_name')
                            ->label('First Name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('recipient.last_name')
                            ->label('Last Name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('recipient_email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Sender Information')
                    ->schema([
                        Forms\Components\TextInput::make('sender.first_name')
                            ->label('First Name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('sender.last_name')
                            ->label('Last Name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('sender_email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('sender.display_name')
                            ->label('Display Name')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('sender.hide_name')
                            ->label('Hide Name')
                            ->default(false),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Gift Details')
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                        Forms\Components\Select::make('currency')
                            ->options([
                                'USD' => 'USD',
                                'EUR' => 'EUR',
                                'GBP' => 'GBP',
                            ])
                            ->default('USD')
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Delivery Information')
                    ->schema([
                        Forms\Components\DatePicker::make('delivery_date')
                            ->label('Delivery Date'),
                        Forms\Components\Toggle::make('send_immediately')
                            ->label('Send Immediately')
                            ->default(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Occasion')
                    ->schema([
                        Forms\Components\Select::make('occasion.type')
                            ->label('Occasion Type')
                            ->options([
                                'birthday' => 'Birthday',
                                'anniversary' => 'Anniversary',
                                'holiday' => 'Holiday',
                                'thank-you' => 'Thank You',
                                'completely-different' => 'A Special Occasion',
                                'other' => 'Other',
                            ]),
                        Forms\Components\TextInput::make('occasion.display_name')
                            ->label('Display Name')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Personalization')
                    ->schema([
                        Forms\Components\Textarea::make('personalization.message')
                            ->label('Message')
                            ->rows(3),
                        Forms\Components\TextInput::make('personalization.photo')
                            ->label('Photo URL')
                            ->url()
                            ->maxLength(500),
                        Forms\Components\TextInput::make('personalization.in_recognition_of')
                            ->label('In Recognition Of')
                            ->maxLength(255),
                    ]),

                Forms\Components\Section::make('Redemption')
                    ->schema([
                        Forms\Components\TextInput::make('redemption.code')
                            ->label('Redemption Code')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('redemption.url')
                            ->label('Redemption URL')
                            ->url()
                            ->maxLength(500),
                    ])
                    ->columns(2),
            ]);
    }

    /**
     * Configure the table schema.
     *
     * @param Table $table The table instance
     * @return Table The configured table
     * @since 1.0.0
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('gift_card_id')
                    ->label('Gift Card ID')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Gift Card ID copied!'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'sent' => 'info',
                        'redeemed' => 'success',
                        'expired' => 'gray',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('recipient_full_name')
                    ->label('Recipient')
                    ->getStateUsing(fn (GiftCard $record): string => 
                        $record->recipient['full_name'] ?? 
                        ($record->recipient['first_name'] . ' ' . $record->recipient['last_name'] ?? '')
                    )
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where('recipient_email', 'like', "%{$search}%")
                            ->orWhereJsonContains('recipient->first_name', $search)
                            ->orWhereJsonContains('recipient->last_name', $search);
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('recipient_email')
                    ->label('Recipient Email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('sender_display_name')
                    ->label('Sender')
                    ->getStateUsing(fn (GiftCard $record): string => 
                        $record->sender['display_name'] ?? 
                        ($record->sender['first_name'] . ' ' . $record->sender['last_name'] ?? '')
                    )
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where('sender_email', 'like', "%{$search}%")
                            ->orWhereJsonContains('sender->first_name', $search)
                            ->orWhereJsonContains('sender->last_name', $search);
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('formatted_amount')
                    ->label('Amount')
                    ->getStateUsing(fn (GiftCard $record): string => 
                        $record->formatted_amount ?? 
                        '$' . number_format($record->amount ?? 0, 2)
                    )
                    ->sortable(query: fn (Builder $query, string $direction): Builder => 
                        $query->orderBy('amount', $direction)
                    ),
                Tables\Columns\IconColumn::make('send_immediately')
                    ->label('Send Immediately')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('delivery_date')
                    ->label('Delivery Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('redemption_code')
                    ->label('Redemption Code')
                    ->getStateUsing(fn (GiftCard $record): string => 
                        $record->redemption_code ?? $record->gift_card_id
                    )
                    ->copyable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'sent' => 'Sent',
                        'redeemed' => 'Redeemed',
                        'expired' => 'Expired',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\Filter::make('send_immediately')
                    ->label('Send Immediately')
                    ->toggle(),
                Tables\Filters\Filter::make('delivery_date')
                    ->form([
                        Forms\Components\DatePicker::make('delivered_from')
                            ->label('Delivered From'),
                        Forms\Components\DatePicker::make('delivered_until')
                            ->label('Delivered Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['delivered_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('delivery_date', '>=', $date),
                            )
                            ->when(
                                $data['delivered_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('delivery_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('redemption_url')
                    ->label('View Redemption')
                    ->url(fn (GiftCard $record): string => $record->redemption_url ?? '#')
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-link')
                    ->visible(fn (GiftCard $record): bool => !empty($record->redemption_url)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * Get the pages for this resource.
     *
     * @return array<string, class-string>
     * @since 1.0.0
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGiftCards::route('/'),
            'create' => Pages\CreateGiftCard::route('/create'),
            'view' => Pages\ViewGiftCard::route('/{record}'),
            'edit' => Pages\EditGiftCard::route('/{record}/edit'),
        ];
    }
}
