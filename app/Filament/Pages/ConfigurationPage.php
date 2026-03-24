<?php

namespace App\Filament\Pages;

use App\Models\Configuration;
use App\Services\Evolution\EvolutionService;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Log;

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
        if($this->configuration->instance_uuid){
            $status = EvolutionService::stateInstance();
            if($status->isSuccessful()){
                $this->configuration->update([
                    'instance_status' => $status->getData()->message
                ]);
            }
        }
        $this->configuration->default_color = auth()->user()->default_color;
        $this->form->fill($this->configuration->toArray());
    }

    public function getBase64()
    {
        $connection = EvolutionService::connectInstance();
        if(isset($connection['base64']))
        {
            return $connection['base64'];
        } else {
            $this->closeActionModal();
        }
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('create_instance')
                ->label(__('Create Instance'))
                ->visible(fn () => !$this->configuration->instance_uuid)
                ->action(function(){
                    EvolutionService::createInstance();
                    redirect('admin/configuration-page');
                }),
            Actions\Action::make('connect_instance')
                ->label(__('Connect Instance'))
                ->visible(fn () => !in_array($this->configuration->instance_status, ['open', null]))
                ->modalContent(view('filament.pages.evolution_connect'))
                ->modalWidth('2xl')
                ->modalCancelAction(false)
                ->modalSubmitAction(false)
                ->color('success')
                ->action(function(){
                    redirect('admin/configuration-page');
                }),
            Actions\Action::make('disconnect_instance')
                ->label(__('Disconnect Instance'))
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn () => $this->configuration->instance_status == 'open')
                ->action(function(){
                    EvolutionService::disconnectInstance();
                    redirect('admin/configuration-page');
                }),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make([
                    Forms\Components\FileUpload::make('company_logo')
                        ->label(__('Company Logo')),
                    Forms\Components\TextInput::make('company_name')
                        ->label(__('Company Name')),
                ])->columnSpanFull()->columns(2),
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

                Forms\Components\Toggle::make('send_alerts')
                    ->label(__('Send Hosting Alerts')),

                Forms\Components\Section::make([
                    Forms\Components\Group::make([
                        Forms\Components\MarkdownEditor::make('internal_alert_message_level_one')
                            ->label(__('Internal Alert Message Level One'))
                            ->hintActions([
                                Forms\Components\Actions\Action::make('Cliente')
                                    ->action(fn($set)=>$set('internal_alert_message_level_one', $this->form->getState()['internal_alert_message_level_one'] .= '{cliente}')),
                            ]),
                        Forms\Components\MarkdownEditor::make('internal_alert_message_level_two')
                            ->label(__('Internal Alert Message Level Two'))
                            ->hintActions([
                                Forms\Components\Actions\Action::make('Cliente')
                                    ->action(fn($set)=>$set('internal_alert_message_level_two', $this->form->getState()['internal_alert_message_level_two'] .= '{cliente}')),
                            ]),
                        Forms\Components\MarkdownEditor::make('internal_alert_message_level_three')
                            ->label(__('Internal Alert Message Level Three'))
                            ->hintActions([
                                Forms\Components\Actions\Action::make('Cliente')
                                    ->action(fn($set)=>$set('internal_alert_message_level_three', $this->form->getState()['internal_alert_message_level_three'] .= '{cliente}')),
                            ]),
                        Forms\Components\MarkdownEditor::make('internal_alert_message_level_four')
                            ->label(__('Internal Alert Message Level Four'))
                            ->hintActions([
                                Forms\Components\Actions\Action::make('Cliente')
                                    ->action(fn($set)=>$set('internal_alert_message_level_four', $this->form->getState()['internal_alert_message_level_four'] .= '{cliente}')),
                            ]),
                    ])->columns(1),
                    Forms\Components\Group::make([
                        Forms\Components\MarkdownEditor::make('client_alert_message_level_one')
                            ->label(__('Client Alert Message Level One'))
                            ->hintActions([
                                Forms\Components\Actions\Action::make('Nome')
                                    ->action(fn($set)=>$set('client_alert_message_level_one', $this->form->getState()['client_alert_message_level_one'] .= '{nome}')),
                                Forms\Components\Actions\Action::make('Data de expiração')
                                    ->action(fn($set)=>$set('client_alert_message_level_one', $this->form->getState()['client_alert_message_level_one'] .= '{data de expiracão}')),
                            ]),
                        Forms\Components\MarkdownEditor::make('client_alert_message_level_two')
                            ->label(__('Client Alert Message Level Two'))
                            ->hintActions([
                                Forms\Components\Actions\Action::make('Nome')
                                    ->action(fn($set)=>$set('client_alert_message_level_two', $this->form->getState()['client_alert_message_level_two'] .= '{nome}')),
                                Forms\Components\Actions\Action::make('Data de expiração')
                                    ->action(fn($set)=>$set('client_alert_message_level_two', $this->form->getState()['client_alert_message_level_two'] .= '{data de expiracão}')),
                            ]),
                        Forms\Components\MarkdownEditor::make('client_alert_message_level_three')
                            ->label(__('Client Alert Message Level Three'))
                            ->hintActions([
                                Forms\Components\Actions\Action::make('Nome')
                                    ->action(fn($set)=>$set('client_alert_message_level_three', $this->form->getState()['client_alert_message_level_three'] .= '{nome}')),
                                Forms\Components\Actions\Action::make('Data de expiração')
                                    ->action(fn($set)=>$set('client_alert_message_level_three', $this->form->getState()['client_alert_message_level_three'] .= '{data de expiracão}')),
                            ]),
                    ])->columns(1),
                ])->columns(2)->collapsible()->collapsed()->heading(__('Alerts'))

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
            $data['domain_default_filter_days'] ?? $data['domain_default_filter_days'] = 90;
            $data['hosting_default_filter_days'] ?? $data['hosting_default_filter_days'] = 90;
            $data['summary_default_interval_days'] ?? $data['summary_default_interval_days'] = 90;
            auth()->user()->default_color = $data['default_color'];
            unset($data['default_color']);
            $this->configuration->fill($data);
            auth()->user()->save();
            $this->configuration->save();
            Notification::make('success')
                ->success()
                ->title('Configurações salvas com sucesso!')
                ->send();
            redirect(route(ConfigurationPage::getRouteName()));
        } catch (Halt $exception) {
            return;
        }
    }
}
