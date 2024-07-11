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
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 3;

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
                        TextInput::make('customer_email')
                            ->label('Email Pelanggan')
                            ->required(),
                    ])
                    ->columns(2),

                // Section for invoice items
                Card::make()
                    ->schema([
                        Placeholder::make('Daftar Menu'),
                        Repeater::make('InvoiceProduct')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('Product')
                                    ->options(Product::query()->where('tersedia', 1)->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $product = Product::find($state);
                                        if ($product) {
                                            $set('price', $product->price);
                                            $set('product_price', $product->price);
                                        } else {
                                            $set('price', null);
                                            $set('product_price', null);
                                        }
                                    })
                                    ->columnSpan([
                                        'md' => 5,
                                    ]),
                                Forms\Components\TextInput::make('quantity')
                                    ->numeric()
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
                            ->addActionLabel('Add to Menu'),
                    ])
                    ->columnSpan('full'),

                    Card::make()
                        ->schema([
                            Forms\Components\TextInput::make('total_amount'),
                        ])
                        ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer_name')
                    ->label('Nama Pelanggan'),
                TextColumn::make('customer_email')
                    ->label('Email Pelanggan'),

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