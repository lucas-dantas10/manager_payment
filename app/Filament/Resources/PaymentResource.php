<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $modelLabel = 'pagamento';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('transaction_id')
                    ->relationship('transaction', 'id')
                    ->required(),
                Forms\Components\TextInput::make('payment_method')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('created_by')
                    ->required()
                    ->maxLength(36),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction.description')
                    ->label('Transação')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->color(function (Payment $payment) {
                        if ($payment->status == 'Pendente') {
                            return 'warning';
                        } else if ($payment->status == 'Pago') {
                            return 'success';
                        } else {
                            return 'danger';
                        }
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query
                    ->select(
                        'id',
                        'transaction_id',
                        DB::raw("CASE 
                            WHEN payment_method = 'cc' THEN 'Cartão de Crédito'
                            WHEN payment_method = 'p' THEN 'Pix'
                            WHEN payment_method = 'cd' THEN 'Cartão de Débito'
                            WHEN payment_method = 'b' THEN 'Boleto'
                            ELSE payment_method
                        END AS payment_method"),
                        DB::raw("CASE 
                            WHEN status = 'pending' THEN 'Pendente'
                            WHEN status = 'pay' THEN 'Pago'
                            WHEN status = 'not_pay' THEN 'Não Pago'
                            ELSE status
                        END AS status"),
                        'created_by',
                        'created_at',
                        'updated_at'
                    )
                    ->where('user_id', auth()->id());
            });
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
