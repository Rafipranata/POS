<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 3;

    public static function updateTotals(Get $get, Set $set): void
    {
        $selectedProducts = collect($get('invoiceProducts'))->filter(fn($item) => !empty($item['product_id']) && !empty($item['quantity']));
        $prices = Product::find($selectedProducts->pluck('product_id'))->pluck('price', 'id');

        $total = $selectedProducts->reduce(function ($total, $product) use ($prices) {
            return $total + ($prices[$product['product_id']] * $product['quantity']);
        }, 0);

        $set('total_amount', $total);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Section for customer details
                Card::make()
                    ->schema([
                        TextInput::make('customer_name')
                            ->label('Nama Pelanggan')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('customer_telp')
                            ->label('No Telp')
                            ->prefix('+62')
                            ,
                    ])
                    ->columns(2),

                // Section for invoice items
                Card::make()
                    ->schema([
                        Placeholder::make('Daftar Menu'),
                        Repeater::make('InvoiceProducts')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('Product')
                                    ->options(Product::query()->where('tersedia', 1)->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, Get $get) {

                                        $product = Product::find($state);
                                        if ($product) {
                                            $set('price', number_format($product->price, 0, ',', '.'));
                                            $set('product_price', $product->price);
                                        } else {
                                            $set('price', null);
                                            $set('product_price', null);
                                        }
                                        self::updateTotals($get, $set);
                                    })

                                    ->columnSpan([
                                        'md' => 5,
                                    ]),
                                Forms\Components\TextInput::make('quantity')
                                    ->numeric()
                                    ->live()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                        self::updateTotals($get, $set);
                                    })
                                    ->minValue(1)
                                    ->default(1)
                                    ->columnSpan([
                                        'md' => 3,
                                    ])
                                    ->required(),
                                Forms\Components\TextInput::make('price')
                                    ->readOnly()
                                    ->dehydrated(false)
                                    ->numeric()
                                    ->columnSpan([
                                        'md' => 4,
                                    ]),
                                Hidden::make('product_price')
                                    ->disabled()
                                    ->dehydrated(true),
                            ])
                            ->defaultItems(1)
                            ->columns(12)
                            ->columnSpan('full')

                            ->addActionLabel('Add to Menu')
                            ->afterStateHydrated(function (Get $get, Set $set) {
                                self::updateTotals($get, $set);
                            }),
                    ])
                    ->columnSpan('full'),

                Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('total_amount')
                            ->prefix('IDR')
                            ->live()
                            ->reactive()
                            ->afterStateHydrated(function (Get $get, Set $set) {
                                self::updateTotals($get, $set);
                            })
                            ->readOnly(),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('customer_name')
                    ->label('Nama Pelanggan')
                    ->searchable(),
                TextColumn::make('customer_telp')
                    ->label('No Telp'),
                TextColumn::make('total_price')
                    ->label('Total Harga')
                    ->formatStateUsing(fn($state, $record) => 'Rp ' . number_format($record->total_price, 0, ',', '.')),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('pdf')
                    ->label('PDF')
                    ->color('success')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record): string => route('pdf', $record->id))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
