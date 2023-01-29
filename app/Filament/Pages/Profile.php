<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Dashboard;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;

class Profile extends Page
{
    protected static ?string $title = '个人设置';

    protected static ?string $slug = 'profile';

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationGroup = '用户配置';

    protected static ?string $navigationLabel = '个人设置';

    protected static ?int $navigationSort = -1;

    protected static string $view = 'filament.pages.profile';

    public ?string $name = null;

    public ?string $current_password = null;

    public ?string $new_password = null;

    public ?string $new_password_confirmation = null;

    public function mount(): void
    {
        $this->form->fill([
            'name'  => auth()->user()->name,
            'email' => auth()->user()->email,
            'phone' => auth()->user()->phone,
        ]);
    }

    public function submit(): void
    {
        $this->form->getState();

        $state = array_filter([
            'name'     => $this->name,
            'password' => !is_null($this->new_password) ? Hash::make($this->new_password) : null,
        ]);

        $user = auth()->user();

        $user->update($state);

        if ($this->new_password) {
            $this->updateSessionPassword($user);
        }

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        $this->notify('success', '个人资料更新成功.');
    }

    protected function updateSessionPassword($user): void
    {
        request()->session()->put([
            'password_hash_' . auth()->getDefaultDriver() => $user->getAuthPassword(),
        ]);
    }

    public function getCancelButtonUrlProperty(): string
    {
        return Dashboard::getUrl();
    }

    protected function getBreadcrumbs(): array
    {
        return [
            url()->current() => '个人设置',
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('基本设置')
                ->schema([
                    TextInput::make('name')
                        ->label('名称')
                        ->required(),
                    TextInput::make('phone')
                        ->label('手机号码')
                        ->disabled(),
                    TextInput::make('email')
                        ->label('邮箱')
                        ->disabled(),
                ]),
            Section::make('更新密码')
                ->schema([
                    TextInput::make('current_password')
                        ->label('当前密码')
                        ->password()
                        ->rules(['required_with:new_password'])
                        ->currentPassword()
                        ->autocomplete('off')
                        ->columnSpan(1),
                    Grid::make()
                        ->schema([
                            TextInput::make('new_password')
                                ->label('新密码')
                                ->password()
                                ->rules(['confirmed'])
                                ->autocomplete(),
                            TextInput::make('new_password_confirmation')
                                ->label('确认密码')
                                ->password()
                                ->rules([
                                    'required_with:new_password',
                                ])
                                ->autocomplete(),
                        ]),
                ]),
        ];
    }
}
