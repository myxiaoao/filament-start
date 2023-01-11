<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PermissionResource\Pages;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $label = '权限';

    protected static ?string $navigationGroup = '用户配置';

    protected static ?string $navigationLabel = '权限管理';

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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePermissions::route('/'),
        ];
    }
}
