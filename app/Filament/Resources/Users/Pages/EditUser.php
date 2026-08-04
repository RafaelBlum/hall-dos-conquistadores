<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use App\Traits\DetectsIncompleteData;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected static ?string $breadcrumb = 'Edição de usuário';
    protected static ?string $title = "Editar";
    protected static ?string $navigationLabel = "Editar Usuário";

    use DetectsIncompleteData;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Deletar usuário')
                ->before(function (User $record) {
                    if ($record->avatar && $record->avatar !== 'default.jpg') {
                        $this->deleteFileFromStorage($record->avatar);
                    }
                    if ($record->channel?->brand && $record->channel->brand !== 'default-brand.png') {
                        $this->deleteFileFromStorage($record->channel->brand);
                    }
                })
                ->requiresConfirmation()
                ->modalHeading('Deletar Usuário adasdas'),
        ];
    }

    protected function beforeSave(): void
    {
        $user = $this->record;

        if (isset($this->data['avatar']) && is_array($this->data['avatar'])) {
            $newAvatar = reset($this->data['avatar']);
            $oldAvatar = $user->avatar;

            if ($oldAvatar !== $newAvatar && $oldAvatar !== 'default.jpg') {
                $this->deleteFileFromStorage($oldAvatar);
            }

        }

        $brandData = data_get($this->data, 'channel.brand');

        if ($brandData && is_array($brandData)) {
            $newBrand = reset($brandData);
            $oldBrand = $user->channel?->brand;

            if ($oldBrand !== $newBrand && $oldBrand !== 'default-brand.png') {
                $this->deleteFileFromStorage($oldBrand);
            }
        }

        if (!empty($this->data['password'])) {
            $this->getSavedNotification();
        }
    }



    private function deleteFileFromStorage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    protected function afterSave(): void
    {
        if (!empty($this->data['password'])) {
            $this->getSavedNotification();
            $this->redirect(route('filament.admin.auth.login'));
        }
    }


    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()->label('Salvar mudanças'),
            $this->getCancelFormAction()->label('Cancelar'),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return parent::getSavedNotification()
            ->title('Usuário editado com sucesso!')
            ->body($this->data['name'] . ' | ' . $this->data['email']);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
