<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-white">
            {{ __('Fiók törlése') }}
        </h2>

        <p class="mt-1 text-sm text-white">
            {{ __('Miután a fiókodat törölted, az összes hozzá tartozó erőforrás és adat véglegesen törlésre kerül. A fiók törlése előtt kérlek töltsd le azokat az adatokat vagy információkat, amelyeket meg szeretnél őrizni.') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Fiók törlése') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-white">
                {{ __('Biztos, hogy törölni akarod a fiókod?') }}
            </h2>

            <p class="mt-1 text-sm text-white">
                {{ __('Miután a fiókodat törölted, az összes hozzá tartozó erőforrás és adat véglegesen törlésre kerül. Kérlek, add meg a jelszavad, hogy megerősítsd: véglegesen törölni szeretnéd a fiókodat.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Jelszó') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('Jelszó') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Mégse') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('Fiók törlése') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
