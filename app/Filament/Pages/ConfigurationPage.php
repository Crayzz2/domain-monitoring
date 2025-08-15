<?php

namespace App\Filament\Pages;

use App\Models\Configuration;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Support\Exceptions\Halt;

class ConfigurationPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';

    protected static ?string $navigationLabel = 'Configurações';

    protected static ?string $title = 'Configurações';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['Super Admin', 'Configuração', 'Editar']);
    }


    public static function getNavigationGroup(): string
    {
        return __('Settings');
    }

    protected static string $view = 'filament.pages.configuration-page';

    public ?array $data = [];

    public ?Configuration $configuration = null;

    public function mount(): void
    {
        $this->configuration = Configuration::first();
        $this->form->fill($this->configuration->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('notification_receive_email')
                    ->label(__('Notification Recieve Email')),
                Forms\Components\Group::make([
                    Forms\Components\ColorPicker::make('default_color')
                        ->label(__('Default Color'))
                        ->default(Configuration::first()?->default_color),
                    Forms\Components\TextInput::make('domain_default_filter_days')
                        ->label(__('Domain Default Filter'))
                        ->numeric()
                        ->suffix(__('Days')),
                    Forms\Components\TextInput::make('hosting_default_filter_days')
                        ->label(__('Hosting Default Filter'))
                        ->numeric()
                        ->suffix(__('Days')),
                    Forms\Components\TextInput::make('summary_default_interval_days')
                        ->label(__('Summary Default Interval'))
                        ->numeric()
                        ->suffix(__('Days')),
                ])->columns(4)->columnSpanFull(),
                Forms\Components\Group::make([
                    Forms\Components\MarkdownEditor::make('domain_default_message')
                        ->label(__('Domain Default Message'))
                        ->hintActions([
                            Forms\Components\Actions\Action::make('Nome')
                                ->action(fn($set)=>$set('domain_default_message', $this->form->getState()['domain_default_message'] .= '{nome}')),
                            Forms\Components\Actions\Action::make('Data de expiração')
                                ->action(fn($set)=>$set('domain_default_message', $this->form->getState()['domain_default_message'] .= '{data de expiracão}')),
                            Forms\Components\Actions\Action::make('Domínio')
                                ->action(fn($set)=>$set('domain_default_message', $this->form->getState()['domain_default_message'] .= '{domínio}')),
                        ]),
                    Forms\Components\MarkdownEditor::make('hosting_default_message')
                        ->label(__('Hosting Default Message'))
                        ->hintActions([
                            Forms\Components\Actions\Action::make('Nome')
                                ->action(fn($set)=>$set('hosting_default_message', $this->form->getState()['hosting_default_message'] .= '{nome}')),
                            Forms\Components\Actions\Action::make('Data de expiração')
                                ->action(fn($set)=>$set('hosting_default_message', $this->form->getState()['hosting_default_message'] .= '{data de expiracão}')),
                        ]),
                ])->columns(2),

            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('save')
                ->label(__('Save'))
                ->submit('save')
        ];
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();
            $data['notification_receive_email'] ? $this->configuration->notification_receive_email = $data['notification_receive_email'] : null;
            $data['domain_default_message'] ? $this->configuration->domain_default_message = $data['domain_default_message'] : null;
            $data['hosting_default_message'] ? $this->configuration->hosting_default_message = $data['hosting_default_message'] : null;
            $data['default_color'] ? $this->configuration->default_color = $data['default_color'] : null;
            $data['domain_default_filter_days'] ? $this->configuration->domain_default_filter_days = $data['domain_default_filter_days'] : null;
            $data['hosting_default_filter_days'] ? $this->configuration->hosting_default_filter_days = $data['hosting_default_filter_days'] : null;
            $data['summary_default_interval_days'] ? $this->configuration->summary_default_interval_days = $data['summary_default_interval_days'] : null;
            $this->configuration->save();
            Notification::make('success')
                ->success()
                ->title('Configurações salvas com sucesso!')
                ->send();
        } catch (Halt $exception) {
            return;
        }
    }
}
