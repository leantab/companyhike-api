<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsersResource\Pages;
use App\Filament\Resources\UsersResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UsersResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->maxLength(255)
                    ->required(),
                Forms\Components\TextInput::make('lastname')
                    ->maxLength(255)
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255)
                    ->required(),
                Forms\Components\TextInput::make('password')->password(),
                Forms\Components\Toggle::make('ch_admin')->label('Administrador CH')->inline(),
                Forms\Components\Toggle::make('active')->label('Activo')->inline(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('lastname')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable()
                    ->limit(100),
                Tables\Columns\TextColumn::make('communities.name'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado el')
                    ->date('d/m/Y'),
                IconColumn::make('ch_admin')
                    ->label('Admin CH')
                    ->boolean()
                    ->falseColor('black'),
                IconColumn::make('active')
                    ->label('Activo')
                    ->boolean()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUsers::route('/create'),
            'edit' => Pages\EditUsers::route('/{record}/edit'),
        ];
    }
}
