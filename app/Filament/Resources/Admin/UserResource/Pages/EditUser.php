<?php

namespace App\Filament\Resources\Admin\UserResource\Pages;

use App\Filament\Resources\Admin\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function fillForm(): void
    {
        $user = $this->getRecord();
        $user->load(['roles', 'karyawan']);

        /** @internal Read the DocBlock above the following method. */
        $this->fillFormWithDataAndCallHooks($user);
    }

    /**
     * @internal Never override or call this method. If you completely override `fillForm()`, copy the contents of this method into your override.
     *
     * @param  array<string, mixed>  $extraData
     */
    protected function fillFormWithDataAndCallHooks(Model $record, array $extraData = []): void
    {
        $this->callHook('beforeFill');

        $data = $this->mutateFormDataBeforeFill([
            ...$record->toArray(),
            ...$extraData,
        ]);

        $this->form->fill($data);

        $this->callHook('afterFill');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['role'] = $data['roles'][0]['name'] ?? null;
        $data['alamat'] = $data['karyawan']['alamat'] ?? null;
        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $alamat = null;
        if (isset($data['alamat'])) {
            $alamat = $data['alamat'];
            unset($data['alamat']);
        }

        $record->syncRoles([$data['role']]);

        $record->update($data);

        if ($alamat != null) {
            $record->karyawan()->update(['alamat' => $alamat]);
        }

        return $record;
    }
}
