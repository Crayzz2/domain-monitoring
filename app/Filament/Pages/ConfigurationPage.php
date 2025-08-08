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
                Forms\Components\MarkdownEditor::make('whatsapp_message')
                    ->label(__('WhatsApp Message')),
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
            $data['whatsapp_message'] ? $this->configuration->whatsapp_message = $data['whatsapp_message'] : null;
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
