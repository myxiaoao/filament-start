<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Spatie\Permission\Models\Role;
use Filament\Forms\Components\Card;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\RoleResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RoleResource\RelationManagers;
use App\Filament\Resources\RoleResource\RelationManagers\PermissionsRelationManager;
use Filament\Forms\Components\Select;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-s-cog';

    protected static ?string $label = '角色';

    protected static ?string $navigationGroup = '用户配置';

    protected static ?string $navigationLabel = '角色管理';

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        TextInput::make('desc')
                            ->label('描述')
                            ->unique(ignoreRecord: true)
                            ->required(),
                        TextInput::make('name')
                            ->label('标识')
                            ->unique(ignoreRecord: true)
                            ->required(),
                        Select::make('permissions')
                            ->label('权限')
                            ->multiple()
                            ->relationship('permissions', 'desc')
                            ->preload()
                            ->required()
                    ])
            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('desc')->label('描述')->sortable()->searchable(),
                TextColumn::make('name')->label('标识')->sortable()->searchable(),
                TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-M-d H:i:s')
                    ->since()
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array {
        return [
            PermissionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
