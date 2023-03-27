<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GamesResource\Pages;
use App\Filament\Resources\GamesResource\RelationManagers;
use App\Models\Game;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GamesResource extends Resource
{
    protected static ?string $model = Game::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('community.name')
                    ->label('Comunidad')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Creador')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status.name')
                    ->label('Estado')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('version'),
                Tables\Columns\TextColumn::make('current_stage')->label('Etapa'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada el')
                    ->date('d/m/Y'),
            ])
            ->filters([
                //
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
            'index' => Pages\ListGames::route('/'),
            'create' => Pages\CreateGames::route('/create'),
            'edit' => Pages\EditGames::route('/{record}/edit'),
        ];
    }
}
