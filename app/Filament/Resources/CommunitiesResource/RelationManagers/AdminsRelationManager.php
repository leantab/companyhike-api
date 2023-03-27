<?php

namespace App\Filament\Resources\CommunitiesResource\RelationManagers;

use Filament\Forms;
use Filament\Pages\Actions\CreateAction;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdminsRelationManager extends RelationManager
{
    protected static string $relationship = 'admins';

    protected static ?string $recordTitleAttribute = 'name';

    protected bool $allowsDuplicates = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('full_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Hidden::make('role_id')->default('1')->required(),
                Forms\Components\Hidden::make('created_at')->default('2022-12-31 23:59:59')->required(),
                Forms\Components\Hidden::make('updated_at')->default('2022-12-31 23:59:59')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name'),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()->mutateFormDataUsing(function (array $data): array {
                    $data['role_id'] = 1;
                    return $data;
                })
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
            ]);
    }
}
