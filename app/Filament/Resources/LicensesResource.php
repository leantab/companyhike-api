<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LicensesResource\Pages;
use App\Filament\Resources\LicensesResource\RelationManagers;
use App\Models\Community;
use App\Models\License;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LicensesResource extends Resource
{
    protected static ?string $model = License::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-euro';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('community_id')->label('Comunidad')
                    ->options(Community::all()->pluck('name', 'id'))
                    ->searchable(),
                Select::make('user_id')->label('Usuario asociado')
                    ->options(User::all()->pluck('name', 'id'))
                    ->searchable(),
                Forms\Components\DatePicker::make('start_date')
                    ->label('Fecha de inicio')
                    ->minDate(now())
                    ->displayFormat('d M Y'),
                Forms\Components\DatePicker::make('end_date')
                    ->label('Fecha de Vencimiento')
                    ->minDate(now())
                    ->displayFormat('d M Y'),
                Forms\Components\TextInput::make('max_users')
                    ->label('Cantidad de usuarios')
                    ->numeric(),
                Forms\Components\TextInput::make('max_games')
                    ->label('Cantidad de partidas')
                    ->numeric(),
                Forms\Components\Toggle::make('is_active')->label('Activo')->inline(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('community.name')->label('Comunidad'),
                Tables\Columns\TextColumn::make('user.full_name')->label('Usuario asociado'),
                Tables\Columns\TextColumn::make('start_date')->date('d M Y'),
                Tables\Columns\TextColumn::make('end_date')->date('d M Y'),
                Tables\Columns\TextColumn::make('max_users'),
                Tables\Columns\TextColumn::make('max_games'),
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListLicenses::route('/'),
            'create' => Pages\CreateLicenses::route('/create'),
            'edit' => Pages\EditLicenses::route('/{record}/edit'),
        ];
    }
}
