<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\RelationManagers\RolesRelationManager;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $label = '用户';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = '用户配置';

    protected static ?string $navigationLabel = '用户管理';

    protected static ?int $navigationSort = 1;

    // protected static bool $shouldRegisterNavigation = false;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
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
                        TextInput::make('phone')
                            ->label('手机号码')
                            ->unique(ignoreRecord: true)
                            ->tel()
                            ->telRegex('/1[3-9]\d{9}$/')
                            ->required()
                            ->length(11),
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
                            ->required()
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('is_admin')
                    ->boolean()
                    ->label('管理员')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('名称')
                    ->icon('heroicon-s-user')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('roles.desc')
                    ->label('角色')
                    ->icon('heroicon-s-identification')
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('手机')
                    ->searchable()
                    ->icon('heroicon-m-device-phone-mobile')
                    ->sortable(),
                TextColumn::make('email')
                    ->label('邮箱')
                    ->searchable()
                    ->icon('heroicon-m-envelope')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->description(fn(User $record): string => $record->created_at)
                    ->label('创建时间')
                    ->dateTime('Y-M-d H:i:s')
                    ->since()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('deleted_at')
                    ->label('删除时间')
                    ->toggleable(isToggledHiddenByDefault: true)
                    // ->dateTime('Y-M-d H:i:s')
                    //->formatStateUsing(fn(string|null $state): string => is_null($state) ? '-' : $state)
                    ->default('-')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('created_from_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('创建开始时间')
                            ->placeholder(fn($state): string => now()->subYear()->format('Y-m-d')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = '创建开始于 ' . Carbon::parse(
                                    $data['created_from']
                                )->toDateString();
                        }

                        return $indicators;
                    }),
                Tables\Filters\Filter::make('created_until_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_until')
                            ->label('创建结束时间')
                            ->placeholder(fn($state): string => now()->format('Y-m-d')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = '创建结束于 ' . Carbon::parse(
                                    $data['created_until']
                                )->toDateString();
                        }

                        return $indicators;
                    }),
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
