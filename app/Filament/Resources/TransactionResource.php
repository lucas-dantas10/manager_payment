<?php

namespace App\Filament\Resources;

use App\Enums\TransactionTypes;
use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $modelLabel = 'transações';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->label('Valor da Transação')
                    ->numeric()
                    ->inputMode('decimal')
                    ->placeholder('0.00')
                    ->required(),
                Forms\Components\Select::make('type')
                    ->label('Tipo')
                    ->options([
                        'income' => 'Receita',
                        'expense' => 'Gasto',
                    ])
                    ->required(),
                Forms\Components\Select::make('payment_method')
                    ->label('Método de Pagamento')
                    ->options([
                        'cc' => 'Cartão de Crédito',
                        'cd' => 'Cartão de Débito',
                        'p' => 'Pix',
                        'b' => 'Boleto',
                    ])
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pay' => 'Pago',
                        'not_pay' => 'Não Pago',
                        'pending' => 'Pendente',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('description')
                    ->label('Valor da Descrição')
                    ->required()
                    ->placeholder('Pagamento faculdade')
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('date_transaction')
                    ->label('Data da Transação')
                    ->default(now())
                    ->seconds(false)
                    ->timezone('America/Sao_Paulo')
                    ->native(false)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuário')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->description(fn (Transaction $record): string => $record->type == TransactionTypes::EXPENSE->value ? 'Gasto' : 'Receita')
                    ->color(fn (Transaction $record): string => $record->type == TransactionTypes::EXPENSE->value ? 'danger' : 'success')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Valor')
                    ->money('brl')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descrição')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_transaction')
                    ->label('Data da transação')
                    ->since()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipos')
                    ->options([
                        'expense' => 'Gastos',
                        'income' => 'Receita',
                    ]),
                Filter::make('date_transaction')
                    ->label('Data da Transação')
                    ->form([
                        DatePicker::make('date_transaction')
                            ->label('Data da Transação')
                            ->format('d/m/Y'),
                    ])->query(function (Builder $query, array $data): Builder {
                            $date = $data['date_transaction'];
                            return $query
                                ->where('date_transaction', 'like', "%{$date}%");
                        })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function(Builder $query) {
                if (auth()->user()->is_admin) {
                    return $query->with('payment');
                }

                return $query->where('user_id', auth()->user()->id);
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
