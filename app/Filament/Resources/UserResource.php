<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Pages\Page;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Forms\Components\CheckboxList;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\UserResource\Pages;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\RelationManagers\RolesRelationManager;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $label = '用户';

    protected static ?string $navigationGroup = '用户配置';

    protected static ?string $navigationLabel = '用户管理';

    // protected static bool $shouldRegisterNavigation = false;

    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('名称')
                            ->required()
                            ->maxLength(255),
                        Toggle::make('is_admin')
                            ->label('管理员')
                            ->required(),
                        TextInput::make('email')
                            ->label('邮箱')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(255),
                        TextInput::make('password')
                            ->label('密码')
                            ->password()
                            ->maxLength(255)
                            ->dehydrateStateUsing(
                                static fn(null|string $state): null|string => filled($state) ? Hash::make(
                                    $state
                                ) : null,
                            )->required(static fn(Page $livewire): bool => $livewire instanceof CreateUser,
                            )->dehydrated(static fn(null|string $state): bool => filled($state),
                            )->label(
                                static fn(Page $livewire
                                ): string => ($livewire instanceof EditUser) ? '新密码' : '密码'
                            ),
                        CheckboxList::make('roles')
                            ->label('角色')
                            ->relationship('roles', 'desc')
                            ->columns(2)
                            ->helperText('只能选择一个角色!')
                            ->required()
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('is_admin')->boolean()->label('管理员')->sortable(),
                TextColumn::make('name')->label('名称')->sortable(),
                TextColumn::make('roles.desc')->label('角色')->sortable(),
                TextColumn::make('email')->label('邮箱')->icon('heroicon-s-mail')->sortable(),
                TextColumn::make('created_at')
                    ->description(fn(User $record): string => $record->created_at)
                    ->label('创建时间')
                    ->dateTime('Y-M-d H:i:s')
                    ->since()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('deleted_at')
                    ->label('删除时间')
                    // ->dateTime('Y-M-d H:i:s')
                    ->formatStateUsing(fn(string|null $state): string => is_null($state) ? '-' : $state)
                    ->sortable()
                    ->searchable(),

            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
                ForceDeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RolesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
